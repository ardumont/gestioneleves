-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 2.3.0
-- Mode                     : UPDATE
-- =============================================================================

-- =============================================================================
--#TITLE(Création des tables)

-- Structure de la table EVAL_COMPETENCES
--#STEP()
CREATE TABLE IF NOT EXISTS `EVAL_COMPETENCES` (
  `ID_EVAL_COL` int(11) NOT NULL,
  `ID_COMPETENCE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- =============================================================================
--#TITLE(Contraintes pour les tables)

-- Contraintes pour la table EVAL_COMPETENCES
--#STEP()
ALTER TABLE `EVAL_COMPETENCES`
  ADD CONSTRAINT `EVAL_COMPETENCES_ibfk_1` FOREIGN KEY (`ID_EVAL_COL`) REFERENCES `EVALUATIONS_COLLECTIVES` (`EVAL_COL_ID`) ON DELETE CASCADE;

-- Contraintes pour la table EVAL_COMPETENCES
--#STEP()
ALTER TABLE `EVAL_COMPETENCES`
  ADD CONSTRAINT `EVAL_COMPETENCES_ibfk_2` FOREIGN KEY (`ID_COMPETENCE`) REFERENCES `COMPETENCES` (`COMPETENCE_ID`) ON DELETE CASCADE;
