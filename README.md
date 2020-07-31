# Agimus nouvelle génération ou Agimus-NG
<img src="https://www.esup-portail.org/sites/default/files/logo-esupportail_1.png"/>

## Version 7.x

| :warning: Version bêta, n'hésitez pas à faire des retours sur la [liste de diffusion](https://listes.esup-portail.org/sympa/info/esup-utilisateurs)  |
| --- |

Cette version, en version bêta, est prévue pour être utilisée avec une version 7.x d'ELK (Elasticsearch, Logstash et Kibana.
Utilisez la branche 2.x d'agimus si vous souhaitez utiliser la version 2 des briques ELK.

## Vers un audimat des services.

Qui utilise quel service ? Quand ? À quel rythme ? L’intérêt des indicateurs mis en place par l’atelier est de mieux connaître l’usage des différents services qui sont déployés dans les établissements et par le fait de mieux en organiser l’accès et de les faire évoluer.
Aujourd'hui, l’objectif est d’harmoniser ces indicateurs pour pouvoir les exploiter à des échelles régionales et nationales.

Objectifs :
 - Définition du besoin d'indicateurs dans nos établissements
 - Établir le cadrage pour le changement vers Agimus-NG
 - Réaliser et suivre la progression des travaux autour de l'outil
 - Tester et implémenter la solution
 - Faire la promotion et le suivi de Agimus-NG

## Documentation du projet

Obtenir plus d'informations sur le projet, merci de consulter la [page wiki du projet](https://www.esup-portail.org/wiki/x/DQCfFg)
Vous avez des questions ? Vous êtes intéressé par le projet, n'hésitez pas à poser vos questions sur la [liste de diffusion](https://listes.esup-portail.org/sympa/info/esup-utilisateurs)

## Contenu de ce dépôt

Vous trouverez ici les principaux fichiers utilisés pour mettre en place Agimus-NG séparés suivant leur utilité dans les dossiers :
* [scripts](scripts) : contient les scripts utiles au traitement régulier des logs
* [logstash](logstash) : contient les configurations logstash de traitement des logs
* [kibana](kibana) : contient des exemples de configuration kibana basés utilisant les données obtenues à partir des configurations logstash présentées ici

Chaque dossier contient à sa racine une rapide description de son contenu mais pour obtenir plus de détails, rendez vous sur la page wiki du projet.
