#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""This module performs automated updates of LCNAF and LCSH headings
against database tables containing authorizedLabels with URIs.

New supported vocabularies should be added to voc_dict following the pattern:
vocabulary_name:[table_to_update, column_holding_authorizedLabel]
where the vocabulary_name is the version appearing in the uris at id.loc.gov
 (e.g., "names" from "http://id.loc.gov/authorities/names/").

---Database stuff---
Local info (db name, a username & pwd for the script, etc.)
need to be added to "Connection"

Make sure voc_dict reflects the tables and columns in your db (see note
about new supported vocabularies above, too)

The name of the column that will contain the new form of label for updated records
is expected to be the same as your column_holding_authorizedLabel + "Update"
(e.g., if you have a table "contributors" where authorizedLabel values would
be recorded in a column called "name", the update column would be "nameUpdate")

"""

import pymysql, feedparser, socket, re, os
from http import client
from datetime import datetime
from time import sleep
from urllib.request import urlopen
from sys import argv

voc_dict = {"names":["test", "name"],
            "subjects":["tbl_subject", "subject"]}

socket.setdefaulttimeout(10)
#feedparser will hang without this

Connection = pymysql.connect(host='localhost',
                             user='pybot',
                             password='',
                             db='csv_db',
                             charset='utf8',
                             cursorclass=pymysql.cursors.DictCursor)

def robotscheck():
    """Function sets request delay to value from robots.txt"""
    #Hopefully LC doesn't change any of the other requirements drastically,
    #but we may want to add a check for updates to this file in case
    #they shake things up later on

    try:
        robots = urlopen('http://id.loc.gov/robots.txt')

    except socket.timeout:
        print("LC's server is busy. Re-running script in 1 hour")
        sleep(3600)
        robotscheck()

    except Exception as e:
        print("Error accessing robots.txt:")
        raise e

    else:
        robots_text = robots.read().decode('utf-8')
        robo_info = {}
        for r in re.split(r'\n', robots_text):
            s = re.split(": ", r)
            if len(s) == 2:
                robo_info[s[0]] = s[1]

        return int(robo_info['Crawl-delay'])

def update(vocabulary):
    """Checks the update feed for a specified vocabulary at id.loc.gov,
