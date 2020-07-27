#!/home/agimus/scripts/python
# -*- coding: utf-8 -*-

# es_templates_export.py
# Usage : scripts/es_templates_export.py dossier_destination

# Permet d'exporter tous les templates elasticsearch dans le dossier passé en paramètre

import sys, json
from pathlib import Path
from elasticsearch import Elasticsearch

if len(sys.argv) != 2:
	print("Erreur : Vous devez spécifier le dossier vers lequel exporter")
	print (f'Usage : {sys.argv[0]} dossier_destination')
	sys.exit(2)

es = Elasticsearch( ['http://agimus1.univ.fr:9200/',
						'http://agimus2.univ.fr:9200/'])

exportDir = Path(sys.argv[1])

# Liste de tous les noms de templates
templates_res = es.cat.templates( h='name' , format = 'json' )
# On ne garde que ceux qui ne sont pas système (ne commençant pas par un ".")
templates_name = [ tpl['name'] for tpl in templates_res if not tpl['name'].startswith('.')]

for template_name in templates_name:
	# Récupération du template
	tpl_obj = es.indices.get_template( name=template_name )
	# Génération du nom de fichier
	filename = template_name + '.json'
	exportFile = exportDir / filename
	# Création du dossier s'il n'existe pas
	exportFile.parent.mkdir( parents=True, exist_ok=True )
	# Dump object to file
	exportFile.write_text( json.dumps( tpl_obj[template_name] ) )
