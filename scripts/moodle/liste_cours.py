#!/usr/bin/python
# coding: utf-8

import MySQLdb, datetime, sys, re, os.path

# fichier résultat
fichier_resultat = "/chemin/pour/les/resultats/rapport_" + datetime.datetime.now().strftime('%Y%m%d') + ".txt"
file = open(fichier_resultat, 'w')

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
cursor.execute("SELECT id,name FROM mdl_modules") 
rows_cat_mod = cursor.fetchall()
tab_cat_mod = dict()
for row in rows_cat_mod:
	tab_cat_mod[row[0]] = row[1]
	#Boucle qui va aller chercher les traductions (pas indispensable)
	fic_trad = moodle_dataroot_path + "/lang/fr/" +  row[1] + ".php"
	if os.path.isfile(fic_trad):
		fic_trad = open(fic_trad)
		for line in fic_trad:
			#print line
			trad = re.findall(".*modulename'.*= '(.*)';", line)
			if len(trad) > 0 :
				#print trad
				tab_cat_mod[row[0]] = trad[0].decode('utf-8').encode('iso-8859-1')

#On fait une liste (tableau) cours_actifs (cours visible avec au moins 10 actions)
# cours_actifs=[43,344,543]
cursor.execute("SELECT mdl_course.id, COUNT(mdl_logstore_standard_log.id) AS HITS FROM mdl_course , mdl_logstore_standard_log WHERE mdl_logstore_standard_log.courseid = mdl_course.id AND mdl_logstore_standard_log.origin= 'web' AND mdl_course.visible = 1 GROUP BY mdl_course.id HAVING  HITS > 10 AND mdl_course.id != 1")
rows_cours_actifs = cursor.fetchall()
cours_actifs=[]
for row in rows_cours_actifs:
	cours_actifs.append(row[0])

##On fait un hash par id du cours, tous les modules
## tab_act[4]=123=forum|456=url|789=forum
cursor.execute("SELECT course,id,module FROM mdl_course_modules")
rows_cours_act = cursor.fetchall()
tab_act = dict()
for row in rows_cours_act:
	#si mon dictionnaire a déjà une entrée correspondant à la structure j'ajoute le module, sinon je le créé avec le nouveau module
	if row[0] in tab_act:
                tab_act[row[0]] += "|" + str(row[1]) + "=" + str(tab_cat_mod[row[2]])
	else:
                tab_act[row[0]] = str(row[1]) + "=" + str(tab_cat_mod[row[2]])

#On fait un hash par id du cours, tous les enseignants
# tab_ens[4]=seclier5|vaillard5|marron5
# tab_ens[5]=seclier5 
cursor.execute("SELECT c.id,u.username FROM mdl_course AS c JOIN mdl_context AS ctx ON c.id = ctx.instanceid AND ctx.contextlevel = 50  JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id JOIN mdl_user AS u ON u.id = ra.userid JOIN mdl_course_categories AS cc ON cc.id = c.category WHERE u.deleted=0 AND ra.roleid = 3")
rows_cours_ens = cursor.fetchall()
tab_ens = dict()
for row in rows_cours_ens:
	#si mon dictionnaire a déjà une entrée correspondant à la structure j'ajoute l'enseignant, sinon je la créé avec le nouvel enseignant
	if row[0] in tab_ens:
                tab_ens[row[0]] += "|" + str(row[1])
	else:
                tab_ens[row[0]] = row[1]

#On fait un hash par catégorie et son parent
# tab_cat[39]=0 -- 0: collegium
# tab_cat[345]=39
cursor.execute("SELECT id,parent,name FROM mdl_course_categories") 
rows_cat = cursor.fetchall()
tab_cat = dict()
for row in rows_cat:
	tab_cat[row[0]] = row[1]

#On fait un hash par nombre de modules dans chaque cours
# tab_mod[4][17]=5 -- Cours 4, 5 activités de type 17
cursor.execute("SELECT cm.course,cm.module,count(cm.instance) FROM mdl_course_modules AS cm,mdl_course AS c WHERE cm.course = c.id AND c.visible = 1 GROUP BY cm.course,cm.module;")
rows_mod = cursor.fetchall()
tab_mod = dict()
#for row in rows_mod:
#        tab_mod[row[0]][row[1]] = row[2]
for row in rows_mod:
	#si mon dictionnaire a déjà une entrée correspondant à la structure j'ajoute l'enseignant, sinon je la créé avec le nouvel enseignant
	if row[0] in tab_mod:
                tab_mod[row[0]] += "|" + str(tab_cat_mod[row[1]]) + "=" + str(row[2])
	else:
                tab_mod[row[0]] = str(tab_cat_mod[row[1]]) + "=" + str(row[2])


#Liste des cours (visibles)
cursor.execute("SELECT mdl_course.id,mdl_course.fullname,mdl_course.category FROM mdl_course, mdl_course_categories WHERE mdl_course.category = mdl_course_categories.id AND mdl_course.visible = 1")
rows = cursor.fetchall()

for row in rows:
	id_cours=row[0]
	nom_cours=row[1]
	id_cat=row[2]

	#Vérifie que le cours est dans la liste des actifs
	if id_cours in cours_actifs:
		actif="1"
	else:
		actif="0"

	#Vérifie que la clé est bien dans le tab_ens (tab_ens[4] existe mais tab_ens[10] n'existe pas)
	if id_cours in tab_ens:
		enseignants = tab_ens[id_cours]
	else:
		enseignants = ""

	#Vérifie que la clé est bien dans le tab_mod (tab_mod[4] existe mais tab_mod[10] n'existe pas)
	if id_cours in tab_mod:
		modules = tab_mod[id_cours]
	else:
		modules = ""

	#Vérifie que la clé est bien dans le tab_act (tab_act[4] existe mais tab_act[10] n'existe pas)
	if id_cours in tab_act:
		activites = tab_act[id_cours]
	else:
		activites = ""

	#Regarde dans quelle categorie de niveau1 se trouve le cours
	cat_niv1=id_cat
	cat_niv2=id_cat
	#Je remonte la catégorie jusqu'à retrouver la catégorie où le parent est 0
	while tab_cat[cat_niv1]!=0 :
		#On garde la valeur d'avant pour récupérer le niveau 2 une fois qu'on a trouvé le niveau 1
		cat_niv2=cat_niv1
		cat_niv1=tab_cat[cat_niv1]

	#Affiche la ligne avec toutes les infos
	file.write("[cours:" + str(id_cours) + "];[nom_cours:" + str(nom_cours) + "];[cat:" + str(id_cat) + "];[enseignants:" + enseignants + "];[activites:" +  activites + "];[type_activites:" +  modules + "];[actif:" + actif + "];[niv2:" + str(cat_niv2)  + "];[niv1:" + str(cat_niv1) + "]\n")


#Fermeture DB
db.close()

#Fermeture fichier
file.close()
