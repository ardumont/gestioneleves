-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 1.8.0
-- Mode                     : UPDATE
-- =============================================================================

-- Structure de la table des profils
--#STEP()
DROP TABLE IF EXISTS PROFILS;
CREATE TABLE PROFILS
(
	PROFIL_ID		INT UNSIGNED	NOT NULL	AUTO_INCREMENT,
	PROFIL_NAME		VARCHAR(30)		NOT NULL,
	PROFIL_COMMENT	TEXT			NULL,

	PRIMARY KEY	(PROFIL_ID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;

-- Structure de la table des droits pour les profils
--#STEP()
DROP TABLE IF EXISTS PROFILS_REL_RIGHTS;
CREATE TABLE PROFILS_REL_RIGHTS
(
	PROFIL_ID		INT UNSIGNED	NOT NULL,
	PROFIL_RIGHT	VARCHAR(30)		NOT NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;

-- Contraintes pour la table PROFILS_REL_RIGHTS
--#STEP()
ALTER TABLE PROFILS_REL_RIGHTS
	ADD CONSTRAINT FK_REL_PROFILS FOREIGN KEY (PROFIL_ID) REFERENCES PROFILS(PROFIL_ID) ON DELETE CASCADE ON UPDATE CASCADE;

-- Ajoute la colonne du profil
--#STEP()
ALTER TABLE PROFESSEURS
 ADD COLUMN `PROFESSEUR_PROFIL_ID` INT UNSIGNED NOT NULL;

--#STEP(TRANSACTION)
-- Le profil de l'administrateur général
INSERT INTO PROFILS
	(PROFIL_ID, PROFIL_NAME, PROFIL_COMMENT)
VALUES
	(1, 'Administrateur', 'Profil de l''administrateur général de l''application');

--#STEP(TRANSACTION)
UPDATE PROFESSEURS
SET PROFESSEUR_PROFIL_ID=1;

