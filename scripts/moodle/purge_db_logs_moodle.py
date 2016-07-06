#!/usr/bin/python
# coding: utf-8

import re, sys, MySQLdb

DBHOST = "moodledb.univ.fr"
DB = "moodle_prod_log"
USER = "moodle_prod_log"
PASS = "MDPSpecifique"

if len(sys.argv) != 2:
    print("""
Ce sript supprime les entrée plus vieilles que le timestamp donné en argument (ex : 1458298216)
""")
    sys.exit(1)

timestamp = sys.argv[1]

#Check qu'il y a bien 10 chiffres
if re.search('\d{10}',  timestamp):
    try:
        #Connexion DB
        db = MySQLdb.connect(DBHOST, USER, PASS, DB)
        cursor = db.cursor()

        req = "DELETE FROM `logstore_standard_log` WHERE timecreated <= %s"
        cursor.execute(req, (timestamp))
        db.commit()

        print "Les lignes ont été supprimées"
    finally:
        cursor.close()
        db.close()

else:
    print "Il faut un timestamp en argument"

