#!/home/agimus/bin/python
# coding: utf8

# preparation_fichiers.py

import argparse, sys, json, os, re
from urllib import request, parse, error

##########################
# Paramétrage du script
#

# Liste des conversions de noms d'attributs
#   Ajoutez ici les noms spécifiques à votre établissement
#	que ce soit des noms d'attribut ou d'index
#	Attention, les noms cibles doivent être en minuscule
conversion_table = {
	'Grouped': 'grouped',
	'HR': '-hr',
	'-label': '-hr',
	'Readable': '-hr',
	'-readable': '-hr',
	'N1': '-n1',
	'N2': '-n2',
	'entiteAffectation': 'aff-princ',
	'supannEntiteAffectation': 'aff-princ',
	'estInscrit': 'estinscrit',
	'inscription.': 'insc-',
	'supannEntiteAffectationPrincipale': 'aff-princ',
	'"uid"': '"ldap_uid"',
	'ezpaarse': 'ezagimus',
	'businesscategorygrouped"': 'businesscategorygrouped-hr"',
	'coursmoodle': 'moodlecours',
	'moodledblog': 'moodledb',
	'affectation-principale': 'aff-princ',
	'idprequest':'idp',
	'multistats': 'multi',
	'user.name': 'user.sid',
	'platformName': 'platform_name',
	'client_virtul': 'virt_client',
	'session': 'virt_session',
	'user': 'virt_user',
	'application': 'virt_application'
}

# URL à laquelle les objets vont être importés (à envoyer en POST)
url_import = "http://agimusv7.univ.fr:5701/api/saved_objects/"



################
#
# Références des indexes et recherches préalablement créées dans kibana
#	A remplacer par les références à vos index nouvellement créés
#	Il se peut que vous ayez entre 1 et n index-pattern et entre 0 et n search
#
########################################
reference_indexes = {
	'ag': {
        "name": "ag",
        "type": "index-pattern",
        "id": "8a45d2a0-46fe-11e9-8ca7-553ac88f8821"
      }
	}

reference_searches = {
	'ezP-base' : {
    "name": "search_0",
    "type": "search",
    "id": "dc406dc0-b528-11e9-9111-d70c38835a32"
  }
}


##########################
# Fonctions
#

## Traitement d'un fichier
#    importFile est de type TextIOWrapper
def traitement(importFile, dossier_export, do_import=False):
	dossier_export = dossier_export.rstrip('/')
	(base_fichier, ext) = os.path.splitext(importFile.name)
	(dossier, nom_fichier) = os.path.split(base_fichier)
	print(f'{nom_fichier} : le fichier résultat est {dossier_export}/{nom_fichier}_converti{ext}')

	parsedJSON = json.load(importFile)
	if isinstance(parsedJSON,list):
		# Export depuis l'interface web
		source = parsedJSON[0]["_ib post requestsource"]
	elif parsedJSON["version"]:
		# Export direct depuis l'index
		source = parsedJSON
	else :
		print('''
Le format du fichier en entrée n'est pas reconnu.
Assurez-vous qu'il soit un export de kibana ou directement de l'index kibana dans elasticsearch.
''')
		ArgumentParser.print_usage()

	references = []

	## On remplace l'index utilisé et on remplace le filtre du type par un filtre d'index
	# Transformation de l'index
	source["kibanaSavedObjectMeta"]["searchSourceJSON"] = re.sub('"index"\s?:\s?"((ez)?agimus|virtul)-\*"', '"indexRefName":"ag"',source["kibanaSavedObjectMeta"]["searchSourceJSON"])
	# Ajout du filtre sur le nom d'index au lieu du type
	source["kibanaSavedObjectMeta"]["searchSourceJSON"] = re.sub(r'"query"\s?:\s?"_type:([^"\s]*)',r'"query": "_index:ag-\1-*',source["kibanaSavedObjectMeta"]["searchSourceJSON"])

	# Traitement du cas des recherches sauvegardées
	if 'savedSearchId' in source:
		source['savedSearchRefName'] = reference_searches[source['savedSearchId']]['name']


	for ancien, nouveau in conversion_table.items():
		source["kibanaSavedObjectMeta"]["searchSourceJSON"] = source["kibanaSavedObjectMeta"]["searchSourceJSON"].replace(ancien, nouveau)

	# Conversion des attributs dont le nom a été modifié pour la partie spécifique aux visualisations
	if 'visState' in source:
		# Permet d'avoir un affichage camembert et non donut
		source['visState'] = re.sub(r'("isDonut":\s*true,)',r'"type": "pie", \1',source['visState'])
		# Conservation de la taille de police
		source['visState'] = re.sub(r'"fontSize"\s*:\s*"([^"]*)"',r'"metric":{"colorsRange": [{"from": 0, "to": 10000}],"labels":{"show": true },"colorSchema": "Green to Red","style":{"fontSize": "\1"}}',source['visState'])
		# Modification des noms de champs
		for ancien, nouveau in conversion_table.items():
			source["visState"] = source["visState"].replace(ancien,nouveau)

	# Suppression de la version pour permettre plusieurs imports
	del source["version"]

	# Ajout des références à l'index ou aux recherches sauvegardées
	indexRefPos = source["kibanaSavedObjectMeta"]["searchSourceJSON"].find('indexRefName')

	if indexRefPos > 0:
		indexRefPos += 15
		indexRefPosFin = source["kibanaSavedObjectMeta"]["searchSourceJSON"].find('"',indexRefPos)
		indexRefName = source["kibanaSavedObjectMeta"]["searchSourceJSON"][indexRefPos:indexRefPosFin]
		references.append(reference_indexes[indexRefName])

	if 'savedSearchRefName' in source:
		references.append(reference_searches[source["savedSearchId"]])
		del source["savedSearchId"]
		source["kibanaSavedObjectMeta"]["searchSourceJSON"] = '{\"query\":{\"query\":\"\",\"language\":\"kuery\"},\"filter\":[]}'

	# Cas des dashboards
	# On boucle sur le panelsJSON et on transforme les id en références
	if 'panelsJSON' in source:
		panels = source["panelsJSON"][2:-2].split("},{")
		panelsRes = []
		inc = 0
		for panel in panels:
			idPanel = re.search('"id"\s*:\s*"([^"]*)"',panel).group(1)
			references.append({"name": f'panel_{inc}', "type": "visualization", "id": idPanel })
			panelsRes.append('{' + re.sub(r'"id":"[^"\s]*"', f'"panelRefName":"panel_{inc}"', panel.replace(',"type":"visualization"','')) + '}')
			inc+=1
		source['panelsJSON'] = '[' + ','.join(panelsRes) + ']'

	# Modification des filtres
	source["kibanaSavedObjectMeta"]["searchSourceJSON"] = re.sub(r'"value"\s?:\s?"([a-zA-Z]*)"',r'"value":"\1","params":{"query":"\1"},"type":"phrase"',source["kibanaSavedObjectMeta"]["searchSourceJSON"])

	content = {"attributes":source, "references": references }

	fichier_converti = f'{dossier_export}/{nom_fichier}_converti{ext}'
	with open(fichier_converti, 'w', encoding='utf8') as json_file:
		json.dump(content, json_file, indent=1, ensure_ascii=False)

	if do_import:
		print(f'''  On lance maintenant l'import de {nom_fichier} dans {url_import.rstrip('/')}/{args.obj_type}''')
		reimport(content, nom_fichier)

