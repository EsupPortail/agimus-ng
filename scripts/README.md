# Scripts utiles au fonctionnement d'Agimus-NG

## deploy_univ.py
Ce script va vous permettre de modifier automatiquement les fichiers de ce dépôt pour y intégrer les attributs LDAP que vous souhaitez suivre via Agimus-NG
Il nécessite d'être paramétrer en indiquant les attributs à prendre en compte (en début de fichier)
Son exécution générera un répertoire build qui contiendra les fichiers modifiés.

## ldap-agg.py
Ce fichier est un fichier template qui sera disponible complété dans le répertoire build.
Il permet de faire un suivi temporel de l'évolution des populations du ldap.

## daily_batch.sh
Exemple de script permettant de lancer l'ensemble des traitements quotidiens nécessaires au fonctionnement d'Agimus-NG.
Son utilisation suppose que les fichiers de log des applications à suivre ont été copiés en local.

## kibana4.service
File to make automatic start of kibana4 on centos

## kibana4_init
script initd to make automatic start of kibana4 on debian

## update-attributes.sh
script to update, add, remove attributes form an existing eslactisearch index
Use ../logstash/logstash-update-attributes-TEMPLATE.conf file

## kibana_import.sh
Script to import a folder of visualizations or dashboards in Kibana.

## kibana_export.py
Script to export quickly all the dashboards and the visualizations in folders.
