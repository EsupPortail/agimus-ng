# -*- coding: utf-8 -*-

# Tuple contenant les noeuds du cluster elasticsearch
cluster_ES = ['http://agimus1.univ.fr:9200/',
					'http://agimus2.univ.fr:9200/']

# Index kibana à utiliser pour les imports-exports
index_kibana = '.kibana'

# Objets kibana à exporter
objectsToExport = [ 'visualization', 'dashboard', 'index-pattern', 'query', 'config']

# Liste des attributs à synthétiser dans l'ordre d'imbrication des agrégations
ldapAttrs = [ 'eduPersonPrimaryAffiliation', 'supannEntiteAffectationPrincipale', 'supannEtuInscription', 'supannEtuSecteurDisciplinaire' ]
