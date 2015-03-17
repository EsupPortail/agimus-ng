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
