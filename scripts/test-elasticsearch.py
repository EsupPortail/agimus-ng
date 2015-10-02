#!/usr/bin/python
# vim: et sw=4 ts=4:
# -*- coding: utf-8 -*-
#
# python test-elasticsearch.py

from datetime import datetime
from elasticsearch import Elasticsearch

es = Elasticsearch()

doc = {
    'author': 'testeur',
    'text': 'Elasticsearch fonctionne dans python',
    'timestamp': datetime.now(),
}
res = es.index(index="test-index", doc_type='essai', id=1, body=doc)
es.indices.refresh(index="test-index")
print("L'index test-index est cree")

res = es.search(index="test-index", body={"query": {"match_all": {}}})
print("Il y a %d document dans l'index test-index :" % res['hits']['total'])
for hit in res['hits']['hits']:
    print("%(timestamp)s %(author)s: %(text)s" % hit["_source"])

print("On le supprime")
es.indices.delete(index='test-index')
