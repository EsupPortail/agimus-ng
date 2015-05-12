#!/usr/bin/python
# vim: et sw=4 ts=4:
# -*- coding: utf-8 -*-
#
# python scripts/ldap-agg.py

from datetime import datetime
from datetime import timedelta
from elasticsearch import Elasticsearch
import sys

# Add date argument to give date parameter in case of add previous traetment
date_object = datetime.now() - timedelta(days=1)
try:
    dateToRecord = sys.argv[1]
    date_object = datetime.strptime('%s' % (dateToRecord), '%Y/%m/%d')
except:
    pass

es = Elasticsearch()

bodysearch = {
    "query": {
        "match_all": {}
    },
    "aggs": {}
}
ldapAttrs = [__VOS_ATTRIBUTS__]

for attr in ldapAttrs:
    bodysearch["aggs"][attr] = {"terms": {"field": attr, "size": 200}}

res = es.search(index="ldap", body=bodysearch)

for attr in ldapAttrs:
    print attr
    for f in res["aggregations"][attr]["buckets"]:
        es.index(index="ldap-stat", doc_type="ldap-stat", body={"attribut": attr,
                 "value": f["key"], "count": f["doc_count"], "timestamp": date_object})
