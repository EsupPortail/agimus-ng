#!/usr/bin/python
# -*- coding: utf-8 -*-

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
