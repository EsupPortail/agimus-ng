#!/usr/bin/env python3
# coding: utf-8

import re, sys, pymysql, datetime

DBHOST = "moodledb.univ.fr"
# Attention à définir une base spécifiquement dédiée (ajoutée préalablement dans la conf moodle) et ne pas supprimer les logs de prod de moodle
DB = "moodle_prod_log"
USER = "moodle_prod_log"
PASS = "MDPspecifique"

if len(sys.argv) != 2:
    print("""
Ce sript supprime dans la table des logs les entrées plus vieilles que le timestamp donné en argument (ex : 1458298216)
""")
    sys.exit(1)

timestamp = sys.argv[1]

#Check qu'il y a bien 10 chiffres
if re.search('\d{10}',  timestamp):
    ts = datetime.datetime.fromtimestamp(int(timestamp))
    tsHR = ts.strftime('%d/%m/%Y %H:%M:%S')
    #Connexion DB
    db = pymysql.connect(DBHOST, USER, PASS, DB)
    cursor = db.cursor()
    try:
        req = f'DELETE FROM `logstore_standard_log` WHERE timecreated <= {timestamp}'
        cursor.execute(req)
        db.commit()
        print (f'Les {cursor.rowcount} lignes antérieures à {tsHR} ont été supprimées')
    except:
        print(f'**** ERREUR ***** Les lignes antérieures à {tsHR} n\'ont pas pu être supprimées')
        db.rollback()
    finally:
        cursor.close()
        db.close()

else:
    print("""
Il faut un timestamp en argument
Ce sript supprime dans la table des logs les entrées plus vieilles que le timestamp donné en argument (ex : 1458298216)
""")
    sys.exit(1)
