-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 1.1.0.0
-- Mode                     : UPDATE
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Ajout du module de connexion
--

ALTER TABLE PROFESSEURS
	ADD COLUMN PROFESSEUR_PWD VARCHAR(50) NOT NULL;

UPDATE PROFESSEURS
 SET PROFESSEUR_PWD = MD5('test');

