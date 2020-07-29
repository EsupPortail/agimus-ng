#!/bin/bash

###
# Script permettant de générer les infos sur le serveur nextcloud
# A lancer quotidiennement sur le serveur nextcloud :
# 00 1 * * * /home/www/scripts/cron_stats_nc.sh > /home/www/nextcloud/infos/nc-stats.log
#


DATA="/nfs/nexcloud/data"
DB_HOST="ncdb.univ.fr"
DB_USER="ncread"
DB_PWD="XXXXX"
DB="nc"

function echo_du {
	#Premier argument : uid
	DU_UID=$1
	FILES_DIR=$DATA/$DU_UID/files
	if [ -d $FILES_DIR ]
	then
		DU=`du -sb $FILES_DIR|cut -f 1`
		FILE=`find $FILES_DIR -type f|wc -l`
		echo -ne "$DU;$(echo_quota $DU_UID);$FILE\n"
	else
		echo "$(date '+%F %X') : L'utilisateur ${DU_UID} n'a pas de répertoire sur le FS (${FILES_DIR})" >> cnx.error.log
		echo -ne "0;$(echo_quota $DU_UID);0\n"
	fi
}

function echo_quota {
  #Premier argument : uid
  QUOTA_UID=$1

	QUOTA=`sudo -u apache php /home/www/nextcloud/console.php user:info ${QUOTA_UID} | grep quota | awk '{print $3*1073741824}'`
	echo $QUOTA
}

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
