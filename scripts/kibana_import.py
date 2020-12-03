#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# kibana_import.py
# Usage : scripts/kibana_import.py dossier_a_importer

# Script permettant d'importer un dossier d'objets préalablement exportés dans Kibana.

import sys, json, config
from pathlib import Path
import requests


if len(sys.argv) != 2:
	print("Erreur : Vous devez spécifier le dossier ou fichier que vous souhaitez importer")
	print (f'Usage : {sys.argv[0]} dossier_a_importer')
	sys.exit(2)
else :
	importPath = Path(sys.argv[1])
	if importPath.exists() :
		if importPath.is_dir() :
			files_list = list( importPath.glob('**/*.ndjson') )
		else :
			files_list = [ importPath ]
	else :
		print ( f'''Le chemin {importPath} n'existe pas
		Usage : {sys.argv[0]} dossier_ou_fichier_a_importer''')
		sys.exit(1)

HEADERS = {
	'kbn-xsrf': 'true'
}

uri = f'{config.uri_saved_objects}/_import?overwrite=true'

files = [{'file': open(f,'rb')} for f in files_list]

print ( f'''\n#### Début de l'import à l'url {config.uri_saved_objects}\n''' )
for fichier in files:
	print(f'''\nImport de {fichier["file"].name}''')
	r = requests.post(uri, headers=HEADERS, files=fichier)
	if r.json()["success"]:
		print(f'''  --> OK''')
	else :
		print(f'''  --> Erreur : {r.text}''')
print( '\n####  -->  Import terminé   ####\n')
