#! /bin/bash

###########
#
# Script permettant l'import des logs. A exécuter régulièrement, il permet d'effectuer le traitement quelque soit l'heure à laquelle arrive le log
#   Il est conseillé d'exécuter juste avant le script de vérification d'existence du plugin LDAPSearch
#   Exemple de configuration cron :
#     0 3-12 * * * /home/agimus/scripts/check_plugin_ldap.sh > /dev/null 2>&1 && /home/agimus/scripts/daily_batch.sh  > /dev/null 2>&1
#
##############################

if [ -z "$1" ]
then
    DATE=`date -d yesterday +"%Y/%m/%d"`
    # Indique que c'est un traitement quotidien classique
    REBUILD=true
else
    DATE=$1
    # Ce n'est pas un traitement quotidien classique. On ne reconstruit pas le ldap, moodle, …
    REBUILD=false
fi


NBDAY_KEEP_LOG=3
LOG_DIR=/data/logs
REP_LOGS=$LOG_DIR/$DATE
ERROR_LOG=$REP_LOGS/error.agimus.log
INFO_LOG=$REP_LOGS/info.agimus.log
STAT_LDAP_LOG=$REP_LOGS/stats-ldap.log
STT_LOGS=$REP_LOGS/state
MAX_WAIT_LOG_TIME=12    # Derniere heure de passage du script

BUILD_HOME="/opt/agimus-ng/"
LOGSTASH_DIR="/opt/logstash"
# La variable d'environnement CONF_PATH est utilisée dans les configurations logstash
export CONF_PATH=$BUILD_HOME"/logstash/"

DELETE_OLD_LOG=true
DUMP_KIBANA_ES=true

MAIL_DEST="agimus-tech@univ.fr"

LINE_SEPARATOR="#################################################################"
LINE_SEPARATOR_START="________________________________________________________________"

# Send stderr both to ERROR_LOG and INFO_LOG
exec 2> >(tee -a $ERROR_LOG >> $INFO_LOG) >>$INFO_LOG

pid=$$

#############################################################
# Fonctions
#############################################################
wait_log() {
    NLOG=$1
    H=`date "+%H"`

    touch $STT_LOGS/$NLOG.WAITLOG
    if [ -e "$STT_LOGS/AG.OK" ]; then
      # Tous les traitements n'ont pas pu être faits
      rm -f $STT_LOGS/AG.OK
    fi

    if [ $H -ge $MAX_WAIT_LOG_TIME ]; then
        echo -e "\n------\n-\n-  ERR : NO file logs $NLOG\n-\n------\n" >&2
        touch $STT_LOGS/$NLOG.ERROR
        rm -f $STT_LOGS/$NLOG.WAITLOG
    fi

}

