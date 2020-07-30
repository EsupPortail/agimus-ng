#!/bin/bash

# Script de récupération des stats RC pour agimus. A lancer via crontab :
#
##Génération des stats pour agimus
#15 * * * * /home/scripts/rocketstats/rocketchat-stats.sh >> /home/scripts/rocketstats/logs/rocketstats-connectedUsers.log 2> /dev/null
#7 6 * * * /home/scripts/rocketstats/rocketchat-stats.sh stats > /home/scripts/rocketstats/logs/stats_rc_$(date  +"\%Y.\%m.\%d")
#10 6 * * * /home/scripts/transfert_agimus_firestorm.sh >/dev/null 2>&1
#

LOGIN='login_rc'
PWD='mdp_hyper_secret'
RC_URL="https://rocketchat.univ.fr/"

# Authentification pour récupérer les tokens d'authentification
AUTH=`curl -s ${RC_URL}api/v1/login -d "user=$LOGIN&password=$PWD"`
read -r USERID AUTHTOKEN <<< $(echo $AUTH | sed 's/.*"userId":"\([^"]*\)".*"authToken":"\([^"]*\)".*/\1 \2/')

# Récupération des statistiques
STATS=`curl -s -H "X-Auth-Token: ${AUTHTOKEN}" -H "X-User-Id: ${USERID}" ${RC_URL}api/v1/statistics`

RES="requestdate="`date +"%Y-%m-%d %H:%M:%S;"`

if [ "$1" == "stats" ]
then
        # On veut les statistiques complètes de la journée
        arr=("totalRooms" "totalChannels" "totalPrivateGroups" "totalDirect" "totalLivechat" "totalMessages" "totalChannelMessages" "totalPrivateGroupMessages" "totalDirectMessages" "totalLivechatMessages" "totalUsers" "totalConnectedUsers")
else
        # Par défaut, on n'affiche le nb d'utilisateurs connectés à cet instant précis
        arr=("totalConnectedUsers")
fi

for i in "${arr[@]}"
do
        VAR=`echo $STATS | grep -Po '(?<="'${i}'":)[0-9]*'`
        RES+="rc_${i}=${VAR};"
done
echo $RES
