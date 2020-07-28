#!/usr/bin/env python3
# vim: et sw=4 ts=4:
# -*- coding: utf-8 -*-
#
# date && curl -XDELETE 'http://agimus.univ-lorraine.fr/elasticsearch/ldap/' && bin/logstash -f conf/logstash-ldap.conf && python conf/ldap-es.py && date

import config
from datetime import datetime
from elasticsearch import Elasticsearch

index_stats = 'ldap-stat'

es = Elasticsearch(config.cluster_ES)

bodysearch = {
  "size": 0,
  "aggs": {
    "stats": {
      "composite": {
        "sources": [
        ]
      }
    }
  }
}


for attr in config.ldapAttrs:
    bodysearch["aggs"]["stats"]["composite"]["sources"].append({attr:{"terms":{"field":attr, "missing_bucket": "true"}}})

def boucleAttr(body,after=""):
    if after != "":
        body["aggs"]["stats"]["composite"]["after"]=after
    res = es.search(index="ldap", body=bodysearch)
    for population in res["aggregations"]["stats"]["buckets"]:
        entree = population["key"]
        entree["total"] = population["doc_count"]
        entree["@timestamp"] = datetime.now()
        es.index(index=index_stats, body=entree)
    if "after_key" in res["aggregations"]["stats"]:
        boucleAttr(body,res["aggregations"]["stats"]["after_key"])

boucleAttr(bodysearch)
