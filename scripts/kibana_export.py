#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# kibana_export.py
# Usage : scripts/kibana_export.py dossier_destination

# Permet d'exporter tous les  objets de l'index kibana ayant un des types
#  définis dans config.py. Par défaut :
#  'visualization', 'dashboard', 'index-pattern', 'query', 'config'
#  Les objets sont enregistrés dans le dossier passé en paramètre


import sys, json, config, re
from pathlib import Path
import requests

if len(sys.argv) != 2:
	print("Erreur : Vous devez spécifier le dossier vers lequel exporter")
	print (f'Usage : {sys.argv[0]} dossier_destination')
	sys.exit(2)

exportDir = Path(sys.argv[1])

HEADERS = {
    'Content-Type': 'application/json',
	'kbn-xsrf': 'true'
}

uri = f'{config.uri_saved_objects}/_export'

query = json.dumps({
	"excludeExportDetails": True,
	"type": config.objectsToExport
})

r = requests.post(uri,headers=HEADERS, data=query)
# On débute le nombre d'objets à -1 car le résumé en fin de fichier va être compté alors qu'il ne contient aucune donnée
nb_obj = 0

for object in r.text.splitlines():
	elt = json.loads(object)
	if "attributes" in elt and "title" in elt["attributes"]:
		# Génération du nom de fichier en supprimant les caractères spéciaux
		filename = re.sub("(:|\.)", "", re.sub("(!|\$|#|&|\"|\'|\(|\)|\||<|>|`|\\\|;| |\/)", "_", elt["attributes"]["title"])) + ".ndjson"
		exportFile = exportDir / elt["type"] / filename
		# Création du dossier s'il n'existe pas
		exportFile.parent.mkdir(parents=True, exist_ok=True)
		# Dump object to file
		exportFile.write_text(json.dumps(elt))

		nb_obj+=1
		print(f'export de {elt["attributes"]["title"]} de type {elt["type"]}')

print(f'''On vient d'exporter {nb_obj} objets''')
