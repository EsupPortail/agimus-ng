#! /bin/sh

if [ -z "$1" ]
then
    DATE=`date '+%Y%m%d'`
else
    DATE=$1
fi

LOG_DIR=/data/logs
CURRENT_TS=`date +%s`
BUILD_HOME="/opt/agimus-ng/"
LOGSTASH_DIR="/opt/logstash"
EXIT_LOG=$LOG_DIR/$DATE/import_moodle.log
CONF_PATH=$BUILD_HOME"/logstash/"
export CONF_PATH=$CONF_PATH
STMT_FILE=$CONF_PATH/moodle-from-db.sql

# Suppression des correspondances id <-> login UL
rm -f $CONF_PATH/maps/map-moodle-users.yml
# Re-génération des correspondances
echo "** Import des correspondances utilisateurs"
$LOGSTASH_DIR/logstash --quiet -f $CONF_PATH/gen_mappings/moodle_users/

# Suppression des correspondances id <-> login UL
rm -f $BUILD_HOME/mappings_auto/map-moodle-categories.yml
# Re-génération des correspondances
echo "** Import des correspondances categories"
$LOGSTASH_DIR/logstash --quiet -f $CONF_PATH/gen_mappings/moodle_categories/

if [ -s $STMT_FILE ]
then
    # Ecriture de la requête de récupération des logs
    sed -i 's/`timecreated` <= \([0-9][0-9]*\).*$/`timecreated` <= '$CURRENT_TS' AND `timecreated` > \1/' $STMT_FILE

    if [ ! -d $LOG_DIR/$DATE ]
    then
      mkdir -p $LOG_DIR/$DATE
    fi
    # Exécution de la requête
    echo -e "\n** Import des logs arche. Les logs de cette exécution se trouvent dans "$EXIT_LOG". Un message s'affiche ci-dessous s'il contient des erreurs."
    $LOGSTASH_DIR/logstash --quiet -f $CONF_PATH/moodle-from-db > $EXIT_LOG



    # Suppression des logs plus vieux qu'une semaine (permet de faire un retraitement si nécessaire)
   ! grep -q -i error $EXIT_LOG && $LOGSTASH_DIR/purge_db_logs_moodle.py `expr $CURRENT_TS - 604800`
fi
