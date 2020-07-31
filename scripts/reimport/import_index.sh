#! /bin/bash
if [ $# -eq 2 ]
then
    export TYPE_A_TRAITER=$1
    export DATE_A_TRAITER=$2
elif [ $# -eq 1 ]
then
    export TYPE_A_TRAITER=$1
    export DATE_A_TRAITER=`date '+%Y.%m.%d'`
else
    echo "Usage : $0 TYPE_A_TRAITER (DATE_A_TRAITER)"
    exit 1
fi

LOGSTASH_DIR="/opt/logstash"
CONF_PATH=$BUILD_HOME"/logstash/"

echo "#### Transfert de l'index agimus-$DATE_A_TRAITER pour le type $TYPE_A_TRAITER : "`date +'%F %R'`
$LOGSTASH_DIR/logstash --path.data /var/lib/logstash/migration -f $CONF_PATH/migrationv2v7
echo "#### Fin du traitement : "`date +'%F %R'`
echo  ""
