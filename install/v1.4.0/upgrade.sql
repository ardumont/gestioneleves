-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 1.4.0
-- Mode                     : UPDATE
-- =============================================================================

-- Structure de la table des paramètres
--#STEP()
DROP TABLE IF EXISTS PARAMETRES;
CREATE TABLE PARAMETRES
(
	VERSION			VARCHAR(15)	NOT NULL,
	DATE_VERSION	DATETIME	NOT NULL	COMMENT 'Stocke la date de modification d''un fichier critique de l''application pour déterminer si une mise à jour est nécessaire.'
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8;

-- =============================================================================
--#TITLE(Contenu des tables systèmes)

--#STEP(TRANSACTION)

-- La seul ligne de la table paramètre
INSERT INTO PARAMETRES
	(VERSION, DATE_VERSION)
VALUES
	('0.0.0', '0000-00-00 00:00:00');
