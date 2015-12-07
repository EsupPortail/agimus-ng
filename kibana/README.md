# Utilisation de kibana 4
## Visualisation de vos données via kibana
Les fichiers contenus dans ce dossier sont des configurations pour visualiser vos indicateurs dans kibana4. N'hésitez pas à partager ceux que vous avez pu créer.


## Import des visualisations
Les fichiers de configurations contenus dans le sous-dossier [visualization](visualization) doivent être importés dans elasticsearch grâce à une des deux méthodes ci-dessous.

### Avec le script d'import [../scripts/kibana_import.py](kibana_import.py)
Nous supposons ici que vous souhaitez importer tout le contenu du dossier visualization de ce dépôt mais il vous suffit de modifier le dossier pour importer son contenu en tant que visualisations.

    scripts/kibana_import.py kibana/visualization visu


### Avec curl
Nous supposons ici que vous exécutez la commande sur le serveur elasticsearch et que vous utilisez le nom d'index par défaut de kibana4 (.kibana)

    curl -XPUT "http://localhost:9200/.kibana/visualization/FICHIER_A_IMPORTER" -d @visualization/FICHIER_A_IMPORTER.json


## Import des dashboards
Les fichiers de configurations contenus dans le sous-dossier [dashboard](dashboard) doivent être importés dans elasticsearch grâce à la ligne de commande ci-dessous.

### Avec le script d'import [../scripts/kibana_import.py](kibana_import.py)
Nous supposons ici que vous souhaitez importer tout le contenu du dossier dashboard de ce dépôt mais il vous suffit de modifier le dossier pour importer son contenu en tant que tableaux de bord.

    scripts/kibana_import.py kibana/dashboard dash


### Avec curl
Nous supposons ici que vous exécutez la commande sur le serveur elasticsearch et que vous utilisez le nom d'index par défaut de kibana4 (.kibana)

    curl -XPUT "http://localhost:9200/.kibana/dashboard/FICHIER_A_IMPORTER" -d @dashboard/FICHIER_A_IMPORTER.json


## Exporter vos propres configurations
Il peut être intéressant d'exporter vos propres visualisations ou dashboards kibana pour créer des sauvegardes ou encore partager avec la communauté le travail réalisé.

### Avec le script d'export [../scripts/kibana_export.py](kibana_export.py)
Le script fourni permet d'exporter l'ensemble des visualisations et tableaux de bord contenu dans votre kibana. L'export se fait dans 2 dossiers tagués avec la date du jour dans le dossier kibana.

    scripts/kibana_export.py


### Avec curl

Pour extraire toutes vos configurations, utilisez la commande suivante pour extraire l'ensemble du contenu (2000 éléments ici mais vous pouvez augmenter la valeur si nécessaire). 

    curl -XGET "http://localhost:9200/.kibana/_search?pretty" -d'
    {
        "size": 2000
    }'

Pour extraire une visualisation en particulier :

    curl -XGET "http://localhost:9200/.kibana/visualization/ID_VISU_A_EXPORTER?pretty"

Les fichiers des sous-répertoires de ce dépôt sont l'extrait "_source" du résultat obtenu

