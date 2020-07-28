#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# kibana_import.py
# Usage : scripts/kibana_import.py path_to_folder

# Script permettant d'importer un dossier d'objets préalablement exportés dans Kibana.

import sys, json, config
from pathlib import Path
from elasticsearch import Elasticsearch

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
es = Elasticsearch( config.cluster_ES )

print ( f'''\nDébut de l'import dans l'index {config.index_kibana}\n''' )

for filePath in files_list:
	print ( f'Import de {filePath.stem}' )
	objet = json.loads( filePath.read_text( encoding='utf-8' ) )
	es.index( index=config.index_kibana, id=filePath.stem, body=objet )

print( '\n  -->  Import terminé\n')
