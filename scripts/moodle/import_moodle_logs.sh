#! /bin/bash

if [ -z "$1" ]
then
    DATE=`date -d yesterday +"%Y/%m/%d"`
else
    DATE=$1
fi

CURRENT_TS=`date +%s`
BUILD_HOME="/opt/agimus-ng/build"
LOGSTASH_DIR="/opt/logstash"
EXIT_LOG=/data/logs/$DATE/import_moodle.log

# Suppression des correspondances id <-> login
rm -f $BUILD_HOME/logstash/maps/map-moodle_users.yml
# Re-génération des correspondances
$LOGSTASH_DIR/bin/logstash --quiet -f $BUILD_HOME/logstash/logstash-moodle-user.conf

# Ecriture de la requête de récupération des logs
echo "SELECT * FROM \`logstore_standard_log\` WHERE \`timecreated\` <= "$CURRENT_TS > $BUILD_HOME/logstash/logstash-moodle-from-db_stmt.sql

# Exécution de la requête
$LOGSTASH_DIR/bin/logstash --quiet -f $BUILD_HOME/logstash/logstash-moodle-from-db.conf > $EXIT_LOG

# Suppression des logs traités
! grep -q -i error $EXIT_LOG && $BUILD_HOME/scripts/purge_db_logs_moodle.py $CURRENT_TS
