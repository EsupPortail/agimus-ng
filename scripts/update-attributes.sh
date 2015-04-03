#!/bin/bash

#
# script to update, add, remove attributes form an existing eslactisearch index
# Use ../logstash/logstash-update-attributes-TEMPLATE.conf file

# List all indeces
#INDEX_TO_PARSE='logstash-*'
INDEX_TO_PARSE='logstash-2015.04.02'

# Template file to update attributes
TEMPLATE_LOGSTASH_FILE=/home/elasticsearch/conf/logstash-update-attributes-TEMPLATE.conf

# temporary dir
TMP_DIR=/home/elasticsearch/tmp

old_IFS=$IFS     # save old sperator
IFS=$'\n'     # set new separator

ALL_INDICES=$(curl --silent "localhost:9200/_cat/indices/${INDEX_TO_PARSE}")

# For all index
for l in $ALL_INDICES; do
    #get index name
    name=$(echo $l | awk '{print $3}')
        
    # generate temproray file
    tmp_file=$TMP_DIR"/update-attributes-$name.$$.tmp.conf"

    # replace index name in template file by temporary name
    sed -e "s/__INDEX_FROM__/${name}/g;s/__INDEX_DEST__/${name}-tmp/g" $TEMPLATE_LOGSTASH_FILE > $tmp_file
	    
    echo "*** Updates attributes from index ${name} : "
    echo " - modify index source : ${name} destination : ${name}-tmp "
    /home/elasticsearch/bin/logstash --quiet -f $tmp_file
	    
    echo " - optimize index ${name}-tmp"
    curl --silent -XPOST 'http://localhost:9200/'${name}'-tmp/_optimize?pretty'
	    
    echo " - delete old index ${name} "
    curl --silent -XDELETE 'http://localhost:9200/'${name}'/?pretty'
	    	    
    echo " - move index ${name}-tmp to ${name}"
    /home/elasticsearch/bin/stream2es es --source http://localhost:9200/${name}-tmp --target http://localhost:9200/${name}
	    
    echo " - optimize new index ${name}"
    curl --silent -XPOST 'http://localhost:9200/'${name}'/_optimize?pretty'
	    
    echo " - delete old temporary index ${name}-tmp"
    curl --silent -XDELETE 'http://localhost:9200/'${name}'-tmp/?pretty'
	    
    /bin/rm -f $tmp_file
done;

IFS=$old_IFS
