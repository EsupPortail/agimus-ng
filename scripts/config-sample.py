# -*- coding: utf-8 -*-

# Tuple contenant les noeuds du cluster elasticsearch
cluster_ES = ['http://agimus1.univ.fr:9200/',
					'http://agimus2.univ.fr:9200/']

# URI d'accès à l'API saved_objects de kibana
uri_saved_objects = 'http://agimus.univ.fr:5701/api/saved_objects'

# Objets kibana à exporter
objectsToExport = [ 'visualization', 'dashboard', 'index-pattern', 'query', 'config']

# Liste des attributs à synthétiser dans l'ordre d'imbrication des agrégations
ldapAttrs = [ 'eduPersonPrimaryAffiliation', 'supannEntiteAffectationPrincipale', 'supannEtuInscription', 'supannEtuSecteurDisciplinaire' ]
