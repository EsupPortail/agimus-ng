#!/home/agimus/bin/python
# vim: et sw=4 ts=4:
# -*- coding: utf-8 -*-
#
# python test-elasticsearch.py

import elasticsearch, config, sys
from datetime import datetime


doc = {
    'author': 'testeur',
    'text': 'Elasticsearch fonctionne dans python',
    'timestamp': datetime.now(),
}

index_test = 'test-index'

try:
	es = elasticsearch.Elasticsearch(config.cluster_ES)
	res = es.index(index="test-index", id=1, body=doc)
	es.indices.refresh(index=index_test)
	print("L'index test-index est cree")

except elasticsearch.ElasticsearchException as err:
	print (f'''\n***   L'index n'a pas pu être créé. \n***   Erreur : \n{err}''')
	sys.exit(1)

try:
	res = es.search(index=index_test, body={"query": {"match_all": {}}})
	print(f'''Il y a {res['hits']['total']['value']} document(s) dans l'index test-index :''')
	for hit in res['hits']['hits']:
		print(f'''Créé le {hit['_source']['timestamp']} par {hit['_source']['author']} : {hit['_source']['text']}''')

except elasticsearch.ElasticsearchException as err:
	print(f'''\n***   Impossible de récupérer les documents contenus dans {index_test} \n***   Erreur : \n{err}''')
	sys.exit(1)


try:
	es.indices.delete(index='test-index')
	print(f'''L'index de test "{index_test}" est supprimé.''')
	
except elasticsearch.ElasticsearchException as err:
	print(f'''\n***   Impossible de supprimer l'index {index_test} \n***   Erreur : \n{err}''')
	sys.exit(1)

print('''\nLe test s'est déroulé correctement. Le plugin elasticsearch pour python est installé correctement''')
