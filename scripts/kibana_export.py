#!/usr/bin/python3

# kibana_export.py
# Usage : scripts/kibana_export.py

# To export quickly all the kibana objects in two directory : kibana/%d-%m-%y_visualization
# and kibana/%d-%m-%y_dashboard

import sys, json, os, time
from urllib.request import urlopen

curDir = os.path.dirname(os.path.abspath(__file__))
curDate = time.strftime('%d-%m-%y', time.localtime())
visuDir = curDir + "/../kibana/" + curDate  + "_visualization"
dashDir = curDir + "/../kibana/" + curDate  + "_dashboard"

url = "http://localhost:9200/.kibana/_search?pretty&size=2000"

ressource = urlopen(url)
parsedJSON = json.loads(ressource.readall().decode('utf-8'))

for objects in parsedJSON['hits']['hits']:
	if objects['_type'] == 'visualization':
		if not os.path.exists(visuDir):
			os.makedirs(visuDir)		
		with open(visuDir + "/" + objects['_id'] + ".json", "w") as json_file:
			json.dump(objects['_source'], json_file)
	elif objects['_type'] == 'dashboard':
		if not os.path.exists(dashDir):
			os.makedirs(dashDir)
		with open(dashDir + "/" + objects['_id'] + ".json", "w") as json_file:
			json.dump(objects['_source'], json_file)
