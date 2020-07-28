# Fichiers de configuration logstash

Les fichiers de configurations logstash contenus dans ce dossier sont des templates qui doivent être adaptés au besoin de votre établissement.

Pour paramétrer les attributs issus du LDAP qui permettront d'enrichir vos logs, modifiez le fichier [modulesBase/inputs/LDAP](modulesBase/inputs/LDAP)

Chaque dossier correspond à un traitement de logs spécifique à une application.
Les traitements qui se retrouvent dans plusieurs traitements sont dans le dossier modulesBase et peuvent être ajouter par un simple lien symbolique dans le dossier que vous créez.
Vous trouverez un exemple dans traitement_vierge qui peut vous servir de bases pour vos nouvelles créations.

N'hésitez pas à partager avec la communauté (à travers un pull request ou sur la liste de diffusion esup-utilisateurs@esup-portail.org) l'intégration de nouvelles applications. Votre travail peut servir à d'autres.
