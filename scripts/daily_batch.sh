#! /bin/bash

ES_DIR=/home/elasticsearch
NBDAY_KEEP_LOG=3
LOG_DIR=/data/logs
REP_LOGS=$LOG_DIR/$(date '+%Y%m%d')
ERROR_LOG=$REP_LOGS/error.agimus.log
INFO_LOG=$REP_LOGS/info.agimus.log
STAT_LDAP_LOG=$REP_LOGS/stats-ldap.log

MAIL_DEST="agimus-tech@univ.fr"

LINE_SEPARATOR="#################################################################"

# Send stderr both to ERROR_LOG and INFO_LOG
exec 2> >(tee -a $ERROR_LOG >> $INFO_LOG) >>$INFO_LOG


echo "$LINE_SEPARATOR"
echo "#### Start of the process : "`date +'%F %R'` 
echo "$LINE_SEPARATOR"
echo ""

echo "$LINE_SEPARATOR"
echo "#### Delete old log - $NBDAY_KEEP_LOG days ago : "`date +'%F %R'`
REP_LOGS_OLD=$LOG_DIR/$(date --date $NBDAY_KEEP_LOG' days ago' '+%Y%m%d')
rm -f $REP_LOGS_OLD/*.log.{gz,bz2}
echo ""

echo "$LINE_SEPARATOR"
echo "#### Dump Elasticsearch templates and .kibana4 index
$ES_DIR/bin/dump_kibana_ES_conf.sh
echo ""

echo "$LINE_SEPARATOR"
echo "#### Delete yesterday index ldap : "`date +'%F %R'`
curl --silent -XDELETE 'http://localhost:9200/ldap/'
echo ""
echo ""

echo "$LINE_SEPARATOR"
echo "#### Rebuild of ldap index and make stats index : "`date +'%F %R'`
logstash --quiet -f logstash/logstash-ldap.conf >&2 && python $ES_DIR/scripts/ldap-agg.py > $STAT_LDAP_LOG
echo "See : $STAT_LDAP_LOG"
echo ""

echo "$LINE_SEPARATOR"
echo "#### Import CAS trace : "`date +'%F %R'`
if [ -f "$REP_LOGS/trace-cas.log.bz2" ]; then
	echo "#### Number of lines in file "`bzcat $REP_LOGS/trace-cas.log.bz2 | wc -l`
	bzcat $REP_LOGS/trace-cas.log.bz2 | logstash --quiet -f logstash/logstash-trace.conf >&2
else 
	echo "ERR : NO file logs CAS-TRACE" >&2
fi
echo ""

echo "$LINE_SEPARATOR"
echo "#### Import ENT logs : "`date +'%F %R'`
if [ -f "$REP_LOGS/access-ent.log.gz" ]; then
	echo "#### Number of lines in file "`zcat $REP_LOGS/access-ent.log.gz | wc -l`
	zcat $REP_LOGS/access-ent.log.gz | logstash --quiet -f logstash/logstash-esup4.conf >&2
else
        echo "ERR : NO file logs ENT" >&2
fi
echo ""

echo "$LINE_SEPARATOR"
echo "#### Import ARCHE logs : "`date +'%F %R'`
if [ -f "$REP_LOGS/arche-access.log.gz" ]; then
	echo "#### Number of lines in file "`zcat $REP_LOGS/arche-access.log.gz | wc -l`
	zcat $REP_LOGS/arche-access.log.gz | logstash --quiet -f logstash/logstash-moodle.conf >&2
else 
        echo "ERR: NO file logs ARCHE" >&2
fi
echo ""

echo "$LINE_SEPARATOR"
echo "#### Clean ES index : older CAS-TRACE : "`date +'%F %R'`
curl --silent -XDELETE 'http://localhost:9200/trace/_query' -d '{"query": {"range": {"@timestamp": {"lt": "now-15d"} } }}'
echo ""
echo ""

echo "$LINE_SEPARATOR"
echo "### End of the process : "`date +'%F %R'`
echo "$LINE_SEPARATOR"

if [ -s $ERROR_LOG ]; then
    export CONTENT_TYPE="text/plain"
    /bin/mail -s "ERREUR dans le traitement AGIMUS-NG : "$(date '+%d/%m/%Y') $MAIL_DEST < $INFO_LOG
fi
