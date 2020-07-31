# Utilisation de kibana
## Visualisation de vos données via kibana
Les fichiers contenus dans ce dossier sont des configurations d'exemple pour visualiser vos indicateurs dans kibana. Référez-vous à la documentation Kibana pour découvrir bien davantage de fonctionnalités. N'hésitez pas à partager des configurations que vous avez pu créer.


## Import des objets d'exemple
Les fichiers de configurations sont triés par type : [visualization](visualization) et [dashboard](dashboard)
Ils peuvent être importés dans elasticsearch grâce à une des deux méthodes ci-dessous.
Les objets fournis font référence à des index-pattern spécifiques définis dans notre établissement. Il vous faudra modifier les id pour qu'ils correspondent à celui de votre index-pattern ag-*

### Via l'interface graphique
Vous pouvez les importer directement en allant dans le menu Management > Kibana/Saved Objects puis en cliquant sur Import.

### Avec le script d'import [../scripts/kibana_import.py](kibana_import.py)
Ce script va vous permettre d'importer tous les objets kibana définis dans les fichiers du dossier a_importer

    scripts/kibana_import.py a_importer


## Exporter vos propres configurations
Il peut être intéressant d'exporter vos propres visualisations ou dashboards kibana pour créer des sauvegardes ou encore partager avec la communauté le travail réalisé.

### Avec le script d'export [../scripts/kibana_export.py](kibana_export.py)
Le script fourni permet d'exporter l'ensemble des objets contenus dans votre kibana. L'export se fait dans le dossier passé en paramètre. Les objets sont ventilés par type au sein de ce dossier.

    scripts/kibana_export.py objets_exportes


### Via l'interface graphique
Vous pouvez les exporter directement en allant dans le menu Management > Kibana/Saved Objects. Sélectionnez les objets que vous souhaitez exporter puis cliquez sur Export
