# front-agimus-ng
Application légère de visualisation de tableaux de bord kibana. 

# Pré requis :
- Serveur LAMP (testé sous debian avec Php 5.4)
- modules curl et mysql pour php

# Installation :

1. Clonez le projet à la racine de votre serveur web
2. Téléchargez et installez phpCas : <https://apereo.atlassian.net/wiki/spaces/CASC/pages/103252517/phpCAS>
3. Créer une base de données et executez le fichier config/bdd.sql pour initialiser les tables dans cette base.
4. Renommez le fichier config/config-sample.php en config/config.php et renseignez le.
5. Connectez vous à l'aide du compte renseigné dans le fichier de config et allez dans l'administration pour commencer à entrer des données.
6. Sécurisez l'accès à kibana en suivant les instructions du wiki ESUP-Portail <https://www.esup-portail.org/wiki/x/DoCQIg>
