# Fichiers de configuration logstash

Les fichiers de configurations logstash contenus dans ce dossier sont des templates qui doivent être adaptés au besoin de votre établissement.

Les configurations logstash peuvent être un fichier contenant toutes les étapes du traitement ou un dossier dont les fichiers de configuration seront traités de manière séquentielle.

Pour paramétrer les attributs issus du LDAP qui permettront d'enrichir vos logs, modifiez le fichier [modulesBase/inputs/LDAP](modulesBase/inputs/LDAP)

Chaque dossier correspond à un traitement de logs spécifique à une application.
Les modifications qui se retrouvent dans plusieurs traitements sont centralisées dans le dossier modulesBase et peuvent être ajoutées par un simple lien symbolique dans le dossier que vous créez. Il peut être nécessaire de nommer le ou les champs à traiter de manière spécifique pour que le module fonctionne.

Vous trouverez un exemple dans [traitement_vierge](traitement_vierge) qui peut vous servir de base pour vos nouvelles créations. Copier le en conservant les liens symboliques et apportez les modifications nécessaires.

Exemple d'ajout d'une application utilisant le cookie trace. On ajoute un lien symbolique vers trace_es pour convertir les traces en le traitant avant (30) ldap_es (40) qui utilise le login pour enrichir. On édite ensuite le module grok pour découper les logs et le module index pour définir le nom de l'index qui sera créé.

    cp -Pr traitement_vierge nouvelle_app
    cd nouvelle_app
    ln -s ../modulesBase/filters/trace_es 30_trace_es
    vim 10_grok
    vim 90_index

N'hésitez pas à partager avec la communauté (à travers un pull request ou sur la liste de diffusion esup-utilisateurs@esup-portail.org) l'intégration de nouvelles applications. Votre travail peut servir à d'autres.