# Reimport d'un fichier
def reimport(content, id):
	url = url_import.rstrip('/') + f'/{args.obj_type}/{parse.quote(id)}?overwrite=true'
	req = request.Request(url, method='POST')
	req.add_header('Content-type', 'application/json')
	req.add_header('kbn-xsrf', 'true')

	# Envoi des données dans kibanav7
	corps=bytes(json.dumps(content, ensure_ascii=False),encoding="utf8")
	req.data = corps
	# print(req.data)
	try:
		request.urlopen(req)
		print('  +++ OK +++')
	except error.HTTPError as e:
		if (e.code == 409):
			print('  --- Erreur --- L''objet existe déjà')
		else:
			print('  --- Erreur --- ', e.code, e.reason)
	except error.URLError as e:
		print('  --- Erreur --- ', e.reason)



##########################
# Vérification des arguments et exécution de la conversion
#

# classe permettant de vérifier les dossiers donnés en argument
class readable_dir(argparse.Action):
	def __call__(self, parser, namespace, values, option_string=None):
		prospective_dir=values
		if not os.path.isdir(prospective_dir):
			raise argparse.ArgumentError(self, f'''{prospective_dir} n'est pas un dossier''')
		# Test du dossier à traiter
		if self.dest == 'dossier':
			if os.access(prospective_dir, os.R_OK):
				setattr(namespace,self.dest,prospective_dir)
			else:
				raise argparse.ArgumentError(self, f'''{prospective_dir} n'est pas un dossier lisible''')
		# Test du dossier résultat
		if self.dest == 'dossier_export':
			if os.access(prospective_dir, os.W_OK):
				setattr(namespace,self.dest,prospective_dir)
			else:
				raise argparse.ArgumentError(self, f'''{prospective_dir} n'est pas un dossier modifiable''')


parser = argparse.ArgumentParser(description='Ce script vous permet de convertir (et éventuellement réimporter) un export de kibana 4 vers kibana 7.')
parser.add_argument('-i', '--reimport', action='store_true', help='reimporte directement le fichier dans le kibana configuré dans le script')
parser.add_argument('-t', '--type', choices=['visualization', 'dashboard'], dest='obj_type', help='type d''objet à reimporter', default='visualization')
parser.add_argument('dossier_export', action=readable_dir, help='le dossier dans lequel sont générés les fichiers résultats', default='/tmp', nargs='?')
a_convertir = parser.add_mutually_exclusive_group(required=True)
a_convertir.add_argument('-f', '--fichier', type=argparse.FileType('r', encoding='utf8'), help='un fichier à convertir (et éventuellement importer)', metavar='fichier_a_traiter')
a_convertir.add_argument('-d', '--dossier', action=readable_dir, help='le dossier à convertir (et éventuellement importer). Seuls les fichiers avec une extension json seront traités', metavar='dossier_a_traiter')
args = parser.parse_args()

if (args.fichier):
	traitement(args.fichier, args.dossier_export, args.reimport)
elif (args.dossier):
	for f in os.listdir(os.path.abspath(args.dossier)):
		(base_fichier, ext) = os.path.splitext(f)
		if ext == ".json" :
			with open(args.dossier.rstrip('/')+'/'+f, 'r', encoding='utf8') as importFile:
				traitement(importFile, args.dossier_export, args.reimport)
		else:
			print  (f'#### On ne traite pas {f} car son extension est "{ext}" au lieu de .json')