ok_log() {
  NLOG=$1
  if [ $# -eq 2 ]; then
    CODE_RETOUR=$2
  else
    CODE_RETOUR=0
  fi
  if [ $CODE_RETOUR -eq 0 ]; then
    if [ -e "$STT_LOGS/$NLOG.WAITLOG" ]; then
      rm -f "$STT_LOGS/$NLOG.WAITLOG"
    fi
    touch "$STT_LOGS/$NLOG.OK"
  fi
}



if [  -e "$STT_LOGS/AG.LOCK" ]
then

    # Dernier traitemant en anomalie
    H=`date "+%H"`
    if [ $H -ge $MAX_WAIT_LOG_TIME ]; then
        echo -e "\n------\n-\n-  ERR : Bloquage sur dernier traitement. \nUn script (pid : $(cat  $STT_LOGS/AG.LOCK)) est toujours en cours d'exécution à ${H}:00. Vérifiez sur le serveur ce qu'il se passe.\n-\n------\n"  >&2
    fi

    exit 1

fi

if [  -e "$STT_LOGS/AG.OK" ]
then
  # Tous les traitements sont finis
  # Il n'y a plus rien à faire
  exit 0
else
  # On crée le fichier qui sera supprimé si un traitement n'est pas encore fait
  ok_log AG
fi

echo $pid > $STT_LOGS/AG.LOCK


echo "$LINE_SEPARATOR_START"
echo -e "|\n|   Start of the process (pid:"$pid") : "`date +'%F %R'`

if [ $REBUILD -a ! -e "$STT_LOGS/LDAP.OK" ]
then

  if [ "$DELETE_OLD_LOG" = true ] ; then
      echo "$LINE_SEPARATOR"
      echo "#### Delete old log - $NBDAY_KEEP_LOG days ago : "`date +'%F %R'`
      REP_LOGS_OLD=$LOG_DIR/$(date --date $NBDAY_KEEP_LOG' days ago' '+%Y%m%d')
      rm -f $REP_LOGS_OLD/*.log.{gz,bz2}
      echo ""
  fi

  if [ "$DUMP_KIBANA_ES" ] && [ ! -e "$STT_LOGS/EXPORT_ES_TEMPLATES.OK" ]; then
      echo "$LINE_SEPARATOR"
      echo "#### Dump Elasticsearch templates : "`date +'%F %R'`
      $BUILD_HOME/scripts/es_templates_export.py $REP_LOGS/es_templates_export
      ok_log EXPORT_ES_TEMPLATES $?
      echo ""
  fi

  if [ "$DUMP_KIBANA_ES" ] && [ ! -e "$STT_LOGS/EXPORT_KIBANA.OK" ]; then
      echo "$LINE_SEPARATOR"
      echo "#### Dump .kibana index : "`date +'%F %R'`
      $BUILD_HOME/scripts/kibana_export.py $REP_LOGS/kibana_export
      ok_log EXPORT_KIBANA $?
      echo ""
  fi

  echo "$LINE_SEPARATOR"
  echo "#### Rebuild AFFECTATION Mapping from ldap : "`date +'%F %R'`
  rm -f $CONF_PATH/maps/map-supannEntiteAffectationReadable.yml
  rm -f $CONF_PATH/maps/map-supannEntiteAffectationNiveau.yml
  rm -f $CONF_PATH/maps/map-supannEntiteAffectationParent.yml
  $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/gen_mappings/aff
  echo  ""

  echo "$LINE_SEPARATOR"
  echo "#### Rebuild VET Mapping from ldap : "`date +'%F %R'`
  rm -f $CONF_PATH/maps/map-vetReadable.yml
  $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/gen_mappings/vet
  rm -f $CONF_PATH/maps/map-vet-niveau.yml
  $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/gen_mappings/vet-niveau
  echo  ""

  echo "$LINE_SEPARATOR"
  echo "#### Rebuild VDI Mapping from ldap : "`date +'%F %R'`
  rm -f $CONF_PATH/maps/map-vdiReadable.yml
  $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/gen_mappings/vdi
  echo  ""

  #
  # On teste qu'on a réussi à accéder au ldap en vérifiant le nombre d'entrées dans le fichier de mapping
  #
  if [ -s $CONF_PATH/maps/map-supannEntiteAffectationReadable.yml ]; then
    NB_LINES_AFF=`wc -l < $CONF_PATH/maps/map-supannEntiteAffectationReadable.yml`
    if [ "$NB_LINES_AFF" -gt 1000 ]; then

      ok_log LDAP

      echo "$LINE_SEPARATOR"
      echo "#### Delete yesterday index ldap : "`date +'%F %R'`
      curl --silent -XDELETE 'http://localhost:9200/ldap/'
      echo ""
      echo ""

      echo "$LINE_SEPARATOR"
      echo "#### Import du LDAP : "`date +'%F %R'`
      $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/importLDAP
      # On attend que tout soit flushé avant de poursuivre
      sleep 60
    else
      # L'index LDAP n'a pas pu être généré. On supprime le fichier de validation du traitement
      rm -f $STT_LOGS/AG.OK
    fi
  else
    # L'index LDAP n'a pas pu être généré. On supprime le fichier de validation du traitement
    rm -f $STT_LOGS/AG.OK
  fi
fi


if [ -e "$STT_LOGS/LDAP.OK" ]; then
  if [ ! -e "$STT_LOGS/LDAPSTATS.OK" ]; then
    echo "$LINE_SEPARATOR"
    echo "#### Génération ldap-stats : "`date +'%F %R'`
    # Génération de ldap-stat
    $LOGSTASH_DIR/ldap-agg.py
    ok_log LDAPSTATS $?
  fi
fi


if [ -e "$STT_LOGS/LDAP.OK" ]; then
  # Il faut que le LDAP soit chargé pour pouvoir enrichir



    ####
    #
    # Enregistrement des traces
    #
    ####

    if [ ! -e "$STT_LOGS/CAST.OK" ]; then
      if [ -s "$REP_LOGS/trace-cas.log.gz" ]; then
        echo "$LINE_SEPARATOR"
        echo "#### Import CAS trace : "`date +'%F %R'`
        echo "#### Number line in file $DATE/trace-cas.log.gz : "`zcat $REP_LOGS/trace-cas.log.gz | wc -l`
        zcat $REP_LOGS/trace-cas.log.gz | $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/trace
        # Vérification que les traces sont enregistrées sur l'ensemble des shards (ici 3)
        nb_shards=3
        for retry in {1..6}
        do
            nb_shards_ok=`curl --silent -XPOST 'http://localhost:9200//trace/_flush/synced?pretty' | grep '"successful" : ' | awk '{print substr($3,1,1);exit;}'`
            if [ $nb_shards_ok -eq $nb_shards ]
            then
                break;
            else
                echo "Le flush des traces n° $retry n'a pas fonctionné ($nb_shards_ok/$nb_shards shards OK). On marque une pause"
                sleep 20
            fi
        done

        ok_log CAST $?
        echo ""
      else
          wait_log CAST
      fi
    fi

    ########
    # Attention, les traitements utilisant les traces agimus doivent avoir un test [ -e "$STT_LOGS/CAST.OK" ]
    #   pour s'assurer que les traces ont été importées
    #


    ####
    #
    # Traitement des logs onlyoffice
    #
    ####

    if [ -e "$STT_LOGS/CAST.OK" ] && [ ! -e "$STT_LOGS/ONLY.OK" ]; then
      if [ -s "$REP_LOGS/access_onlyoffice" ]; then
        echo "$LINE_SEPARATOR"
        echo "#### Import onlyoffice logs : "`date +'%F %R'`
        echo "#### Number line in file $DATE/access_onlyoffice : "`cat $REP_LOGS/access_onlyoffice | wc -l`
        cat $REP_LOGS/access_onlyoffice | $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/onlyoffice
        ok_log ONLY $?
        echo ""
      else
        wait_log ONLY
      fi
    fi


    ####
    #
    # Traitement des logs Moodle
    #
    ####

    if [ ! -e "$STT_LOGS/CMDL.OK" ]; then
        if [ -s "$REP_LOGS/coursMoodle.log" ]; then
            echo "$LINE_SEPARATOR"
            echo "#### Import coursMoodle logs : "`date +'%F %R'`
            echo "#### Number line in file $DATE/coursMoodle.log : "`cat $REP_LOGS/coursMoodle.log | wc -l`
            ## On ajoute en fin de ligne un timestamp postérieur pour être certain que les cours soient considérés comme du jour
            sed "s/$/;[time:`date --date="$DATE +5 hours" +%s`]/"  $REP_LOGS/coursMoodle.log | $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/moodle-cours
            ok_log CMDL $?
            ## Ajout de l'alias moodle-cours-courant qui sera utilisé par moodle-from-db
            ancien=`curl -XGET "http://localhost:9200/_alias/ag-moodlecours-courant" 2>/dev/null | awk -F\" '{print $2}'`;
            curl --silent -XPOST "http://localhost:9200/_aliases" -H 'Content-Type: application/json' -d"{\"actions\": [{\"remove\": {\"index\": \"${ancien}\", \"alias\": \"ag-moodlecours-courant\" } }, {\"add\": {\"index\": \"ag-moodlecours-${DATE::4}.${DATE:4:2}\", \"alias\": \"ag-moodlecours-courant\" } }]}"
            echo ""
        else
            wait_log CMDL
        fi
    fi

    if [  $REBUILD -a ! -e "$STT_LOGS/MDL.OK" ]; then
      echo "$LINE_SEPARATOR"
      echo "#### Traitement des logs Moodle : "`date +'%F %R'`
      $BUILD_HOME/scripts/traitement-moodle.sh $DATE
      ok_log MDL $?
      if [ -s $LOG_DIR/import_moodle.log ] && [[ `wc -l $LOG_DIR/import_moodle.log` -gt 1 ]]; then
        # On crée une alerte si le log du traitement moodle n'est pas vide
        touch $STT_LOGS/MDL.ERROR
        echo -e "#########################################\n#  ATTENTION : Il y a des erreurs \n#########################################"
        sed '1d' $LOG_DIR/import_moodle.log
      fi
    fi

    ####
    #
    # Traitement des logs Antispam Renater
    #
    ####

    if [ ! -e "$STT_LOGS/ASRENATER.OK" ]; then
        if [ -s "$REP_LOGS/ASrenater.log.bz2" ]; then
          echo "$LINE_SEPARATOR"
          echo "#### Import Antispam Renater logs : "`date +'%F %R'`
          echo "#### Number line in file $DATE/ASrenater.log.bz2 : "`bzcat $REP_LOGS/ASrenater.log.bz2 | wc -l`
          # On ajoute l'année aux débuts de lignes de log
          bzcat $REP_LOGS/ASrenater.log.bz2 | sed "s/.*/`date --date="$DATE -1day" +%Y`-&/" | $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/renaterantispam
          ok_log ASRENATER $?
          echo ""
        else
            wait_log ASRENATER
        fi
    fi


    ####
    #
    # Traitement des infos nextcloud
    # Les infos sont générées par le script cron_stats_nc.sh lancé sur la machine nextcloud
    #
    ####

    if [ ! -e "$STT_LOGS/NC.OK" ]; then
        if [ -s "$REP_LOGS/nc-stats.log" ]; then
          echo "$LINE_SEPARATOR"
          echo "#### Import nextcloud logs : "`date +'%F %R'`
          echo "#### Number line in file $DATE/nc-stats.log : "`cat $REP_LOGS/nc-stats.log | wc -l`
          ## On ajoute en fin de ligne un timestamp postérieur pour être certain que les logs soient considérés comme du jour
          sed "s/$/;[time:`date --date="$DATE +5 hours" +%s`]/" $REP_LOGS/nc-stats.log | $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/nextcloud
          ok_log NC $?
          echo ""
        else
            wait_log NC
        fi
    fi


    ####
    #
    # Enrichissement des fichiers ezProxy
    # Ils seront stockées sur le serveur ezPaarse pour pouvoir refaire les enrichissements ezPaarse
    # si de nouveaux parseurs sont créés
    # le traitement des données ezPaarse est exécuté indépendamment dans le script traitement-ezpaarse.sh
    #
    ####

    if [ ! -e "$STT_LOGS/EZPROXY.OK" ]; then
       echo "$LINE_SEPARATOR"
       echo "#### Traitement logs ezProxy : "`date +'%F %R'`
       $BUILD_HOME/bin/traitement-ezproxy.sh $DATE
       ok_log EZPROXY $?
       echo ""
    fi

    ####
    #
    # Traitement des logs IDP shibboleth
    #
    ####

    if [ ! -e "$STT_LOGS/IDP.OK" ]; then
        if [ -s "$REP_LOGS/idp-audit.log"  ]; then
          echo "$LINE_SEPARATOR"
          echo "#### Import IDP logs : "`date +'%F %R'`
          echo "#### Number line in file $DATE/idp-audit.log : "`cat $REP_LOGS/idp-audit.log | wc -l`
          cat $REP_LOGS/idp-audit.log | $LOGSTASH_DIR/logstash --quiet -f $CONF_DIR/idp
          ok_log IDP $?
          echo ""
        else
            wait_log IDP
        fi
      fi

    ####
    #
    # Nettoyage des traces anciennes
    #
    ####

    echo "$LINE_SEPARATOR"
    echo "#### Clean ES index : CAS-TRACE older than 15 days : "`date +'%F %R'`
    curl --silent -XPOST 'http://localhost:9200/trace/_delete_by_query'  -H 'Content-Type: application/json' -d '{"query": {"range": {"@timestamp": {"lt": "now-15d"} } }}'
    echo ""
    echo ""

    echo -e "|\n|   End of the process (pid:"$pid") : "`date +'%F %R'`
    echo "$LINE_SEPARATOR_START"
    echo ""

fi

rm -f $STT_LOGS/AG.LOCK

# Dernier traitemant en anomalie
H=`date "+%H"`
if [ $H -ge $MAX_WAIT_LOG_TIME ] ||  [ -e "$STT_LOGS/AG.OK" ] || [ ! $REBUILD ]; then
  # Y a-t-il des fichiers erreur ou wait
  err_exist=`find  $STT_LOGS -regex '.*\.ERROR\|.*\.WAITLOG' -exec basename {} \;`
	if [ $(sed '/^warning.*report_on_exception is true):/{:a;N;/thread_pool.rb:218$/!ba};/ConcurrencyError/d' $ERROR_LOG | wc -l ) -ne 0  ] || [ -n "$err_exist" ] ; then
	    # On supprime les balises html du fichier pour qu'il ne soit pas envoyé en PJ
	    sed -i 's/<[^>]*>/Balise_XML_retiree/g' $INFO_LOG
      if [ -n "$err_exist" ]; then
        mail_body="`printf "\n## Attention \n#\nDes traitements sont en erreur ou ne sont pas terminés :\n${err_exist}\n\n\n|"` $(cat $INFO_LOG)"
      else
        mail_body=$(cat $INFO_LOG)
      fi
	    export CONTENT_TYPE="text/plain"
	    echo "$mail_body" | /bin/mail  -S sendcharsets=UTF-8 -s "$(echo -e "Rapport AGIMUS ("$DATE") traité le "$(date '+%d/%m/%Y'))" $MAIL_DEST

	fi

fi
