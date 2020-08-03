#!/bin/bash

REP="/home/agimus/scripts/pageres"
TMP="$REP/tmp"
SHOTS="shots"
FSHOTS="$REP/$SHOTS"
FRONTAL="http://indicateurs.univ.fr/"
# Modifier le token ici et dans la configuration du frontal
TOKEN="KDjhkjj77Sghd545JkHNdkkj"

echo " ------------ Debut" > $TMP/shotlog.log

if [ -e $TMP/screenshot.run ]; then
   exit 0
fi

if [ ! -d $TMP ]; then
   mkdir $TMP
fi
if [ ! -d $SHOTS ]; then
   mkdir $SHOTS
fi

touch $TMP/screenshot.run

curl --silent  $FRONTAL"index.php/get_export?token="$TOKEN > $TMP/ag_screenshots.csv


cat $TMP/ag_screenshots.csv | while IFS=  read -r  EXPORT
do
   ID=$( echo $EXPORT | cut -d';' -f1 )

   echo " Traite $ID" >> $TMP/shotlog.log

   re='^[0-9]+$'
   if  [[ $ID =~ $re ]] ; then
      DEBUT=`date -d $( echo $EXPORT | cut -d';' -f2 ) +%d/%m/%Y`
      FIN=`date -d $( echo $EXPORT | cut -d';' -f3 ) +%d/%m/%Y`
      # On passe l'url en http au lieu de https
      URL=$( echo $EXPORT | cut -d';' -f5 | sed 's/^https:/http:/')
      EMAIL=$( echo $EXPORT | cut -d';' -f6 )
      DEMAND=$( echo $EXPORT | cut -d';' -f9 )
      DEMANDHR=`date -d $( echo ${DEMAND% *}) +%d/%m/%Y`
      TITLE=$( echo $EXPORT | cut -d';' -f11 )

   	echo "ID:$ID URL:$URL FILE:$SHOTS/$EMAIL/$TITLE-$DEMAND " >> $TMP/shotlog.log

      if [ ! -d "$FSHOTS/$EMAIL" ]; then
         mkdir -p "$FSHOTS/$EMAIL"
      fi

      #$REP/screenshot.sh "$URL" "$SHOTS/$EMAIL/$TITLE-$DEMAND"  &>> $TMP/shotlog.log
      pageres "$URL" -d50 --overwrite --filename="pageres/$SHOTS/$EMAIL/$TITLE-$DEMAND" &>> $TMP/shotlog.log

      echo "Bonjour,

Vous avez demandé un export du tableau de bord agimus \"${TITLE}\" pour les événements entre le ${DEBUT} et le ${FIN} (demande faite le ${DEMANDHR})
L'image se trouve en pièce jointe.

En cas de soucis, n'hésitez pas à contacter l'équipe en charge de l'outil à l'adresse agimus-contact@univ.fr

Cordialement,

L'équipe Agimus

	" | mailx -s "Capture d'écran Agimus $TITLE" -S replyto="Agimus <agimus-contact@univ.fr>" -a "$FSHOTS/$EMAIL/$TITLE-$DEMAND.png" $EMAIL

      # On indique que l'export a été réalisé
      curl --silent  $FRONTAL"index.php/close_export/$ID?token="$TOKEN

   fi

done

rm $TMP/screenshot.run



# FIN DU SCRIPT
