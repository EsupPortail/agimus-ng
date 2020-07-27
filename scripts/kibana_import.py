#!/home/agimus/scripts/python
# -*- coding: utf-8 -*-

# kibana_import.py
# Usage : scripts/kibana_import.py path_to_folder

# Script permettant d'importer un dossier d'objets exportés dans Kibana.

import sys, json
from pathlib import Path
from elasticsearch import Elasticsearch


cluster_ES = ['http://agimus1.univ.fr:9200/',
					'http://agimus2.univ.fr:9200/'])
index_kibana = '.kibana'

if len(sys.argv) != 2:
	print("Erreur : Vous devez spécifier le fichier ou dossier que vous souhaitez importer")
	print (f'Usage : {sys.argv[0]} dossier_ou_fichier_a_importer')
	sys.exit(2)
else :
	importPath = Path(sys.argv[1])
	if importPath.exists() :
		if importPath.is_dir() :
			files_list = list( importPath.glob('**/*.json') )
		else :
			files_list = [ importPath ]
	else :
		print ( f'''Le chemin {importPath} n'existe pas
		Usage : {sys.argv[0]} dossier_ou_fichier_a_importer''')
		sys.exit(1)

# Connexion Elasticsearch
es = Elasticsearch( cluster_ES )

print ( f'''\nDébut de l'import dans l'index {index_kibana}\n''' )

for filePath in files_list:
	print ( f'Import de {filePath.stem}' )
	objet = json.loads( filePath.read_text( encoding='utf-8' ) )
	es.index( index=index_kibana, id=filePath.stem, body=objet )

print( '\n  -->  Import terminé\n')

# kibana_import.py
# Usage : scripts/kibana_import.py path_to_folder

# Script permettant d'importer un dossier de visualisations ou de dashboards dans Kibana.

import sys, json, os, requests

if len(sys.argv) != 3:
	print "Error : Arguments needed : 1 : Path to the folder to import in Kibana, 2 : visu|dash"
	print "Usage : scripts/kibana_import.py path_to_folder visu|dash"
	sys.exit(2)
elif not os.path.isdir(sys.argv[1]):
	print "Error : " + sys.argv[1] + " is not a directory"
	print "Usage : scripts/kibana_import.py path_to_folder visu|dash"
	sys.exit(2)
elif sys.argv[2] not in ("visu", "dash"):
	print "Error : " + sys.argv[2] + " : invalid argument"
	print "Usage : scripts/kibana_import.py path_to_folder visu|dash"
	sys.exit(2)
else:
	importDir = os.path.abspath(sys.argv[1])

if sys.argv[2] == "visu":
	kibana_type = "visualization"
else:
	kibana_type = "dashboard"

os.chdir(importDir)

for f in os.listdir(importDir):
	tabFile = f.split(".")
	if tabFile[1] == "json":
		with open(f) as json_file:
			parsedJSON = json.load(json_file)
		url = "http://localhost:9200/.kibana/" + kibana_type + "/" + tabFile[0]
		headers = {'content-type': 'application/json'}
		request = requests.post(url, data=json.dumps(parsedJSON), headers=headers)
		print request.text
print "Done\n"
