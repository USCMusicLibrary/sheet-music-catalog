Connection = pymysql.connect(host='localhost',
                             user='root',
                             password='',
                             db='sheetmusic',
                             charset='utf8',
                             cursorclass=pymysql.cursors.DictCursor)