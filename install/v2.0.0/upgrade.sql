-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 2.0.0
-- Mode                     : UPDATE
-- =============================================================================

-- =============================================================================
--#TITLE(Création des tables)

-- Structure de la table COMMENTAIRES
--#STEP()
DROP TABLE IF EXISTS COMMENTAIRES;
CREATE TABLE `COMMENTAIRES` (
  `COMMENTAIRE_ID` int(11) NOT NULL auto_increment,
  `COMMENTAIRE_VALEUR` varchar(200) NOT NULL,
  `ID_PERIODE` int(11) NOT NULL,
  `ID_ELEVE` int(11) NOT NULL,
  `ID_CLASSE` int(11) NOT NULL,    
  PRIMARY KEY  (`COMMENTAIRE_ID`),
  KEY `ID_PERIODE` (`ID_PERIODE`),
  KEY `ID_ELEVE` (`ID_ELEVE`),
  KEY `ID_CLASSE` (`ID_CLASSE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- =============================================================================
--#TITLE(Contraintes pour les tables)

-- Contraintes pour la table COMMENTAIRES
--#STEP()
ALTER TABLE `COMMENTAIRES`
  ADD CONSTRAINT `COMMENTAIRES_ibfk_1` FOREIGN KEY (`ID_PERIODE`) REFERENCES `PERIODES` (`PERIODE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `COMMENTAIRES_ibfk_2` FOREIGN KEY (`ID_ELEVE`) REFERENCES `ELEVES` (`ELEVE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `COMMENTAIRES_ibfk_3` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE;
  