identifies headings in our db that need updating; adds update values
to our local db; these will be applied later after approval."""

    date = datetime.date(datetime.now())
    today_date = str(date)
    updates = []
    lastupdatefile = "fp_last_" + vocabulary + ".txt"

    def on_error(date, url):
        """records last entry successfully parsed before updatecheck failure"""
        with open("lc_update_" + vocabulary + "Error.txt", "a+", encoding="utf-8") as f:
            f.write(str(datetime.now()) + "\t" + url + "\t".join(updates[-1]) + "\n")

    def feedread(url, feed_date):
        """requests & parses atom feeds; checks for needed changes in our db"""

        openurl = urlopen(url)
        status = openurl.getcode()

        #moves tombstones in with the other entries, adds bogus "deleted" element
        #Couldn't find a better way to get tombstones to be processed in line with <entry>
        #<deleted> added in case LC starts using <content> for <entry> too

        u = openurl.read().decode('utf-8')
        u = re.sub(r'<at:deleted-entry.*?>', '<entry><deleted>record deleted</deleted>', u)
        u = u.replace('</at:deleted-entry>', '</entry>')

        fp = feedparser.parse(u)

        while True:

            #Check for malformed atom feed:
            bozo = fp.bozo
            if bozo == 1:
                on_error(feed_date, url)
                raise ValueError("Bozo error tripped: This isn't a valid atom feed")

            else:

                if status == 200:

                    if last_update >= datetime.strptime(fp.entries[0]['updated'], "%Y-%m-%dT%H:%M:%S-04:00").date():
                        #No new updates since last time
                        break

                    else:

                        for ff in fp.entries:
                            form_fdate = datetime.strptime(ff['updated'], "%Y-%m-%dT%H:%M:%S-04:00")
                            fdate = form_fdate.date()
                            try:
                                #contains record deletion message from tombstones:
                                ff["content"]

                            except KeyError:
                                link = ff['link'] #uri
                                name = ff['title'] #current form of name
                                updatedate = ff['updated'] #updatedate
                                message = "" #no <content> message

                            else:
                                try:
                                    ff['deleted']

                                except KeyError:
                                    print("LC is using <content> in non-tombstone entries")

                                else:
                                    link = ff["link"] #uri
                                    name = ff["title"] #name
                                    updatedate = ff['updated'] #updatedate
                                    message = ff["content"][0]["value"] #deletion text & datetime

                            if fdate > last_update:
                                updates.append([link, name, updatedate, url, message])

                            elif fdate == last_update:
                                print("Checking for any extra entries from the last_date from our previous session...")

                                try:
                                    f = open(lastupdatefile, "r", encoding="utf-8")

                                except FileNotFoundError:
                                    #this error indicates this is the first
                                    #time this function has been called, or
                                    #the first time since check_all() last ran

                                    print(lastupdatefile + " not set yet."
                                          + " Data from cache should be complete.")

                                    break

                                else:
                                    #reads the info from the last entry we processed
                                    #continues with this day's updates until it finds that entry
                                    print("Comparing other values with " + lastupdatefile)
                                    fp_last = re.split('\t', f.read())
                                    last_lnd = fp_last[0] + "\t" + fp_last[1] + "\t" + fp_last[2]
                                    ff_lnd = ff["link"] + "\t" + ff["title"] + form_fdate

                                    if last_lnd == ff_lnd:
                                        break

                                    else:
                                        updates.append(link, name, updatedate, url, message)

                                f.close()

                    nextLink = fp.feed['links'][1]['href']
                    url = nextLink
                    lastdate = datetime.date(form_fdate)
                    return url, lastdate

                else:
                    #just in case
                    onError(feed_date, url)
            break

    if vocabulary in voc_dict:
        tableName = voc_dict[vocabulary][0]
        labelColumn = voc_dict[vocabulary][1]

        naptime = int(robotscheck())

        try:
            f = open(lastupdatefile, "r", encoding="utf-8")

        except FileNotFoundError:
            print("no last date recorded; starting with today")
            start_date = datetime.strptime(today_date, "%Y-%m-%d").date()

        else:
            text = f.read()

            if text == "":
                lastupdatefile + " found, but empty"
                start_date = datetime.strptime(today_date, "%Y-%m-%d").date()

            else:
                txt_split = re.split("\t", text)
                txt_date = txt_split[2]
                print("Using date from " + lastupdatefile + ": " + str(txt_date))
                start_date = datetime.strptime(txt_date, "%Y-%m-%dT00:00:00-04:00").date()

            f.close()

        last_update = start_date
        url = 'http://id.loc.gov/authorities/' + vocabulary + '/feed/1'

        print("Checking pages until we reach " + str(last_update))

        while date > last_update:
            print(url + "\n",
                  "Date from feed page: " + str(date))

            try:
                url, date = feedread(url, date)

            except TypeError:
                break
                #In case url, date not returned, begin processing updates

            else:
                sleep(naptime)

        if len(updates) == 0:
            print("no updates needed")

        else:
            print("Updates acquired. Preparing list of unique changes...")

            updates.reverse()
            updateUniques = {}

            for uri, name, date, feed, message in updates:

                if message == "":
                    updateUniques[uri] = name
                    #if <content> was not present in the entry, we record {uri:name}

                else:
                    updateUniques[uri] = message
                    #if <content> did exist, we record {uri:content} to get the deletion message

            print("Processing SQL update...")
            for uri, name in updateUniques.items():

                with Connection.cursor() as cursor:
                    sql = "UPDATE `" + tableName + "` SET `" + labelColumn + "Update` =%s WHERE (`uri` = %s) AND (`" + labelColumn + "` != %s)"
                    #Sets nameUpdate to current name from id.loc if uri matches & names don't
                    cursor.execute(sql, (name, uri, name))
                    #Do we need to commit?

            with open(lastupdatefile, "w", encoding="utf-8") as f:
                #records uri, name, updatedate, feed page, and message from last update processed
                f.write("\t".join(updates[-1]))

        Connection.close()
        print("Updates complete")

    else:
        print("Vocabulary not found. Supported vocabularies: '" + "' or '".join(voc_dict.keys()) + "'")

def check_all(vocabulary):
    """Checks all headings for which we have an id.loc.gov uri against the api
