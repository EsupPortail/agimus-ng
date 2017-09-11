#!/bin/bash

###
# Script permettant de générer les infos sur le serveur owncloud
# A lancer quotidiennement sur le serveur owncloud :
# 00 1 * * * /home/www/scripts/cron_cnx_oc.sh > /home/www/owncloud/infos/cnx-oc.log
#

DATA="/nfs/owncloud/data"
DB_HOST="ocdb.univ.fr"
DB_USER="ocread"
DB_PWD="XXXXX"
DB="oc"

#du -s retourne les valeurs en K
function echo_du {
        #Premier argument : uid
        DU_UID=$1
        DU=`du -sb $DATA/$DU_UID/files|cut -f 1`
        FILE=`find $DATA/$DU_UID/files -type f|wc -l`
        echo -ne "$DU;$FILE\n"
}

mysql --default-character-set=utf8 -h $DB_HOST -u $DB_USER -p$DB_PWD $DB -e "SELECT uid, displayname FROM oc_users" | while read uid displayname; do

        if [ "$uid" != "uid" -a "$uid" != "admin" ] 
        then
                echo -ne "$uid;$displayname;"
                echo_du $uid
        fi

done
