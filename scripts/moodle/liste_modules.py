#!/usr/bin/python
# coding: utf-8

import MySQLdb, datetime, config, sys, re, os.path

# fichier résultat
fichier = "/chemin/pour/les/resultats/modules_" + datetime.datetime.now().strftime('%Y%m%d') + ".txt"
file = open(fichier, 'w')

# Paramétrage de l'accès à la base
dbprod = dict(
        host = 'moodledb.univ.fr',
        user = 'moodle_prod',
        mdp = 'MDPmoodle',
        base = 'moodle_prod',
)

# Chemin vers le dossier dataroot pour aller chercher les traductions
moodle_dataroot_path = "/path/vers/moodle-data/data"

#Connexion DB
db = MySQLdb.connect(dbprod['host'], dbprod['user'], dbprod['mdp'], dbprod['base'])
cursor = db.cursor()


#On fait un hash par catégorie de module et son intitulé
# tab_cat_mod[1]=assign
cursor.execute("SELECT name FROM mdl_modules")
rows_cat_mod = cursor.fetchall()
tab_cat_mod = dict()
for row in rows_cat_mod:
	tab_cat_mod[row[0]] = row[0]
	#Boucle qui va aller chercher les traductions
	fic_trad = moodle_dataroot_path + "/lang/fr/" +  row[0] + ".php"
	if os.path.isfile(fic_trad):
		fic_trad = open(fic_trad)
		for line in fic_trad:
			#print line
			trad = re.findall(".*modulename'.*= '(.*)';", line)
			if len(trad) > 0 :
				#print trad
				tab_cat_mod[row[0]] = trad[0]

for key,value in tab_cat_mod.items():
	file.write('"mod_'+key+'": "'+value+'"\n')


#Fermeture DB
db.close()

#Fermeture fichier
file.close()
