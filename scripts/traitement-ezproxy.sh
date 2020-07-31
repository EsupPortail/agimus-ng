#! /bin/sh

if [ -z "$1" ]
then
    DATE=`date '+%Y%m%d'`
else
    DATE=$1
fi

BUILD_HOME="/opt/agimus-ng/"
LOGSTASH_DIR="/opt/logstash/bin"
CONF_DIR=$BUILD_HOME"/logstash"
DATA_LOG="/data/logs"
NOM_FIC=`ls $DATA_LOG/$DATE/ezproxy-*.gz | xargs basename -s .gz`
EXIT_LOG=$DATA_LOG/$DATE/traitement_ezproxy.log


# Exécution de la requête
echo -e "\n** Enrichissement des logs ezProxy de $DATE/$NOM_FIC.gz vers un fichier temporaire /tmp/traitement_ezproxy"
# On définit un path spécifique pour éviter que plusieurs traitements logstash collisionnent
zcat $DATA_LOG/$DATE/$NOM_FIC.gz | $LOGSTASH_DIR/logstash --path.data /var/lib/logstash/ezproxy --quiet -f $CONF_DIR/ezproxy > $EXIT_LOG

echo -e "\n** Copie et compression du résultat /tmp/traitement_ezproxy vers ezPaarse/$NOM_FIC.log.gz"
mv -f /tmp/traitement_ezproxy $DATA_LOG/ezPaarse/fichiers_enrichis/$NOM_FIC.log
gzip $DATA_LOG/ezPaarse/fichiers_enrichis/$NOM_FIC.log

echo -e "\n** Envoi vers le serveur ezPaarse de ezPaarse/fichiers_enrichis/$NOM_FIC.log.gz"
scp $DATA_LOG/ezPaarse/fichiers_enrichis/$NOM_FIC.log.gz ezpaarse@ezpaarse.univ.fr:fichiers_enrichis_agimus/.
