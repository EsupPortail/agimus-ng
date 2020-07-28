# Scripts utiles au fonctionnement d'Agimus-NG

## config.py
Permet de paramétrer les scripts python de ce répertoire

## ldap-agg.py
Ce fichier permet de faire un suivi temporel de l'évolution des populations du ldap.
Vous pouvez paramétrer les attributs à suivre dans le fichier config.py

## daily_batch.sh
Exemple de script permettant de lancer l'ensemble des traitements quotidiens nécessaires au fonctionnement d'Agimus-NG.
Son utilisation suppose que les fichiers de log des applications à suivre aient été copiés en local.

## update-attributes.sh
script to update, add, remove attributes form an existing eslactisearch index
Use ../logstash/logstash-update-attributes-TEMPLATE.conf file

## es_template_export.py
Script permettant d'exporter les templates elasticsearch pour effectuer des sauvegardes.
Le paramétrage s'effectue dans config.py

## kibana_export.py
Script permettant d'exporter les objets kibana, notamment pour effectuer des sauvegardes.
Le paramétrage s'effectue dans config.py

## kibana_import.py
Script permettant d'importer des objets préalablement exportés dans Kibana.
Le paramétrage s'effectue dans config.py

## test-elasticsearch.py
Script permettant de tester le fonctionnement basique du plugin elasticsearch pour python
Le paramétrage s'effectue dans config.py

## Autres fichiers
Il existe d'autres fichiers qui peuvent être utiles pour des traitements particuliers (Moodle, Nextcloud, …)
