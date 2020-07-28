#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# kibana_export.py
# Usage : scripts/kibana_export.py dossier_destination

# Permet d'exporter tous les  objets de l'index kibana ayant un des types
#  définis dans config.py. Par défaut :
#  'visualization', 'dashboard', 'index-pattern', 'query', 'config'
#  Les objets sont enregistrés dans le dossier passé en paramètre

import json, time, config
from pathlib import Path
from elasticsearch import Elasticsearch

if len(sys.argv) != 2:
	print("Erreur : Vous devez spécifier le dossier vers lequel exporter")
	print (f'Usage : {sys.argv[0]} dossier_destination')
	sys.exit(2)

es = Elasticsearch( config.cluster_ES )

exportDir = Path(sys.argv[1])

dump_obj = es.search( index=config.index_kibana, body='{"size":10000}' )

for objects in dump_obj['hits']['hits']:
	obj_type = objects['_source']['type']
	if obj_type in config.objectsToExport:
		# Génération du nom de fichier
		filename = objects['_id'] + '.json'
		exportFile = exportDir / obj_type / filename
		# Création du dossier s'il n'existe pas
		exportFile.parent.mkdir(parents=True, exist_ok=True)
		# Écriture de l'objet dans le fichier
		exportFile.write_text(json.dumps(objects['_source']))
