-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 2.1.0
-- Mode                     : UPDATE
-- =============================================================================

-- =============================================================================
--#TITLE(Création des tables)

-- Structure de la table COMM_CONSEIL_MAITRES
--#STEP()
DROP TABLE IF EXISTS `COMM_CONSEIL_MAITRES`;
CREATE TABLE `COMM_CONSEIL_MAITRES` (
  `CCM_ID` int(11) NOT NULL auto_increment,
  `CCM_VALEUR` varchar(250) NOT NULL,
  `ID_ELEVE` int(11) NOT NULL,
  `ID_CLASSE` int(11) NOT NULL,    
  PRIMARY KEY  (`CCM_ID`),
  KEY `ID_ELEVE` (`ID_ELEVE`),
  KEY `ID_CLASSE` (`ID_CLASSE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- =============================================================================
--#TITLE(Contraintes pour les tables)

-- Contraintes pour la table COMM_CONSEIL_MAITRES
--#STEP()
ALTER TABLE `COMM_CONSEIL_MAITRES`
  ADD CONSTRAINT `CCM_ibfk_1` FOREIGN KEY (`ID_ELEVE`) REFERENCES `ELEVES` (`ELEVE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `CCM_ibfk_2` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE;
