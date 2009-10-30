-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 1.2.0.0
-- Mode                     : UPDATE
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Mise en place de l'utf8
--

ALTER DATABASE baseeval CHARSET=utf8;

ALTER TABLE CLASSES CHARSET=utf8;
ALTER TABLE COMPETENCES CHARSET=utf8;
ALTER TABLE CYCLES CHARSET=utf8;
ALTER TABLE DOMAINES CHARSET=utf8;
ALTER TABLE ECOLES CHARSET=utf8;
ALTER TABLE ELEVES CHARSET=utf8;
ALTER TABLE ELEVE_CLASSE CHARSET=utf8;
ALTER TABLE EVALUATIONS_COLLECTIVES CHARSET=utf8;
ALTER TABLE EVALUATIONS_INDIVIDUELLES CHARSET=utf8;
ALTER TABLE MATIERES CHARSET=utf8;
ALTER TABLE NIVEAUX CHARSET=utf8;
ALTER TABLE NIVEAU_CLASSE CHARSET=utf8;
ALTER TABLE NOTES CHARSET=utf8;
ALTER TABLE PERIODES CHARSET=utf8;
ALTER TABLE PROFESSEURS CHARSET=utf8;
ALTER TABLE PROFESSEUR_CLASSE CHARSET=utf8;
