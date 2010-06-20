-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 2.2.0
-- Mode                     : UPDATE
-- =============================================================================

-- =============================================================================
--#TITLE(Création des tables)

-- Structure de la table HIDDEN_OBJECTS
--#STEP()
DROP TABLE IF EXISTS `HIDDEN_OBJECTS`;
CREATE TABLE `HIDDEN_OBJECTS` (
  `HO_LIBELLE` varchar(40) NOT NULL,
  `ID_PROFESSEUR` int(11) NOT NULL,
  PRIMARY KEY  (`HO_LIBELLE`, `ID_PROFESSEUR`),
  KEY `ID_PROFESSEUR` (`ID_PROFESSEUR`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- =============================================================================
--#TITLE(Contraintes pour les tables)

-- Contraintes pour la table HIDDEN_OBJECTS
--#STEP()
ALTER TABLE `HIDDEN_OBJECTS`
  ADD CONSTRAINT `HO_ibfk_1` FOREIGN KEY (`ID_PROFESSEUR`) REFERENCES `PROFESSEURS` (`PROFESSEUR_ID`) ON DELETE CASCADE;