in order to get the current authorizedLabel.
Records update value to be applied pending approval"""

    date = datetime.date(datetime.now())
    today_date = str(date)
    def on_failure(uri):
        """If check_all fails, writes the URI that was being checked to a file.
Eventually, we may use this to add the ability to pick up from where we left off."""
        #might be good to add ability to pick up where we left off in case of error
        errfile = 'check_all' + vocabulary + 'Error.txt'
        with open(errfile, 'w', encoding = 'utf-8') as err:
            err.write(uri)

    if vocabulary in voc_dict:

        tableName = voc_dict[vocabulary][0]
        labelColumn = voc_dict[vocabulary][1]

        naptime = robotscheck()
        name_dict = {}

        with Connection.cursor() as cursor:

            sql_select = "SELECT `uri` FROM `" + tableName + "`"

            try:
                cursor.execute(sql_select)
                results = cursor.fetchall()

            except Exception as e:
                print("There was a problem retrieving uris from our database")
                raise e

            for r in results:
                url = r['uri']
                print(url)

                if url == "":
                    pass

                else:
                    #lc doesn't care as long as one exists, but we should still come up with a better user-agent
                    m = re.match(r'http://(.*?)(/.*)', url)
                    conn = client.HTTPConnection(m.group(1))
                    headers = {'User-Agent': 'pythontest'}
                    conn.request( "HEAD", m.group(2), headers=headers)

                    try:
                        res = conn.getresponse()

                    except socket.timeout:
                        print("request timed out; will try again in 5 minutes")
                        sleep(300)

                        try:
                            res = conn.getresponse()

                        except socket.timeout:
                            print("LC is busy right now; Try re-running script later.")
                            print("Writing uri for last record checked to vocab's error file")
                            on_failure(url)
                            raise

                    except Exception as e:
                        print("We encountered an error while requesting header data from id.loc.gov")
                        print("Writing uri for last record checked to vocab's error file")
                        on_failure(url)
                        raise e

                    else:
                        name = res.getheader('X-PrefLabel')
                        name = name.encode('ISO-8859-1').decode('utf-8')
                        name_dict[url] = name
                        sleep(int(naptime))

            with open("last_" + vocabulary + "_check_results.txt", "w", encoding='utf-8') as t:
                for u, n in name_dict.items():
                    if n == None:
                        #for deleted records, set nameUpdate to "This item was deleted"; we can use this to filter deletions
                        sql_update = "UPDATE `" + tableName + "` SET `" + labelColumn + "Update` ='record deleted' WHERE `uri` = %s"
                        cursor.execute(sql_update, u)
                        t.write(u + "\t" + "This item was deleted" + "\n")
                    else:
                        #Otherwise, set name field to X-PrefLabel value
                        sql_update = "UPDATE `" + tableName + "` SET `" + labelColumn + "` =%s WHERE `uri` = %s"
                        cursor.execute(sql_update, (n, u))
                        t.write(u + "\t" + n + "\n")

        with open("last_full_update_" + vocabulary + ".txt", "w", encoding='utf-8') as f:
            f.write(today_date)
            #on completion, update file date & delete
            try:
                f = open("fp_last_" + vocabulary + ".txt", "r", encoding="utf-8")

            except FileNotFoundError:
                pass

            else:
                filename = "fp_last_" + vocabulary + ".txt"
                os.remove(os.remove(filename))

    else:
        print("Vocabulary not supported, or misspelled--check spelling used in id.loc.gov URIs")

if __name__ == "__main__":
    print(len(argv), argv)
    if len(argv) == 3:
        function = argv[1]
        vocabulary = argv[2]
        print("function: " + function, "vocab: " + vocabulary)
        if function == "update":
            update(vocabulary)
        elif function == "check_all":
            check_all(vocabulary)
    else:
        print("Requires a function name and vocabulary. Supported vocabularies: '" + "' or '".join(voc_dict.keys()) + "'")
