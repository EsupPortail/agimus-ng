#!/usr/bin/env bash
# Script permettant la vérification de la présence du plugin LDAPSearch.
# A lancer avant chaque exécution du traitement quotidien (daily_batch.sh)

MAIL_DEST="agimus-contact@univ.fr"

loup_y_es_tu=`/opt/logstash/bin/logstash-plugin list --installed logstash-input-LDAPSearch 2>&1`
if [ 'logstash-input-LDAPSearch' != "${loup_y_es_tu}" ]; then
  /opt/logstash/bin/logstash-plugin install logstash-input-LDAPSearch
  new_loup_y_es_tu=`/opt/logstash/bin/logstash-plugin list --installed logstash-input-LDAPSearch 2>&1`
  corps_mail="
Mes salutations,

Je viens de la planète $(hostname) !

Le plugin LDAPSearch n'était plus installé, il a dû y avoir une mise à jour de logstash.
J'ai fait une réinstallation automatique. Si le plugin n'est toujours pas installé, il va falloir vérifier vous-mêmes l'origine du problème.

Sortie du test :
  ${loup_y_es_tu}

Sortie après installation (il faut que le nom du plugin s'affiche) :
  ${new_loup_y_es_tu}


À la prochaine

Un script qui vous veut du bien ($0)
"
  echo "${corps_mail}" | /bin/mail  -S sendcharsets=UTF-8 -s "LDAPSearch a disparu" "$MAIL_DEST"
fi
