#!/bin/bash

DATE=`date -d yesterday +"%Y/%m/%d"`
LOG_DIR=/data/logs
REP_LOGS=$LOG_DIR/$DATE

# SAVE Kibana Index
curl -s -XGET "http://localhost:9200/.kibana/_search?pretty" -d'
{
  "size": 2000
}'  > $REP_LOGS/index_kibana.json

# SAVE Elasticsearch Templates
curl -s -XGET http://localhost:9200/_template?pretty > $REP_LOGS/EStemplates.json

