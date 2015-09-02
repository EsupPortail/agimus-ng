--
-- Base de données: `agimus-ng`
--

-- --------------------------------------------------------

--
-- Structure de la table `dashboard`
--

CREATE TABLE IF NOT EXISTS `dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `roles` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Structure de la table `dashboard_graphe`
--

CREATE TABLE IF NOT EXISTS `dashboard_graphe` (
  `dashboard_id` int(11) NOT NULL,
  `graphe_id` int(11) NOT NULL,
  PRIMARY KEY (`dashboard_id`,`graphe_id`),
  KEY `IDX_8F8DA89BB9D04D2B` (`dashboard_id`),
  KEY `IDX_8F8DA89B2A270321` (`graphe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `graphe`
--

CREATE TABLE IF NOT EXISTS `graphe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url` longtext COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `roles` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_USERNAME` (`username`),
  UNIQUE KEY `UNIQ_EMAIL_USER` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `dashboard_graphe`
--
ALTER TABLE `dashboard_graphe`
  ADD CONSTRAINT `FK_8F8DA89B2A270321` FOREIGN KEY (`graphe_id`) REFERENCES `graphe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_8F8DA89BB9D04D2B` FOREIGN KEY (`dashboard_id`) REFERENCES `dashboard` (`id`) ON DELETE CASCADE;