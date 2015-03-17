#!/usr/bin/python
# vim: et sw=4 ts=4:
# -*- coding: utf-8 -*-
#
# python scripts/ldap-agg.py
 
from datetime import datetime
from elasticsearch import Elasticsearch
es = Elasticsearch()
bodysearch = {
    "query" : {
        "match_all" : { }
    },
    "aggs" : {}
}
ldapAttrs = [ __VOS_ATTRIBUTS__ ]
 
for attr in ldapAttrs:
    bodysearch["aggs"][attr]={"terms":{"field":attr, "size":100}}
 
res = es.search(index="ldap", body=bodysearch)
 
for attr in ldapAttrs:
    print attr[0:-4]
    for f in res["aggregations"][attr]["buckets"]:
        print "   --> f : %s" %f
        es.index(index="ldap-stat", doc_type="ldap-stat", body={"attribut": attr[0:-4], "value": f["key"], "count":f["doc_count"], "timestamp":datetime.now()}) 
