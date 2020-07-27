#!/home/agimus/bin/python

# kibana_export.py
# Usage : scripts/kibana_export.py

# To export quickly all the kibana objects in two directory : kibana/%d-%m-%y_visualization
# and kibana/%d-%m-%y_dashboard

# import sys, json, time
import json, time
from pathlib import Path
from elasticsearch import Elasticsearch

es = Elasticsearch(['http://agimus1.univ:9200/','http://agimus2.univ.fr:9200/'])

curDate = time.strftime('%d-%m-%y', time.localtime())
exportDir = Path('/home/agimus/export_kibana/' + curDate)

objectsToExport = [ 'visualization', 'dashboard', 'index-pattern', 'query']

dump_obj = es.search(index='.kibana', body='{"size":2000}')

for objects in dump_obj['hits']['hits']:
	obj_type = objects['_source']['type']
	if obj_type in objectsToExport:
		# Génération du nom de fichier
		filename = objects['_source'][obj_type].get('title','NO_TITLE').replace('/', '_') + '.json'
		exportFile = exportDir / obj_type / filename
		# Création du dossier s'il n'existe pas
		exportFile.parent.mkdir(parents=True, exist_ok=True)
		# Écriture de l'objet dans le fichier
		exportFile.write_text(json.dumps(objects['_source']))
