#! /bin/sh

##########
# Ce traitement est à ajouter au cron en parallèle des traitements agimus.
#  On traite les nouveaux fichiers peu importe leur date car il peut s'agir de retraitement ezPaarse
#   0 8,12,18 * * * /home/agimus/scripts/traitement-ezpaarse.sh
#

BUILD_HOME="/opt/agimus-ng/"
LOGSTASH_DIR="/opt/logstash/bin"
CONF_DIR=$BUILD_HOME"/logstash"

DATA_LOGS=/data/logs
DATE=`date '+%Y%m%d'`
REP_LOGS=$DATA_LOGS/$DATE
EXIT_LOG=$DATA_LOGS"/ezPaarse/${DATE}.log"
fic_lock=$DATA_LOGS"/ezPaarse/EZPAARSE.LOCK"
fic_date_defaut=$DATA_LOGS"/ezPaarse/dernier_traitement_ezPaarse"

MAIL_DEST="agimus-contact@univ.fr"

# Send stderr both to ERROR_LOG and INFO_LOG
touch $EXIT_LOG
exec >>$EXIT_LOG 2>&1

if [ -f $fic_lock ]
then
  echo "${DATE} : Un traitement est en cours. On ne fait rien."
  if [ $# -gt 0 ]
  then
    echo "Le fichier ${*} ne sera pas importé."
  fi
  /bin/mail -S sendcharsets=UTF-8 -s "Rapport AGIMUS Annulation import ezPaarse $(date '+%d/%m/%Y')" $MAIL_DEST < $EXIT_LOG
  exit 0
fi

# On commence le traitement donc on lock pour éviter les doubles traitements
touch $fic_lock

if [ $# -gt 0 ]
then
  # Le fichier est passé en paramètre
  fic_import=`ls ${*}`
  if [ -z "$fic_import" ]
  then
    # Le fichier à traiter n'existe pas
    echo "Le fichier ${fic_import} n'existe pas. Comment veux-tu qu'on l'importe ?
    T'as déjà mangé des sushis au monstre du Loch Ness ? Non ! Ben là c'est pareil, on va pas découper un fichier que certains pensent avoir aperçu sans apporter de preuves réelles de son existence. C'est une université sérieuse ici !"
    rm $fic_lock
    exit 1
  fi
else
  # Pas de paramètre, on vérifie juste s'il y a des nouveautés
  if [ ! -f $fic_date_defaut ]
  then
    echo "Le fichier ${fic_date_defaut} n'existe pas. Comment trouver des fichiers plus récents que rien ?
    On va quand même pas tout réimporter, non mais quand même. Reviens quand tu seras calmé.
    Au fait si le fichier par défaut n'existe pas, tu peux toujours en passer un en argument, je l'importerai."
    rm $fic_lock
    exit 1
  else
    fic_import=`find ${DATA_LOGS} -name ezpaarse-*.csv.gz -newer ${fic_date_defaut}`
    if [ -z "$fic_import" ]
    then
      # Il n'y a pas de fichiers à traiter. On repassera plus tard.
      rm $fic_lock
      exit 0
    fi

    echo -e "On rafraichit le fichier de référence par défaut (${fic_date_defaut})  pour changer la date du dernier traitement.\nOn importe les fichiers postérieurs à ce fichier. Et zou, direction elasticsearch.\n"
    touch $fic_date_defaut

  fi
fi

echo "#### Récupération des logs ezPaarse : "`date +'%F %R'`
for fic in $fic_import
do
    nb_lignes=`zcat ${fic} | sed '1d'| wc -l`
    echo "Import des ${nb_lignes} lignes du fichier ${fic}"
    # On définit un path spécifique pour éviter que plusieurs traitements logstash collisionnent
    zcat $fic | sed '1d' | $LOGSTASH_DIR/logstash --path.data /var/lib/logstash/ezpaarse --quiet -f $CONF_DIR/ezpaarse

done

# On est fier du travail accompli, on supprime le fichier de lock
rm -f $fic_lock
echo -e "\nAyé, j'ai fini. À la prochaine !"

echo ""

# Envoi du mail en fin de traitement
if [ -s $EXIT_LOG ];then
  /bin/mail  -S sendcharsets=UTF-8 -s "Rapport AGIMUS Import ezPaarse ($(date '+%d/%m/%Y'))" $MAIL_DEST < $EXIT_LOG
fi
