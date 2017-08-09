#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
    Sheet Music Catalog
    Copyright (C) 2016-2017 - University of South Carolina

    License: GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
"""
"""This module performs automated updates of authorized access points
following vocabularies supported by services at id.loc.gov by checking
heading recorded for a given id.loc.gov URI recorded in a local
database table against the current value at id.loc.gov, then recording
updated headings, deprecation information, or an error message for the
URI to columns in the local database table.

The function "check_all" checks all headings with URIs for a change
in form of heading. This should be run infrequently so as not to
tax LC's system

The function "update" checks the id.loc.gov update rss feed for a
given vocabulary for recent changes. This should be run frequently
(no less than once per week; daily or nightly if possible)

Either may be applied via cmd line:
authoritycheck.py [desired function name] [specified vocabulary]

If check_all encounters an error and fails prior to checking all
headings, the last uri will be recorded to a file:
check_all' + [vocabulary name] + '_Error.txt
To pick up where check_all left off, call check_uri with the uri:
authoritycheck.py check_all [specified vocabulary] [last uri]

Database connection information should be recorded in a dbconfig.py
file located in the same directory as the authoritycheck.py file.

Supported vocabularies may be added in voc_dict following the pattern:
vocabulary_name:[table_to_update, column_holding_headings]
where the vocabulary_name is the version appearing in the uris at
id.loc.gov (e.g., "names" from "http://id.loc.gov/authorities/names/")
"""

import pymysql, feedparser, socket, re, os
from http import client
from datetime import datetime
from time import sleep
from urllib.request import urlopen
from sys import argv

voc_dict = {"names":["names", "name"],
            "subjects":["subject_headings", "subject_heading"]
            }

socket.setdefaulttimeout(10)
#feedparser will hang without this

#we do this so that we have the db configuration in a separate file 
#TODO: test
from past.builtins import execfile
execfile('dbconfig.py')


def robotscheck():
    """Function sets request delay to value from robots.txt"""

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
    identifies headings in our db that need updating; adds update
    values to our local db; these will be applied pending approval.
    """

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
                    sql = "UPDATE `" + tableName + "` SET `" + labelColumn + "Update` = %s WHERE (`uri` = %s) AND (`" + labelColumn + "` != %s)"
                    #Sets nameUpdate to current name from id.loc if uri matches & names don't
                    cursor.execute(sql, (name, uri, name))

            with open(lastupdatefile, "w", encoding="utf-8") as f:
                #records uri, name, updatedate, feed page, and message from last update processed
                f.write("\t".join(updates[-1]))

        Connection.close()
        print("Updates complete")

    else:
        print("Vocabulary not found. Supported vocabularies: '" + "' or '".join(voc_dict.keys()) + "'")

def check_all(vocabulary, pickup_uri=None):
    """Checks all headings for which we have an id.loc.gov uri against
    the api to get the current authorizedLabel.
    Records update value to be applied pending approval.

    In case the script fails before the complete list of URIs has been
    checked, the URI last processed is recorded via on_failure, with a
    message indicating the nature of the error encountered.
    """

    date = datetime.date(datetime.now())
    today_date = str(date)
    def on_failure(uri, problem):
        """If check_all fails, writes the URI that was being checked to a file"""
        #TODO: add ability to pick up where we left off, based on the uri last checked
        errfile = 'check_all' + vocabulary + '_Error.txt'
        with open(errfile, 'w', encoding = 'utf-8') as err:
            err.write(uri + '\t' + problem)

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

            if pickup_uri is not None:
                #test uri: http://id.loc.gov/authorities/names/no2012028627
                print("Looking for pickup uri: " + pickup_uri + "...")
                for i, r in enumerate(results):
                    url = r['uri']
                    if url == pickup_uri:
                        print("found")
                        results = results[i:]
                        break
    
            for r in results:
                url = r['uri']
                if url == "" or url == None:
                    pass

                else:
                    print(url)
                    #lc doesn't care as long as one exists, but we should still come up with a better user-agent
                    m = re.match(r'http://(.*?)(/.*)', url)
                    conn = client.HTTPConnection(m.group(1))
                    headers = {'User-Agent': 'pythontest'}
                    try:
                        conn.request( "HEAD", m.group(2), headers=headers)
                    except socket.gaierror:
                        print("Error while attempting to connect to host " + m.group(1))
                        on_failure(url, "DNS Error (gaierror) while attempting to reach id.loc.gov")
                        raise

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
                            on_failure(url, "Timeout error from id.loc.gov")
                            raise

                    except Exception as e:
                        print("We encountered an error while requesting header data from id")
                        print("Writing uri for last record checked to vocab's error file")
                        on_failure(url, "Unspecified issue retrieving data from id.loc.gov")
                        raise e

                    else:
                        name = res.getheader('X-PrefLabel')
                        
                        try:
                            name = name.encode('ISO-8859-1').decode('utf-8')
                        except AttributeError:
                            print(res.getheaders())
                            print(res.status)
                            print(res.reason)
                            if res.getheader('X-Uri', default = None) != None:
                                sql_update = "UPDATE `" + tableName + "` SET `problem_note` = '::ERROR:: uri has been deprecated at id.loc.gov; check for replacement' WHERE `uri` = %s"
                            else:
                                sql_update = "UPDATE `" + tableName + "` SET `problem_note` = '::ERROR:: id.loc.gov service failed to retrieve record--check URI for typo' WHERE `uri` = %s"
                            cursor.execute(sql_update, url)
                        else:
                            
                            if name == None or name == '':
                                #for deleted records
                                sql_update = "UPDATE `" + tableName + "` SET `problem_note` ='::ERROR:: No name found' WHERE `uri` = %s"
                                cursor.execute(sql_update, url)
    
                            else:
                                #Otherwise, set name field to X-PrefLabel value
                                sql_update = "UPDATE `" + tableName + "` SET `"+labelColumn+"Update` = %s WHERE `uri` = %s AND " + labelColumn + " != %s"
                                cursor.execute(sql_update, (name, url, name))
                                                          
                        sleep(int(naptime))


            sql = "SELECT name, uri, nameUpdate, problem_note FROM " + tableName + " WHERE nameUpdate IS NOT NULL OR problem_note IS NOT NULL"
            cursor.execute(sql)
            results = cursor.fetchall()
            print(len(results))

        with open("last_full_update_" + vocabulary + ".txt", "w", encoding='utf-8') as f:
            f.write(today_date)
            #on completion, delete stale update data:
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
    if len(argv) in (3, 4):
        function = argv[1]
        vocabulary = argv[2]
        if function == "update":
            update(vocabulary)
        elif function == "check_all":
            if len(argv) == 4:
                url = argv[3]
                check_all(vocabulary, url)
            else:
                check_all(vocabulary)
    else:
        print("Requires a function name (i.e. 'check_all' or 'update') and vocabulary. May include optional 3rd parameter for uri if using check_all. Supported vocabularies: '" + "' or '".join(voc_dict.keys()) + "'")
