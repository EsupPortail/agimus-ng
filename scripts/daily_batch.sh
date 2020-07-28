#! /bin/bash

if [ -z "$1" ]
then
    DATE=`date -d yesterday +"%Y/%m/%d"`
else
    DATE=$1
fi


NBDAY_KEEP_LOG=3
LOG_DIR=/data/logs
REP_LOGS=$LOG_DIR/$DATE
ERROR_LOG=$REP_LOGS/error.agimus.log
INFO_LOG=$REP_LOGS/info.agimus.log
STAT_LDAP_LOG=$REP_LOGS/stats-ldap.log
BUILD_HOME="/opt/agimus-ng/build"
LOGSTASH_DIR="/opt/logstash"

DELETE_OLD_LOG=true
DUMP_KIBANA_ES=false

MAIL_DEST="agimus-tech@univ.fr"

LINE_SEPARATOR="#################################################################"

# Send stderr both to ERROR_LOG and INFO_LOG
exec 2> >(tee -a $ERROR_LOG >> $INFO_LOG) >>$INFO_LOG


echo "$LINE_SEPARATOR"
echo "#### Start of the process : "`date +'%F %R'`
echo "$LINE_SEPARATOR"
echo ""

if [ "$DELETE_OLD_LOG" = true ] ; then
    echo "$LINE_SEPARATOR"
    echo "#### Delete old log - $NBDAY_KEEP_LOG days ago : "`date +'%F %R'`
    REP_LOGS_OLD=$LOG_DIR/$(date --date $NBDAY_KEEP_LOG' days ago' '+%Y%m%d')
    rm -f $REP_LOGS_OLD/*.log.{gz,bz2}
    echo ""
fi

if [ "$DUMP_KIBANA_ES" = true ] ; then
	echo "$LINE_SEPARATOR"
	echo "#### Dump Elasticsearch templates"
	$BUILD_HOME/scripts/es_template_export.py $REP_LOGS/es_template_export
  echo "$LINE_SEPARATOR"
  echo "#### Dump .kibana index"
	$BUILD_HOME/scripts/kibana_export.py $REP_LOGS/kibana_export
	echo ""
fi

echo "$LINE_SEPARATOR"
echo "#### Delete yesterday index ldap : "`date +'%F %R'`
curl --silent -XDELETE 'http://localhost:9200/ldap/'
echo ""
echo ""

echo "$LINE_SEPARATOR"
echo "#### Rebuild of ldap index and make stats index : "`date +'%F %R'`
$LOGSTASH_DIR/bin/logstash --quiet -f $BUILD_HOME/logstash/logstash-ldap.conf >&2 && python $BUILD_HOME/scripts/ldap-agg.py > $STAT_LDAP_LOG
echo "See : $STAT_LDAP_LOG"
echo ""

echo "$LINE_SEPARATOR"
echo "#### Import CAS trace : "`date +'%F %R'`
if [ -f "$REP_LOGS/trace-cas.log.bz2" ]; then
	echo "#### Number of lines in file "`bzcat $REP_LOGS/trace-cas.log.bz2 | wc -l`
	bzcat $REP_LOGS/trace-cas.log.bz2 | $LOGSTASH_DIR/bin/logstash --quiet -f $BUILD_HOME/logstash/logstash-trace.conf >&2
else
	echo "ERR : NO file logs CAS-TRACE" >&2
fi
echo ""

echo "$LINE_SEPARATOR"
echo "#### Import ENT logs : "`date +'%F %R'`
if [ -f "$REP_LOGS/access-ent.log.gz" ]; then
	echo "#### Number of lines in file "`zcat $REP_LOGS/access-ent.log.gz | wc -l`
	zcat $REP_LOGS/access-ent.log.gz | $LOGSTASH_DIR/bin/logstash --quiet -f $BUILD_HOME/logstash/logstash-esup.conf >&2
else
        echo "ERR : NO file logs ENT" >&2
fi
echo ""

echo "$LINE_SEPARATOR"
echo "#### Import Moodle logs : "`date +'%F %R'`
if [ -f "$REP_LOGS/moodle-access.log.gz" ]; then
	echo "#### Number of lines in file "`zcat $REP_LOGS/moodle-access.log.gz | wc -l`
	zcat $REP_LOGS/moodle-access.log.gz | $LOGSTASH_DIR/bin/logstash --quiet -f $BUILD_HOME/logstash/logstash-moodle.conf >&2
else
        echo "ERR: NO file logs Moodle" >&2
fi
echo ""

####
#
# You can also get moodle log from db
#
####
#    echo "$LINE_SEPARATOR"
#    echo "#### Import COURS Moodle : "`date +'%F %R'`
#    if [ -f "$REP_LOGS/coursMoodle.log" ]; then
#        echo "#### Number line in file $DATE/coursMoodle.log : "`cat $REP_LOGS/coursMoodle.log | wc -l`
#        ## On ajoute en fin de ligne un timestamp postérieur pour être certain que les cours soient dans l'index attendu
#        sed "s/$/;[time:`date --date="$DATE +5 hours" +%s`]/"  $REP_LOGS/coursMoodle.log | $LOGSTASH_DIR/bin/logstash --quiet -f $BUILD_HOME/conf/logstash-coursmoodle.conf >&2
#    else
#            echo -e "\n------\n-\n-  ERR: NO file logs COURS Moodle\n-\n------\n" >&2
#    fi
#    echo ""
#
#    echo "$LINE_SEPARATOR"
#    echo "#### Import Moodle logs FROM DB : "`date +'%F %R'`
#    echo -e "\nLes logs du traitement se trouvent dans le fichier "$REP_LOGS"/import_moodle.log"
#    $BUILD_HOME/scripts/moodle/import_moodle_logs.sh $DATE
#    LOGSIZE=`wc -l < "$REP_LOGS"/import_moodle.log`
#    if [ $LOGSIZE -ne 0 ];then
#        echo -e "\nATTENTION :\nTaille du fichier "$REP_LOGS"/import_moodle.log : "$LOGSIZE"\n"
#    fi

####
#
# Traitement des logs Antispam Renater
#
####

echo "$LINE_SEPARATOR"
echo "#### Import Antispam Renater logs : "`date +'%F %R'`
if [ -f "$REP_LOGS/ASrenater.log.bz2" ]; then
    echo "#### Number line in file $DATE/ASrenater.log.bz2 : "`bzcat $REP_LOGS/ASrenater.log.bz2 | wc -l`
    # On ajoute l'année aux débuts de lignes de log
    bzcat $REP_LOGS/ASrenater.log.bz2 | sed "s/.*/`date --date="$DATE" +%Y`-&/" | $LOGSTASH_DIR/bin/logstash -w8 --quiet -f $BUILD_HOME/conf/logstash-renaterantispam.conf >&2
else
        echo -e "\n------\n-\n-  ERR : NO file logs antispam Renater\n-\n------\n" >&2
fi
echo ""


####
#
# Traitement des infos owncloud
# Les infos sont générées par le script cron_cnx_bul.sh lancé sur la machine owncloud
#
####

echo "$LINE_SEPARATOR"
echo "#### Import OC-Stats logs : "`date +'%F %R'`
if [ -f "$REP_LOGS/cnx-oc.log" ]; then
		echo "#### Number line in file $DATE/cnx-oc.log : "`cat $REP_LOGS/cnx-oc.log | wc -l`
		## On ajoute en fin de ligne un timestamp postérieur pour être certain que les logs soient considérés comme du jour
		sed "s/$/;[time:`date --date="$DATE +5 hours" +%s`]/" $REP_LOGS/cnx-oc.log | $LOGSTASH_DIR/bin/logstash -w8 --quiet -f $BUILD_HOME/logstash/logstash-oc-stats.conf >&2
else
				echo -e "\n------\n-\n-  ERR : NO file logs OC-Stats\n-\n------\n" >&2
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
    /bin/mail -s "$(echo -e "ERREUR dans le traitement AGIMUS-NG : "$(date '+%d/%m/%Y')"\nContent-Type: text/plain")" $MAIL_DEST < $INFO_LOG
fi
