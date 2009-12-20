/*==============================================================================
Application : GestionEleves
Version     : 1.9.0
Mode        : new_install
OldVersion	: all_or_none
==============================================================================*/

-- =============================================================================
--#TITLE(Désactivation des contraintes)

--#STEP(ALWAYS_RUN)
SET FOREIGN_KEY_CHECKS = 0;

--#STEP(ALWAYS_RUN)
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- =============================================================================
--#TITLE(Création des tables)

-- Structure de la table CLASSES
--#STEP()
DROP TABLE IF EXISTS CLASSES;
CREATE TABLE `CLASSES` (
  `CLASSE_ID` int(11) NOT NULL auto_increment,
  `CLASSE_NOM` varchar(10) NOT NULL,
  `CLASSE_ANNEE_SCOLAIRE` varchar(9) NOT NULL,
  `ID_ECOLE` int(11) NOT NULL,
  PRIMARY KEY  (`CLASSE_ID`),
  KEY `ID_ECOLE` (`ID_ECOLE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table COMPETENCES
--#STEP()
DROP TABLE IF EXISTS COMPETENCES;
CREATE TABLE `COMPETENCES` (
  `COMPETENCE_ID` int(11) NOT NULL auto_increment,
  `COMPETENCE_NOM` varchar(200) NOT NULL,
  `ID_MATIERE` int(11) NOT NULL,
  PRIMARY KEY  (`COMPETENCE_ID`),
  KEY `ID_MATIERE` (`ID_MATIERE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table CYCLES
--#STEP()
DROP TABLE IF EXISTS CYCLES;
CREATE TABLE `CYCLES` (
  `CYCLE_ID` int(11) NOT NULL auto_increment,
  `CYCLE_NOM` varchar(50) NOT NULL,
  PRIMARY KEY  (`CYCLE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table DOMAINES
--#STEP()
DROP TABLE IF EXISTS DOMAINES;
CREATE TABLE `DOMAINES` (
  `DOMAINE_ID` int(11) NOT NULL auto_increment,
  `DOMAINE_NOM` varchar(200) NOT NULL,
  `ID_CYCLE` int(11) NOT NULL,
  PRIMARY KEY  (`DOMAINE_ID`),
  KEY `ID_CYCLE` (`ID_CYCLE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table ECOLES
--#STEP()
DROP TABLE IF EXISTS ECOLES;
CREATE TABLE `ECOLES` (
  `ECOLE_ID` int(11) NOT NULL auto_increment,
  `ECOLE_NOM` varchar(50) NOT NULL,
  `ECOLE_VILLE` varchar(50) NOT NULL,
  `ECOLE_DEPARTEMENT` varchar(5) NOT NULL,
  PRIMARY KEY  (`ECOLE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table ELEVES
--#STEP()
DROP TABLE IF EXISTS ELEVES;
CREATE TABLE `ELEVES` (
  `ELEVE_ID` int(11) NOT NULL auto_increment,
  `ELEVE_NOM` varchar(50) NOT NULL,
  `ELEVE_ACTIF` tinyint(1) default '1',
  `ELEVE_DATE_NAISSANCE` date default NULL,
  PRIMARY KEY  (`ELEVE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table ELEVE_CLASSE
--#STEP()
DROP TABLE IF EXISTS ELEVE_CLASSE;
CREATE TABLE `ELEVE_CLASSE` (
  `ID_ELEVE` int(11) NOT NULL,
  `ID_CLASSE` int(11) NOT NULL,
  PRIMARY KEY  (`ID_ELEVE`,`ID_CLASSE`),
  KEY `ID_CLASSE` (`ID_CLASSE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- Structure de la table EVALUATIONS_COLLECTIVES
--#STEP()
DROP TABLE IF EXISTS EVALUATIONS_COLLECTIVES;
CREATE TABLE `EVALUATIONS_COLLECTIVES` (
  `EVAL_COL_ID` int(11) NOT NULL auto_increment,
  `EVAL_COL_NOM` varchar(50) NOT NULL,
  `EVAL_COL_DESCRIPTION` varchar(200) NOT NULL,
  `EVAL_COL_DATE` date NOT NULL,
  `ID_PERIODE` int(11) NOT NULL,
  `ID_CLASSE` int(11) NOT NULL,
  PRIMARY KEY  (`EVAL_COL_ID`),
  KEY `ID_PERIODE` (`ID_PERIODE`),
  KEY `ID_CLASSE` (`ID_CLASSE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Structure de la table EVALUATIONS_INDIVIDUELLES
--#STEP()
DROP TABLE IF EXISTS EVALUATIONS_INDIVIDUELLES;
CREATE TABLE `EVALUATIONS_INDIVIDUELLES` (
  `EVAL_IND_ID` int(11) NOT NULL auto_increment,
  `EVAL_IND_COMMENTAIRE` varchar(200) NOT NULL,
  `EVAL_IND_DATE` date NOT NULL,
  `ID_COMPETENCE` int(11) NOT NULL,
  `ID_EVAL_COL` int(11) NOT NULL,
  `ID_ELEVE` int(11) NOT NULL,
  `ID_NOTE` int(11) NOT NULL,
  PRIMARY KEY  (`EVAL_IND_ID`),
  KEY `ID_COMPETENCE` (`ID_COMPETENCE`),
  KEY `ID_EVAL_COL` (`ID_EVAL_COL`),
  KEY `ID_NOTE` (`ID_NOTE`),
  KEY `ID_ELEVE` (`ID_ELEVE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table MATIERES
--#STEP()
DROP TABLE IF EXISTS MATIERES;
CREATE TABLE `MATIERES` (
  `MATIERE_ID` int(11) NOT NULL auto_increment,
  `MATIERE_NOM` varchar(200) NOT NULL,
  `ID_DOMAINE` int(11) NOT NULL,
  PRIMARY KEY  (`MATIERE_ID`),
  KEY `ID_DOMAINE` (`ID_DOMAINE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table NIVEAUX
--#STEP()
DROP TABLE IF EXISTS NIVEAUX;
CREATE TABLE `NIVEAUX` (
  `NIVEAU_ID` int(11) NOT NULL auto_increment,
  `NIVEAU_NOM` varchar(50) NOT NULL,
  `ID_CYCLE` int(11) NOT NULL,
  PRIMARY KEY  (`NIVEAU_ID`),
  KEY `ID_CYCLE` (`ID_CYCLE`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table NIVEAU_CLASSE
--#STEP()
DROP TABLE IF EXISTS NIVEAU_CLASSE;
CREATE TABLE `NIVEAU_CLASSE` (
  `ID_NIVEAU` int(11) NOT NULL,
  `ID_CLASSE` int(11) NOT NULL,
  PRIMARY KEY  (`ID_NIVEAU`,`ID_CLASSE`),
  KEY `ID_CLASSE` (`ID_CLASSE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- Structure de la table NOTES
--#STEP()
DROP TABLE IF EXISTS NOTES;
CREATE TABLE `NOTES` (
  `NOTE_ID` int(11) NOT NULL auto_increment,
  `NOTE_NOM` varchar(50) NOT NULL,
  `NOTE_LABEL` varchar(3) NOT NULL,
  `NOTE_NOTE` int(11) NOT NULL,
  PRIMARY KEY  (`NOTE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table PERIODES
--#STEP()
DROP TABLE IF EXISTS PERIODES;
CREATE TABLE `PERIODES` (
  `PERIODE_ID` int(11) NOT NULL auto_increment,
  `PERIODE_NOM` varchar(10) NOT NULL,
  `PERIODE_DATE_DEBUT` varchar(16) NOT NULL,
  `PERIODE_DATE_FIN` varchar(16) NOT NULL,
  PRIMARY KEY  (`PERIODE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table PROFESSEURS
--#STEP()
DROP TABLE IF EXISTS PROFESSEURS;
CREATE TABLE `PROFESSEURS` (
  `PROFESSEUR_ID` int(11) NOT NULL auto_increment,
  `PROFESSEUR_PROFIL_ID` INT UNSIGNED NOT NULL,
  `PROFESSEUR_NOM` varchar(30) NOT NULL,
  `PROFESSEUR_PWD` varchar(50) NOT NULL,  
  PRIMARY KEY  (`PROFESSEUR_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Structure de la table PROFESSEUR_CLASSE
--#STEP()
DROP TABLE IF EXISTS PROFESSEUR_CLASSE;
CREATE TABLE `PROFESSEUR_CLASSE` (
  `ID_CLASSE` int(11) NOT NULL,
  `ID_PROFESSEUR` int(11) NOT NULL,
  PRIMARY KEY  (`ID_CLASSE`,`ID_PROFESSEUR`),
  KEY `ID_PROFESSEUR` (`ID_PROFESSEUR`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- Structure de la table PARAMETRES
--#STEP()
DROP TABLE IF EXISTS PARAMETRES;
CREATE TABLE `PARAMETRES` (
	`VERSION` VARCHAR(15) NOT NULL,
	`DATE_VERSION` DATETIME NOT NULL COMMENT 'Stocke la date de modification d''un fichier critique de l''application pour déterminer si une mise à jour est nécessaire.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Structure de la table des profils
--#STEP()
DROP TABLE IF EXISTS PROFILS;
CREATE TABLE `PROFILS` (
	`PROFIL_ID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`PROFIL_NAME` VARCHAR(30) NOT NULL,
	`PROFIL_COMMENT` TEXT NULL,
	PRIMARY KEY	(`PROFIL_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Structure de la table des droits pour les profils
--#STEP()
DROP TABLE IF EXISTS PROFILS_REL_RIGHTS;
CREATE TABLE `PROFILS_REL_RIGHTS` (
	`PROFIL_ID`	 INT UNSIGNED NOT NULL,
	`PROFIL_RIGHT` VARCHAR(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- =============================================================================
--#TITLE(Contraintes pour les tables)

-- Contraintes pour la table CLASSES
--#STEP()
ALTER TABLE `CLASSES`
  ADD CONSTRAINT `CLASSES_ibfk_1` FOREIGN KEY (`ID_ECOLE`) REFERENCES `ECOLES` (`ECOLE_ID`) ON DELETE CASCADE;

-- Contraintes pour la table COMPETENCES
--#STEP()
ALTER TABLE `COMPETENCES`
  ADD CONSTRAINT `COMPETENCES_ibfk_1` FOREIGN KEY (`ID_MATIERE`) REFERENCES `MATIERES` (`MATIERE_ID`) ON DELETE CASCADE;

-- Contraintes pour la table DOMAINES
--#STEP()
ALTER TABLE `DOMAINES`
  ADD CONSTRAINT `DOMAINES_ibfk_1` FOREIGN KEY (`ID_CYCLE`) REFERENCES `CYCLES` (`CYCLE_ID`) ON DELETE CASCADE;

-- Contraintes pour la table ELEVE_CLASSE
--#STEP()
ALTER TABLE `ELEVE_CLASSE`
  ADD CONSTRAINT `ELEVE_CLASSE_ibfk_1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `ELEVE_CLASSE_ibfk_2` FOREIGN KEY (`ID_ELEVE`) REFERENCES `ELEVES` (`ELEVE_ID`) ON DELETE CASCADE;

-- Contraintes pour la table EVALUATIONS_COLLECTIVES
--#STEP()
ALTER TABLE `EVALUATIONS_COLLECTIVES`
  ADD CONSTRAINT `EVALUATIONS_COLLECTIVES_ibfk_1` FOREIGN KEY (`ID_PERIODE`) REFERENCES `PERIODES` (`PERIODE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `EVALUATIONS_COLLECTIVES_ibfk_2` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE;

-- Contraintes pour la table EVALUATIONS_INDIVIDUELLES
--#STEP()
ALTER TABLE `EVALUATIONS_INDIVIDUELLES`
  ADD CONSTRAINT `EVALUATIONS_INDIVIDUELLES_ibfk_1` FOREIGN KEY (`ID_COMPETENCE`) REFERENCES `COMPETENCES` (`COMPETENCE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `EVALUATIONS_INDIVIDUELLES_ibfk_2` FOREIGN KEY (`ID_EVAL_COL`) REFERENCES `EVALUATIONS_COLLECTIVES` (`EVAL_COL_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `EVALUATIONS_INDIVIDUELLES_ibfk_3` FOREIGN KEY (`ID_NOTE`) REFERENCES `NOTES` (`NOTE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `EVALUATIONS_INDIVIDUELLES_ibfk_4` FOREIGN KEY (`ID_ELEVE`) REFERENCES `ELEVES` (`ELEVE_ID`) ON DELETE CASCADE;

-- Contraintes pour la table MATIERES
--#STEP()
ALTER TABLE `MATIERES`
  ADD CONSTRAINT `MATIERES_ibfk_1` FOREIGN KEY (`ID_DOMAINE`) REFERENCES `DOMAINES` (`DOMAINE_ID`) ON DELETE CASCADE;

-- Contraintes pour la table NIVEAUX
--#STEP()
ALTER TABLE `NIVEAUX`
  ADD CONSTRAINT `NIVEAUX_ibfk_1` FOREIGN KEY (`ID_CYCLE`) REFERENCES `CYCLES` (`CYCLE_ID`) ON DELETE CASCADE;

-- Contraintes pour la table NIVEAU_CLASSE
--#STEP()
ALTER TABLE `NIVEAU_CLASSE`
  ADD CONSTRAINT `NIVEAU_CLASSE_ibfk_1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `NIVEAU_CLASSE_ibfk_2` FOREIGN KEY (`ID_NIVEAU`) REFERENCES `NIVEAUX` (`NIVEAU_ID`) ON DELETE CASCADE;

-- Contraintes pour la table PROFESSEUR_CLASSE
--#STEP()
ALTER TABLE `PROFESSEUR_CLASSE`
  ADD CONSTRAINT `PROFESSEUR_CLASSE_ibfk_1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `PROFESSEUR_CLASSE_ibfk_2` FOREIGN KEY (`ID_PROFESSEUR`) REFERENCES `PROFESSEURS` (`PROFESSEUR_ID`) ON DELETE CASCADE;

-- Contraintes pour la table PROFILS_REL_RIGHTS
--#STEP()
ALTER TABLE `PROFILS_REL_RIGHTS`
  ADD CONSTRAINT `FK_REL_PROFILS` FOREIGN KEY (`PROFIL_ID`) REFERENCES `PROFILS` (`PROFIL_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================================================
--#TITLE(Contenu des tables systèmes)

--#STEP(TRANSACTION)

-- La seul ligne de la table paramètre
INSERT INTO `PARAMETRES` (`VERSION`, `DATE_VERSION`)
VALUES ('0.0.0', '0000-00-00 00:00:00');

-- =============================================================================
--#TITLE(Contenu "spécial" de l'application)

--#STEP(TRANSACTION)
-- Création des profils de l'application
INSERT INTO PROFILS(`PROFIL_ID`, `PROFIL_NAME`, `PROFIL_COMMENT`) VALUES
(1, 'Administrateur', 'Profil de l''administrateur général de l''application'),
(2, 'Professeur', 'Professeur utilisateur de l''application'),
(3, 'Directeur/Directrice', 'Directeur ou directrice d''une école');

--#STEP(TRANSACTION)
-- Ajout des droits pour le profil 'Professeur'
INSERT INTO `PROFILS_REL_RIGHTS`(`PROFIL_ID`, `PROFIL_RIGHT`) VALUES
(2, 'eleve_active'),
(2, 'eleve_add'),
(2, 'eleve_edit'),
(2, 'eleve_list'),
(2, 'consultation_list'),
(2, 'eval_ind_add'),
(2, 'eval_ind_delete'),
(2, 'eval_ind_edit'),
(2, 'eval_ind_list'),
(2, 'eval_col_add'),
(2, 'eval_col_delete'),
(2, 'eval_col_edit'),
(2, 'eval_col_list'),
(2, 'livret_list'),
(2, 'profil_edit');

--#STEP(TRANSACTION)
-- Ajout des droits pour le profil 'Directeur/Directrice'
INSERT INTO `PROFILS_REL_RIGHTS`(`PROFIL_ID`, `PROFIL_RIGHT`) VALUES
(3, 'admin_profil_list'), 
(3, 'admin_profil_add'), 
(3, 'admin_profil_edit'),  
(3, 'admin_profil_delete'), 
(3, 'professeur_list'),  
(3, 'professeur_add'),
(3, 'professeur_edit'), 
(3, 'professeur_delete'),
(3, 'ecole_list'), 
(3, 'ecole_add'), 
(3, 'ecole_edit'), 
(3, 'ecole_delete'),
(3, 'classe_list'), 
(3, 'classe_add'), 
(3, 'classe_edit'), 
(3, 'classe_delete'),
(3, 'admin_eleve_list'),
(3, 'cycle_list'), 
(3, 'cycle_add'), 
(3, 'cycle_edit'),
(3, 'cycle_delete'),
(3, 'niveau_list'), 
(3, 'niveau_add'), 
(3, 'niveau_edit'), 
(3, 'niveau_delete'),
(3, 'domaine_list'), 
(3, 'domaine_add'), 
(3, 'domaine_edit'), 
(3, 'domaine_delete'),
(3, 'matiere_list'), 
(3, 'matiere_add'), 
(3, 'matiere_edit'), 
(3, 'matiere_delete'),
(3, 'competence_list'), 
(3, 'competence_add'), 
(3, 'competence_edit'), 
(3, 'competence_delete'),
(3, 'note_list'),
(3, 'periode_list'), 
(3, 'periode_add'), 
(3, 'periode_edit'), 
(3, 'periode_delete');

--#STEP(TRANSACTION)
-- Ajout d'un premier utilisateur nommé 'administrateur' avec mot de passe 'admin'
INSERT INTO `PROFESSEURS` (`PROFESSEUR_ID`, `PROFESSEUR_NOM`, `PROFESSEUR_PWD`, `PROFESSEUR_PROFIL_ID`) VALUES
(1, 'administrateur', MD5('admin'), 1);

--#STEP(TRANSACTION)
-- Contenu de la table `NIVEAUX`
INSERT INTO `CYCLES` (`CYCLE_ID`, `CYCLE_NOM`) VALUES
(1, 'I'),
(2, 'II'),
(3, 'III');

-- Contenu de la table `NIVEAUX`
--#STEP(TRANSACTION)
INSERT INTO `NIVEAUX` (`NIVEAU_ID`, `NIVEAU_NOM`, `ID_CYCLE`) VALUES
(1, 'PS', 1),
(2, 'MS', 1),
(3, 'GS', 2),
(4, 'CP', 2),
(5, 'CE1', 2),
(6, 'CE2', 3),
(7, 'CM1', 3),
(8, 'CM2', 3);

-- Contenu de la table `NOTES`
--#STEP(TRANSACTION)
INSERT INTO `NOTES` (`NOTE_ID`, `NOTE_NOM`, `NOTE_LABEL`, `NOTE_NOTE`) VALUES
(1, 'Acquis', 'A', 20),
(2, 'A Renforcer', 'AR', 15),
(3, 'En cours d''acquisition', 'ECA', 10),
(4, 'Non Acquis', 'NA', 5),
(5, 'Non Evaluée', 'NE', 0);

-- Contenu de la table `PERIODES`
--#STEP(TRANSACTION)
INSERT INTO `PERIODES` (`PERIODE_ID`, `PERIODE_NOM`, `PERIODE_DATE_DEBUT`, `PERIODE_DATE_FIN`) VALUES
(1, 'p1', 'rentrée', 'toussaint'),
(2, 'p2', 'toussaint', 'noël'),
(3, 'p3', 'noël', 'février'),
(4, 'p4', 'février', 'pâques'),
(5, 'p5', 'pâques', 'été');

-- Contenu de la table `DOMAINES`
--#STEP(TRANSACTION)
INSERT INTO `DOMAINES` (`DOMAINE_ID`, `DOMAINE_NOM`, `ID_CYCLE`) VALUES
(1, 'Le langage au coeur des apprentissages', 1),
(2, 'Vivre ensemble', 1),
(3, 'Agir et s''exprimer avec son corps', 1),
(4, 'Découvrir le monde', 1),
(5, 'La sensibilité, l''imagination et la création', 1),
(6, 'Instruction civique et morale', 2),
(7, 'Français', 2),
(8, 'Mathématiques', 2),
(9, 'Découvertes du monde', 2),
(10, 'Langue vivante', 2),
(11, 'Pratiques artistiques et histoire des arts', 2),
(12, 'Education physique et sportive', 2),
(13, 'Langue française, éducation littéraire et humaine', 3),
(14, 'Education scientifique - Mathématiques', 3),
(15, 'Sciences expérimentales et technologie', 3),
(16, 'Education artistique', 3),
(17, 'Education physique et sportive', 3),
(18, 'Brevet informatique et Internet - niveau 1', 3);

-- Contenu de la table `MATIERES`
--#STEP(TRANSACTION)
INSERT INTO `MATIERES` (`MATIERE_ID`, `MATIERE_NOM`, `ID_DOMAINE`) VALUES
(1, 'Communication', 1),
(2, 'Le langage d''accompagnement de l''action (langage en situation)', 1),
(3, 'Le langage d''évocation', 1),
(4, 'Le langage écrit - Fonctions de l''écrit', 1),
(5, 'Le langage écrit - Familiarisation avec la langue de l''écrit et la littérature', 1),
(6, 'Le langage écrit - Découverte des réalités sonores du langage', 1),
(7, 'Le langage écrit - Activités graphiques et écriture', 1),
(8, 'Le langage écrit - Découverte du principe alphabétique', 1),
(9, 'Vivre ensemble', 2),
(10, 'Réaliser une action que l''on peut mesurer', 3),
(11, 'Adapter ses déplacements à différents types d''environnements', 3),
(12, 'Coopérer et s''opposer individuellement ou collectivement', 3),
(13, 'Réaliser des actions à visée artistique, esthétique ou expressive', 3),
(14, 'Le domaine sensoriel', 4),
(15, 'Le domaine de la matière et des objets', 4),
(16, 'Le domaine du vivant, de l''environnement, de l''hygiène et de la santé', 4),
(17, 'Le domaine de la structuration de l''espace', 4),
(18, 'Le domaine de la structuration du temps', 4),
(19, 'Formes et grandeurs', 4),
(20, 'Quantités et nombres', 4),
(21, 'Le regard et le geste', 5),
(22, 'La voix et l''écoute', 5),
(23, 'Instruction civique et morale', 6),
(24, 'Lecture écriture', 7),
(25, 'Orthographe', 7),
(26, 'Grammaire et conjugaison', 7),
(27, 'Vocabulaire', 7),
(28, 'Langage oral', 7),
(29, 'Organisation et gestion des données', 8),
(30, 'Nombres', 8),
(31, 'Calculs', 8),
(32, 'Géométrie', 8),
(33, 'Grandeurs et mesures', 8),
(34, 'L''espace et le temps', 9),
(35, 'Le monde du vivant', 9),
(36, 'Le monde de la matière et des objets', 9),
(37, 'Langue vivante', 10),
(38, 'Arts visuels', 11),
(39, 'Education musical', 11),
(40, 'Education physique et sportive', 12),
(41, 'Littérature (lire, dire, écrire)', 13),
(42, 'Observation réfléchie de la langue (orthographe, grammaire, conjugaison, vocabulaire)', 13),
(43, 'Langues étrangères et régionales', 13),
(44, 'Histoire', 13),
(45, 'Géographie', 13),
(46, 'Compétences générales', 14),
(47, 'Exploitation de données numériques : Problèmes relevant des quatre opérations', 14),
(48, 'Exploitation de données numériques : Proportionnalité', 14),
(49, 'Exploitation de données numériques : Organisation et représentation de données numériques', 14),
(50, 'Exploitation de données numériques : Connaissance des nombres entiers naturels, Désignations orales et écrites des nombres entiers naturels', 14),
(51, 'Exploitation de données numériques : Ordre sur les nombres entiers naturels', 14),
(52, 'Exploitation de données numériques : Structuration arithmétique des nombres entiers naturels', 14),
(53, 'Exploitation de données numériques : Connaissance des fractions simples et des nombres décimaux, Fractions', 14),
(54, 'Exploitation de données numériques : Désignations orales et écrites des nombres décimaux', 14),
(55, 'Exploitation de données numériques : Ordre sur les nombres décimaux', 14),
(56, 'Exploitation de données numériques : Relations entre certains nombres décimaux', 14),
(57, 'Calcul: Résultats mémorisés, procédures automatisées', 14),
(58, 'Calcul réfléchi', 14),
(59, 'Calcul instrumenté', 14),
(60, 'Espace et géométrie - Repérage, utilisation de plans, de cartes', 14),
(61, 'Espace et géométrie - Relations et propriétés : alignement, perpendicularité, parallélisme, égalité de longueurs, symétrie axiale', 14),
(62, 'Espace et géométrie - Figures planes : triangle (et cas particuliers), carré, rectangle, losange, cercle', 14),
(63, 'Espace et géométrie - Solides : cube, parallélépipède rectangle', 14),
(64, 'Espace et géométrie - Agrandissement, réduction', 14),
(65, 'Espace et géométrie - Grandeurs et mesure: Longueurs, masses, volumes (contenances), repérage du temps, durées', 14),
(66, 'Espace et géométrie - Aires', 14),
(67, 'Espace et géométrie - Angles', 14),
(68, 'Sciences expérimentales et technologie', 15),
(69, 'Arts visuels', 16),
(70, 'Education musicale', 16),
(71, 'Education physique et sportive', 17),
(72, 'Maîtriser les premières bases de la technologie informatique et avoir une approche des principales fonctions d''un ordinateur.', 18),
(73, 'Adopter une attitude citoyenne face aux informations véhiculées par les outils informatiques', 18),
(74, 'Produire, crée, modifier et exploiter un document à l''aide d''un logiciel de traitement de texte.', 18),
(75, 'Chercher, se documenter au moyen d''un produit multimédia (cédérom, dévédérom, site Internet, base de données).', 18),
(76, 'Communiquer au moyen d''une messagerie électronique.', 18);

-- Contenu de la table `COMPETENCES`
--#STEP(TRANSACTION)
INSERT INTO `COMPETENCES` (`COMPETENCE_ID`, `COMPETENCE_NOM`, `ID_MATIERE`) VALUES
(1, 'Etre capable de répondre aux sollicitations de l''adulte en se faisant comprendre dès la fin de la première année de scolarité (à trois ou quatre ans).', 1),
(2, 'Etre capable de prendre l''initiative d''un échange et le conduire au-delà de la première réponse.', 1),
(3, 'Etre capable de participer à un échange collectif en acceptant d''écouter autrui, en attendant son tour de parole et en restant dans le propos de l''échange.', 1),
(4, 'Etre capable de comprendre les consignes ordinaires de la classe.', 2),
(5, 'Etre capable de dire ce que l''on fait ou ce que fait un camarade (dans une activité, un atelier...).', 2),
(6, 'Etre capable de prêter sa voix à une marionnette.', 2),
(7, 'Etre capable de rappeler en se faisant comprendre un événement qui a été vécu collectivement (sortie, activité scolaire, incident...).', 3),
(8, 'Etre capable de comprendre une histoire adaptée à son âge et le manifester en reformulant dans ses propres mots la trame narrative de l''histoire.', 3),
(9, 'Etre capable d''identifier les personnages d''une histoire, les caractériser physiquement et moralement, les dessiner.', 3),
(10, 'Etre capable de raconter un conte déjà connu en s''appuyant sur la succession des illustrations.', 3),
(11, 'Etre capable d''inventer une courte histoire dans laquelle les acteurs seront correctement posés, où il y aura au moins un événement et une clôture.', 3),
(12, 'Etre capable de dire ou chanter chaque année au moins une dizaine de comptines ou de jeux de doigts et au moins une dizaine de chansons et de poésies.', 3),
(13, 'Etre capable de savoir à quoi servent un panneau urbain, une affiche, un journal, un livre, un cahier, un écran d''ordinateur... (c''est-à-dire donner des exemples de textes pouvant être trouvés sur l''u', 4),
(14, 'Etre capable de dicter individuellement un texte à un adulte en contrôlant la vitesse du débit et en demandant des rappels pour modifier ses énoncés.', 5),
(15, 'Etre capable de dans une dictée collective à l''adulte, restaurer la structure syntaxique d''une phrase non grammaticale, proposer une amélioration de la cohésion du texte (pronominalisation, connexion ', 5),
(16, 'Etre capable de reformuler dans ses propres mots un passage lu par l''enseignant.', 5),
(17, 'Etre capable d''évoquer, à propos de quelques grandes expériences humaines, un texte lu ou raconté par le maître.', 5),
(18, 'Etre capable de raconter brièvement l''histoire de quelques personnages de fiction rencontrés dans les albums ou dans les contes découverts en classe.', 5),
(19, 'Etre capable de rythmer un texte en en scandant les syllabes orales.', 6),
(20, 'Etre capable de reconnaître une même syllabe dans plusieurs énoncés (en fin d''énoncé, en début d''énoncé, en milieu d''énoncé).', 6),
(21, 'Etre capable de produire des assonances ou des rimes.', 6),
(22, 'Etre capable d''écrire son prénom en capitales d''imprimerie et en lettres cursives.', 7),
(23, 'Etre capable de copier des mots en capitales d''imprimerie, en cursive avec ou sans l''aide de l''enseignant.', 7),
(24, 'Etre capable de reproduire un motif graphique simple en expliquant sa façon de procéder.', 7),
(25, 'Etre capable de représenter un objet, un personnage, réels ou fictifs.', 7),
(26, 'Etre capable d''en fin d''école maternelle, copier une ligne de texte en écriture cursive en ayant une tenue correcte de l''instrument, en plaçant sa feuille dans l''axe du bras et en respectant le sens d', 7),
(27, 'Etre capable de dès la fin de la première année passée à l''école maternelle (à trois ou quatre ans), reconnaître son prénom écrit en capitales d''imprimerie.', 8),
(28, 'Etre capable de pouvoir dire où sont les mots successifs d''une phrase écrite après lecture par l''adulte.', 8),
(29, 'Etre capable de connaître le nom des lettres de l''alphabet.', 8),
(30, 'Etre capable de proposer une écriture alphabétique pour un mot simple en empruntant des fragments de mots au répertoire des mots affichés dans la classe.', 8),
(31, 'Etre capable de jouer son rôle dans une activité en adoptant un comportement individuel qui tient compte des apports et des contraintes de la vie collective.', 9),
(32, 'Etre capable d''identifier et connaître les fonctions et le rôle des différents  adultes de l''école.', 9),
(33, 'Etre capable de respecter les règles de la vie commune (respect de l''autre, du matériel, des règles de la politesse...) et appliquer dans son comportement vis-à-vis de ses camarades quelques principes', 9),
(34, 'Etre capable de courir, sauter, lancer de différentes façons (par exemple : courir vite, sauter loin avec ou sans élan).', 10),
(35, 'Etre capable de courir, sauter, lancer dans des espaces et avec des matériels', 10),
(36, 'Etre capable de se déplacer dans des formes d''actions inhabituelles remettant en cause l''équilibre (sauter, grimper, rouler, se balancer, se déplacer à quatre pattes, se renverser...).', 11),
(37, 'Etre capable de se déplacer (marcher, courir) dans des environnements proches, puis progressivement dans des environnements étrangers et incertains (cour, parc public, petit bois...).', 11),
(38, 'Etre capable de se déplacer avec ou sur des engins présentant un caractère d''instabilité (tricycles, trottinettes, vélos, rollers...).', 11),
(39, 'Etre capable de se déplacer dans ou sur des milieux instables (eau, neige, glace, sable...).', 11),
(40, 'Etre capable de s''opposer individuellement à un adversaire dans un jeu de lutte : tirer, pousser, saisir, tomber avec, immobiliser....', 12),
(41, 'Etre capable de coopérer avec des partenaires et s''opposer collectivement à un ou plusieurs adversaires dans un jeu collectif : transporter, lancer (des objets, des balles), courir pour attraper, pour', 12),
(42, 'Etre capable d''exprimer corporellement des images, des personnages, des sentiments, des états.', 13),
(43, 'Etre capable de communiquer aux autres des sentiments ou des émotions.', 13),
(44, 'Etre capable de s''exprimer de façon libre ou en suivant un rythme simple, musical ou non, avec ou sans matériel.', 13),
(45, 'Etre capable de décrire, comparer et classer des perceptions élémentaires (tactiles, gustatives, olfactives, auditives et visuelles).', 14),
(46, 'Etre capable d''associer à des perceptions déterminées les organes des sens qui correspondent.', 14),
(47, 'Etre capable de reconnaître, classer, sérier, désigner des matières, des objets, leurs qualités et leurs usages.', 15),
(48, 'Etre capable d''utiliser des appareils alimentés par des piles (lampe de poche, jouets, magnétophone...).', 15),
(49, 'Etre capable d''utiliser des objets programmables.', 15),
(50, 'Etre capable de choisir des outils et des matériaux adaptés à une situation, à des actions techniques spécifiques (plier, couper, coller, assembler, actionner...).', 15),
(51, 'Etre capable de réaliser des jeux de construction simples, construire des maquettes simples.', 15),
(52, 'Etre capable d''utiliser des procédés empiriques pour faire fonctionner des mécanismes simples.', 15),
(53, 'Etre capable de retrouver l''ordre des étapes du développement d''un animal ou d''un végétal.', 16),
(54, 'Etre capable de reconstituer l''image du corps humain, d''un animal ou d''un végétal à partir d''éléments séparés.', 16),
(55, 'Etre capable de reconnaître des manifestations de la vie animale et végétale, les relier à de grandes fonctions : croissance, nutrition, locomotion, reproduction.', 16),
(56, 'Etre capable de repérer quelques caractéristiques des milieux.', 16),
(57, 'Etre capable de connaître et appliquer quelques règles d''hygiène du corps (lavage des mains...), des locaux (rangement, propreté), de l''alimentation (régularité des repas, composition des menus).', 16),
(58, 'Etre capable de prendre en compte les risques de la rue (piétons et véhicules) ainsi que ceux de l''environnement familier proche (objets et comportements dangereux, produits toxiques) ou plus lointain', 16),
(59, 'Etre capable de repérer une situation inhabituelle ou de danger, demander de l''aide, pour être secouru ou porter secours.', 16),
(60, 'Etre capable de repérer des objets ou des déplacements dans l''espace par rapport à soi.', 17),
(61, 'Etre capable de décrire des positions relatives ou des déplacements à l''aide d''indicateurs spatiaux et en se référant à des repères stables variés.', 17),
(62, 'Etre capable de décrire et représenter simplement l''environnement proche (classe, école, quartier...).', 17),
(63, 'Etre capable de décrire des espaces moins familiers (espace vert, terrain vague, forêt, étang, haie, parc animalier).', 17),
(64, 'Etre capable de suivre un parcours décrit oralement (pas à pas), décrire ou représenter un parcours simple.', 17),
(65, 'Etre capable de savoir reproduire l''organisation dans l''espace d''un ensemble limité d''objets (en les manipulant, en les représentant).', 17),
(66, 'Etre capable de s''intéresser à des espaces inconnus découverts par des documentaires.', 17),
(67, 'Etre capable de reconnaître le caractère cyclique de certains phénomènes, utiliser des repères relatifs aux rythmes de la journée, de la semaine et de l''année, situer des événements les uns par rappor', 18),
(68, 'Etre capable de pouvoir exprimer et comprendre les oppositions entre présent et passé, présent et futur en utilisant correctement les marques temporelles et chronologiques.', 18),
(69, 'Etre capable de comparer des événements en fonction de leur durée.', 18),
(70, 'Etre capable d''exprimer et comprendre, dans le rappel d''un événement ou dans un récit, la situation temporelle de chaque événement par rapport à l''origine posée, leurs situations relatives (simultanéi', 18),
(71, 'Etre capable de différencier et classer des objets en fonction de caractéristiques liées à leur forme.', 19),
(72, 'Etre capable de reconnaître, classer et nommer des formes simples : carré, triangle, rond.', 19),
(73, 'Etre capable de reproduire un assemblage d''objets de formes simples à partir d''un modèle (puzzle, pavage, assemblage de solides).', 19),
(74, 'Etre capable de comparer, classer et ranger des objets selon leur taille, leur masse ou leur contenance.', 19),
(75, 'Etre capable de comparer des quantités en utilisant des procédures non numériques ou numériques.', 20),
(76, 'Etre capable de réaliser une collection qui comporte la même quantité d''objets qu''une autre collection (visible ou non, proche ou éloignée) en utilisant des procédures non numériques ou numériques, or', 20),
(77, 'Etre capable de résoudre des problèmes portant sur les quantités (augmentation, diminution, réunion, distribution, partage) en utilisant les nombres connus, sans recourir aux opérations usuelles.', 20),
(78, 'Etre capable de reconnaître globalement et exprimer de très petites quantités (d''un à trois ou quatre).', 20),
(79, 'Etre capable de reconnaître globalement et exprimer des petites quantités organisées en configurations connues (doigts de la main, constellations du dé).', 20),
(80, 'Etre capable de connaître la comptine numérique orale au moins jusqu''à trente.', 20),
(81, 'Etre capable de dénombrer une quantité en utilisant la suite orale des nombres connus.', 20),
(82, 'Etre capable d''associer le nom des nombres connus avec leur écriture chiffrée en se référant à une bande numérique.', 20),
(83, 'Etre capable d''adapter son geste aux contraintes matérielles (outils, supports, matières).', 21),
(84, 'Etre capable de surmonter une difficulté rencontrée.', 21),
(85, 'Etre capable de tirer parti des ressources expressives d''un procédé et d''un matériau donnés.', 21),
(86, 'Etre capable d''exercer des choix parmi des procédés et des matériaux déjà expérimentés.', 21),
(87, 'Etre capable d''utiliser le dessin comme moyen d''expression et de représentation.', 21),
(88, 'Etre capable de réaliser une composition en plan ou en volume selon un désir d''expression.', 21),
(89, 'Etre capable de reconnaître des images d''origines et de natures différentes.', 21),
(90, 'Etre capable d''identifier les principaux constituants d''un objet plastique (image, oeuvre d''art, production d''élève...).', 21),
(91, 'Etre capable d''établir des rapprochements entre deux objets plastiques (une production d''élève et une reproduction d''oeuvre par exemple) sur leplan de la forme, de la couleur, du sens ou du procédé de', 21),
(92, 'Etre capable de dire ce qu''on fait, ce qu''on voit, ce qu''on ressent, ce qu''on pense.', 21),
(93, 'Etre capable d''agir en coopération dans une situation de production collective.', 21),
(94, 'Etre capable d''avoir mémorisé un répertoire varié de comptines et de chansons.', 22),
(95, 'Etre capable d''interpréter avec des variantes expressives un chant, une comptine, en petit groupe.', 22),
(96, 'Etre capable de jouer de sa voix pour explorer des variantes de timbre, d''intensité, de hauteur, de nuance.', 22),
(97, 'Etre capable de marquer la pulsation corporellement ou à l''aide d''un objet sonore, jouer sur le tempo en situation d''imitation.', 22),
(98, 'Etre capable de repérer et reproduire des formules rythmiques simples corporellement ou avec des instruments.', 22),
(99, 'Etre capable de coordonner un texte parlé ou chanté et un accompagnement corporel ou instrumental.', 22),
(100, 'Etre capable de tenir sa place dans des activités collectives et intervenir très brièvement en soliste.', 22),
(101, 'Etre capable d''écouter un extrait musical ou une production, puis s''exprimer et dialoguer avec les autres pour donner ses impressions.', 22),
(102, 'Etre capable d''utiliser quelques moyens graphiques simples pour représenter et coder le déroulement d''une phrase musicale.', 22),
(103, 'Etre capable d''utiliser le corps et l''espace de façon variée et originale en fonction des caractéristiques temporelles et musicales des supports utilisés.', 22),
(104, 'Etre capable de faire des propositions lors des phases de création et d''invention, avec son corps, sa voix ou des objets sonores.', 22),
(105, 'Prendre des responsabilités et être autonome', 23),
(106, 'Respecter les adultes et les obéir', 23),
(107, 'Respecter les règles de la vie collective', 23),
(108, 'Connaître les règles élémentaires de politesse', 23),
(109, 'Connaître des règles simples de comportement en société', 23),
(110, 'Coopérer à la vie de la classe et rendre service', 23),
(111, 'Connaître les principaux symboles de la nation et de la République et les respecter', 23),
(112, 'Repérer un son auditivement', 24),
(113, 'Repérer la graphie d''un son', 24),
(114, 'Lire une syllabe', 24),
(115, 'Lire un phonème', 24),
(116, 'Lire des mots', 24),
(117, 'Lire des phrases', 24),
(118, 'Lire à haute voix un texte et le comprendre', 24),
(119, 'Lire silencieusement une consigne, un texte et les comprendre', 24),
(120, 'Connaître le vocabulaire lié à la lecture d''un texte (auteur, éditeur...)', 24),
(121, 'Découper un mot en syllabes', 24),
(122, 'Découper une phrase en mots', 24),
(123, 'Découper un texte en phrases', 24),
(124, 'Utiliser des mots d''une même famille', 24),
(125, 'Comprendre un texte lu par l''adulte', 24),
(126, 'Retrouver, à l''écrit, des informations dans un texte', 24),
(127, 'Tenir correctement son outil', 24),
(128, 'Ecrire en minuscule de manière sûre et lisible', 24),
(129, 'Passer de l''écriture scripte à l''écriture cursive', 24),
(130, 'Connaître et savoir écrire les majuscules', 24),
(131, 'Ecrire sans faute des mots simples', 24),
(132, 'Ecrire sans faute des mots invariables courants', 24),
(133, 'Ecrire des syllabes sous la dictée', 24),
(134, 'Ecrire des mots sous la dictée', 24),
(135, 'Ecrire des phrases sous la dictée', 24),
(136, 'Copier une phrase sans faute', 24),
(137, 'Copier un court texte sans faute', 24),
(138, 'Ordonner des fragments de textes', 24),
(139, 'Ecrire la suite d''évènements d''un texte', 24),
(140, 'Ecrire spontanément des phrases', 24),
(141, 'Ecrire de manière autonome un court texte', 24),
(142, 'Relire sa production et la corriger', 24),
(143, 'Utiliser la marque du pluriel des noms à l''écrit', 25),
(144, 'Utiliser les marques du genre dans le groupe nominal, à l''écrit', 25),
(145, 'Utiliser les marques du nombre dans le groupe nominal, à l''écrit', 25),
(146, 'Marquer les accords sujet / verbe dans la phrase', 25),
(147, 'Passer du singulier au pluriel et inversement dans le groupe nominal', 25),
(148, 'Passer du masculin au féminin et inversement dans le groupe nominal', 25),
(149, 'Distinguer quelques homonymes grammaticaux', 25),
(150, 'Utiliser à l''écrit, les marques du pluriel des verbes', 25),
(151, 'Utiliser à l''oral les formes affirmatives, négatives et interrogatives', 26),
(152, 'Ordonner les mots d''une phrase', 26),
(153, 'Identifier et déplacer les groupes de mots dans la phrase', 26),
(154, 'Utiliser le point et la majuscule', 26),
(155, 'Distinguer les mots selon leur nature : verbe, nom, adjectif qualificatif, pronoms personnels sujets, articles', 26),
(156, 'Trouver le groupe sujet', 26),
(157, 'Savoir remplacer le sujet par un pronom personnel', 26),
(158, 'Trouver le groupe verbal et le verbe', 26),
(159, 'Faire correspondre actions présente, passée, future et le temps de la conjugaison', 26),
(160, 'Connaître et utiliser les pronoms de la conjugaison', 26),
(161, 'Remplacer le sujet par un pronom personnel', 26),
(162, 'Trouver les noms dans la phrase', 26),
(163, 'Distinguer nom commun et nom propre', 26),
(164, 'Reconnaître et utiliser les adjectifs qualificatifs', 26),
(165, 'Reconnaître et utiliser les articles', 26),
(166, 'Identifier présent, imparfait, futur, passé composé dans un texte', 26),
(167, 'Conjuguer au présent les verbes du 1° groupe', 26),
(168, 'Conjuguer au présent le verbe être', 26),
(169, 'Conjuguer au présent le verbe avoir', 26),
(170, 'Conjuguer au présent le verbe aller', 26),
(171, 'Conjuguer au présent le verbe faire', 26),
(172, 'Conjuguer au présent le verbe dire', 26),
(173, 'Conjuguer au présent le verbe venir', 26),
(174, 'Conjuguer au futur les verbes du 1° groupe', 26),
(175, 'Conjuguer au futur le verbe être', 26),
(176, 'Conjuguer au futur le verbe avoir', 26),
(177, 'Conjuguer au passé-composé les verbes du 1° groupe', 26),
(178, 'Conjuguer au passé-composé le verbe être', 26),
(179, 'Conjuguer au passé-composé le verbe avoir', 26),
(180, 'Trouver l''infinitif d''un verbe et son groupe', 26),
(181, 'Connaître l''ordre alphabétique', 27),
(182, 'Utiliser le dictionnaire', 27),
(183, 'Distinguer les différents sens d''un mot', 27),
(184, 'Distinguer différents homonymes', 27),
(185, 'Trouver des synonymes d''un mot', 27),
(186, 'Trouver des contraires d''un mot', 27),
(187, 'Construire des mots en ajoutant des préfixes', 27),
(188, 'Construire des mots en ajoutant des suffixes', 27),
(189, 'Distinguer le radical et la terminaison d''un mot', 27),
(190, 'Distinguer sens propre et sens figuré', 27),
(191, 'Trouver des mots de la même famille', 27),
(192, 'S''exprimer clairement à l''oral', 28),
(193, 'Savoir écouter les autres', 28),
(194, 'Savoir décrire des images', 28),
(195, 'Savoir reformuler une formule', 28),
(196, 'Savoir présenter à la classe un travail collectif ou individuel', 28),
(197, 'Etre capable de dire de mémoire un poême', 28),
(198, 'Savoir calculer une augmentation ou d''une diminution', 29),
(199, 'Savoir calculer le nombre de parts dans des situations de partage', 29),
(200, 'Choisir la bonne opération', 29),
(201, 'Savoir repérer les informations utiles', 29),
(202, 'Savoir rédiger clairement le résultat', 29),
(203, 'Savoir dénombrer de 0 à 99', 30),
(204, 'Savoir dénombrer de 0 à 999', 30),
(205, 'Connaître les doubles et les moitiés des nombres connus', 30),
(206, 'Savoir dénombrer en utilisant les groupements par dizaine', 30),
(207, 'Savoir dénombrer en utilisant les groupements par centaine', 30),
(208, 'Savoir déterminer la valeur d''un chiffre dans l''écriture d''un nombre', 30),
(209, 'Savoir décomposer un nombre en écritures additives et multiplicatives', 30),
(210, 'Produire des suites orales et écrites de nombres de 2 en 2, 5 en 5', 30),
(211, 'Produire des suites orales et écrites de nombres de 10 en 10', 30),
(212, 'Produire des suites orales et écrites de nombres de 100 en 100', 30),
(213, 'Etre capable de citer le nombre qui suit et celui qui précède', 30),
(214, 'Savoir ordonner des nombres en ordre croissant', 30),
(215, 'Savoir ordonner des nombres en ordre décroissant', 30),
(216, 'Savoir compléter une frise numérique', 30),
(217, 'Savoir écrire les nombres en chiffres', 30),
(218, 'Savoir écrire les nombres en lettres', 30),
(219, 'Savoir comparer et ranger les nombres', 30),
(220, 'Savoir encadrer les nombres appris entre deux dizaines', 30),
(221, 'Savoir encadrer les nombres appris entre deux centaines', 30),
(222, 'Savoir utiliser les signes < > et =', 30),
(223, 'Savoir situer des nombres sur une ligne graduée de 1 en 1', 30),
(224, 'Savoir situer des nombres sur une ligne graduée de 10 en 10', 30),
(225, 'Savoir situer des nombres sur une ligne graduée de 100 en 100', 30),
(226, 'Construire et utiliser des tables d''addition de 1 à 9', 31),
(227, 'Savoir trouver le complément d''un nombre à la dizaine supérieure', 31),
(228, 'Connaître le sens de la multiplication', 31),
(229, 'Utiliser les tables de multiplication par 2, 3, 4 et 5', 31),
(230, 'Savoir multiplier par 10', 31),
(231, 'Savoir calculer des additions en ligne', 31),
(232, 'Savoir calculer des additions posées en colonne sans retenue', 31),
(233, 'Savoir calculer des additions posées en colonne avec retenue', 31),
(234, 'Savoir calculer des soustractions en ligne', 31),
(235, 'Savoir calculer des soustractions posées en colonne sans retenue', 31),
(236, 'Savoir calculer des soustractions posées en colonne avec retenue', 31),
(237, 'Savoir calculer une multiplication à 1 chiffre', 31),
(238, 'Savoir organiser des calculs additifs', 31),
(239, 'Savoir organiser des calculs soustractifs', 31),
(240, 'Savoir organiser des calculs multiplicatifs', 31),
(241, 'Savoir utiliser une calculatrice', 31),
(242, 'Savoir multiplier mentalement par 10, 100, 1000...', 31),
(243, 'Savoir diviser par 2 et multiplier par 5 des nombres entiers inférieurs à 200', 31),
(244, 'Comprendre et utiliser le vocabulaire lié à la situation dans l''espace', 32),
(245, 'Savoir repérer et coder des cases et des noeuds sur un quadrillage', 32),
(246, 'Reconnaître sur un objet ou un dessin les angles droits avec l''équerre', 32),
(247, 'Percevoir sur un objet ou un dessin les axes de symétrie', 32),
(248, 'Percevoir sur un objet ou un dessin les égalités de longueurs', 32),
(249, 'Savoir tracer un segment à l''aide de : gabarits, règle, pliage, calque, papier quadrillé', 32),
(250, 'Savoir distinguer les solides : cube, pavé droit', 32),
(251, 'Savoir distinguer les figures planes : triangle, carré, rectangle, cercle', 32),
(252, 'Savoir vérifier si une figure est un carré ou un rectangle', 32),
(253, 'Savoir reproduire, décrire et tracer une figure plane', 32),
(254, 'Savoir comparer des objets selon leur longueur', 33),
(255, 'Savoir comparer des objets selon leur masse', 33),
(256, 'Savoir utiliser une règle graduée en cm pour mesurer', 33),
(257, 'Savoir utiliser une règle graduée en cm pour tracer un segment', 33),
(258, 'Savoir utiliser une balance', 33),
(259, 'Savoir choisir l''unité appropriée à un mesurage (cm ou m)', 33),
(260, 'Savoir choisir l''unité appropriée à une pesée (g ou kg)', 33),
(261, 'Connaître les unités usuelles (cm et m ; g et kg)', 33),
(262, 'Connaître l''unité usuelle litre (l)', 33),
(263, 'Connaître et utiliser les euros et leurs centimes', 33),
(264, 'Savoir lire et utiliser les heure et 1/2 heure', 33),
(265, 'Comprendre et utiliser un tableau, un graphique', 33),
(266, 'Comprendre les représentations d''un espace familier : classe, école, quartier, ville', 34),
(267, 'Savoir construire et utiliser des plans simples', 34),
(268, 'Comparer un milieu familier avec d''autres milieux plus éloignés', 34),
(269, 'Connaître la position de sa région sur une carte', 34),
(270, 'Repérer l''alternance jours / nuits', 34),
(271, 'Repérer les semaines et les jours', 34),
(272, 'Connaître les mois', 34),
(273, 'Connaître les saisons', 34),
(274, 'Savoir se repérer sur un calendrier, une horloge', 34),
(275, 'Mémoriser quelques dates et personnages de l''histoire de France', 34),
(276, 'Prendre conscience de l''évolution des modes de vie', 34),
(277, 'Connaître quelques caractéristiques des végétaux (naissance, croissance, reproduction, nutrition)', 35),
(278, 'Connaître quelques caractéristiques des animaux (naissance, croissance, reproduction, régimes alimentaires', 35),
(279, 'Connaître quelques caractéristiques de la croissance de son corps', 35),
(280, 'Connaître quelques caractéristiques des mouvements', 35),
(281, 'Connaître quelques caractéristiques du squelette', 35),
(282, 'Connaître quelques caractéristiques de son alimentation', 35),
(283, 'Connaître quelques caractéristiques de la dentition', 35),
(284, 'Connaître les différentes caractéristiques des cinq sens', 35),
(285, 'Connaître quelques règles d''hygiène et de propreté', 35),
(286, 'Connaître quelques règles d''hygiène alimentaire', 35),
(287, 'Connaître quelques règles de sécurité individuelle et collective', 35),
(288, 'Connaître quelques règles de respect de l''environnement', 35),
(289, 'Savoir distinguer les solides et les liquides', 36),
(290, 'Savoir reconnaître les états solides et liquides de l''eau', 36),
(291, 'Savoir construire un circuit électrique simple', 36),
(292, 'Réaliser quelques maquettes élémentaires', 36),
(293, 'Savoir utiliser quelques fonctions de base d''un ordinateur', 36),
(294, 'Se présenter, présenter quelqu''un', 37),
(295, 'Se saluer, prendre congé', 37),
(296, 'Compter jusqu''à 10', 37),
(297, 'Les formules de politesse', 37),
(298, 'Dire ce qu''on aime ou n''aime pas', 37),
(299, 'Les jours de la semaine', 37),
(300, 'Les consignes de la classe', 37),
(301, 'La famille', 37),
(302, 'La météo', 37),
(303, 'Les fêtes du calendrier', 37),
(304, 'Pratiquer la peinture, le dessin', 38),
(305, 'Utiliser la photo, la vidéo, les arts numériques', 38),
(306, 'Travailler sur les volumes', 38),
(307, 'Utiliser différentes techniques : recouvrement, tracé, collage, montage', 38),
(308, 'Savoir reconnaître et nommer certaines oeuvres d''artistes', 38),
(309, 'Etre capable de chanter juste', 39),
(310, 'Etre capable d''interpréter de mémoire une dizaine de chansons ou comptines', 39),
(311, 'Savoir écouter les autres', 39),
(312, 'Savoir produire des rythmes simples', 39),
(313, 'Savoir reconnaître les grandes familles d''instruments', 39),
(314, 'Réaliser des performances dans des activités athlétiques : courir, sauter, lancer, franchir', 40),
(315, 'Adapter ses déplacements : grimper, nager, glisser, rouler', 40),
(316, 'S''orienter', 40),
(317, 'Coopérer et s''opposer dans des jeux de lutte, de raquette, des jeux traditionnels, des sports collectifs', 40),
(318, 'Respecter les règles de jeux', 40),
(319, 'Concevoir et réaliser des actions dansées, gymniques ou acrobatiques', 40),
(320, 'Etre capable de se servir des catalogues (papiers ou informatiques) de la BCD pour trouver un livre.', 41),
(321, 'Etre capable de se servir des informations portées sur la couverture et la page de titre d''un livre pour savoir s''il correspond au livre que l''on cherche.', 41),
(322, 'Etre capable de comprendre en le lisant silencieusement un texte littéraire court (petite nouvelle, extrait...) de complexité adaptée à l''âge et à la culture des élèves en s''appuyant sur un traitement', 41),
(323, 'Etre capable de lire en le comprenant un texte littéraire long, mettre en mémoire ce qui a été lu (synthèses successives) en mobilisant ses souvenirs lors des reprises.', 41),
(324, 'Etre capable de lire personnellement au moins un livre de littérature par mois.', 41),
(325, 'Etre capable de reformuler dans ses propres mots une lecture entendue.', 41),
(326, 'Etre capable de participer à un débat sur l''interprétation d''un texte littéraire en étant susceptible de vérifier dans le texte ce qui interdit ou permet l''interprétation défendue.', 41),
(327, 'Etre capable de restituer au moins dix textes (de prose, de poésie ou de théâtre) parmi ceux qui ont été mémorisés.', 41),
(328, 'Etre capable de dire quelques-uns de ces textes en en proposant une interprétation (et en étant susceptible d''expliciter cette dernière).', 41),
(329, 'Etre capable de pouvoir mettre sa voix et son corps en jeu dans un travail collectif portant sur un texte théâtral ou sur un texte poétique.', 41),
(330, 'Etre capable d''élaborer et écrire un récit d''au moins une vingtaine de lignes, avec ou sans support, en respectant des contraintes orthographiques, syntaxiques, lexicales et de présentation.', 41),
(331, 'Etre capable de pouvoir écrire un fragment de texte de type poétique en obéissant à une ou plusieurs règles précises en référence à des textes poétiques.', 41),
(332, 'Avoir compris et retenu que le sens d''une oeuvre littéraire n''est pas immédiatement accessible, mais que le travail d''interprétation nécessaire ne peut s''affranchir des contraintes du texte.', 41),
(333, 'Avoir compris et retenu qu''on ne peut confondre un récit littéraire et un récit historique, la fiction et le réel.', 41),
(334, 'Avoir compris et retenu les titres des textes lus dans l''année et le nom de leurs auteurs.', 41),
(335, 'Etre capable d''effectuer des manipulations dans un texte écrit (déplacement, remplacement, expansion, réduction).', 42),
(336, 'Etre capable d''identifier les verbes dans une phrase.', 42),
(337, 'Etre capable de manipuler les différents types de compléments des verbes les plus fréquents.', 42),
(338, 'Etre capable d''identifier les noms dans une phrase.', 42),
(339, 'Etre capable de manipuler les différentes déterminations du nom (articles, déterminants possessifs, démonstratifs, indéfinis).', 42),
(340, 'Etre capable de manipuler les différentes expansions du nom (adjectifs qualificatifs, relatives, compléments du nom).', 42),
(341, 'Etre capable de trouver le présent, le passé composé, l''imparfait, le passé simple, le futur, le conditionnel présent et le présent du subjonctif des verbes réguliers (à partir des règles d''engendreme', 42),
(342, 'Etre capable de marquer l''accord sujet/verbe (situations régulières).', 42),
(343, 'Etre capable de repérer et réaliser les chaînes d''accords dans le groupe nominal.', 42),
(344, 'Etre capable d''utiliser un dictionnaire pour retrouver la définition d''un mot dans un emploi déterminé.', 42),
(345, 'Avoir compris et retenu qu''un texte est structuré.', 42),
(346, 'Avoir compris et retenu que les constituants d''une phrase ne sont pas seulement juxtaposés mais sont liés par de nombreuses relations (avec le verbe, autour du nom).', 42),
(347, 'Avoir compris et retenu que la plupart des mots, dans des contextes différents, ont des significations différentes.', 42),
(348, 'Avoir compris et retenu qu''il existe des régularités dans l''orthographe lexicale et que l''on peut les mobiliser pour écrire.', 42),
(349, 'Etre capable de comprendre des énoncés oraux simples au sujet de lui-même, de sa famille et de l''environnement concret et immédiat, si les gens parlent lentement et distinctement.', 43),
(350, 'Etre capable de reconnaître des éléments connus ainsi que des phrases très simples, par exemple dans des annonces, des affiches ou des catalogues.', 43),
(351, 'Etre capable de prendre part à une conversation', 43),
(352, 'Etre capable de communiquer de façon simple, à condition que l'' interlocuteur soit disposé à répéter ou à reformuler ses phrases plus lentement et à l''aider à formuler ce qu''il/elle essaie de dire.', 43),
(353, 'Etre capable de poser des questions simples sur des sujets familiers ou sur ce dont il/elle a immédiatement besoin, ainsi que répondre à de telles questions.', 43),
(354, 'Etre capable d'' utiliser des expressions et des phrases simples pour décrire son lieu d''habitation et les gens qu''il/elle connaît. ', 43),
(355, 'Etre capable de raconter une courte séquence au passé.', 43),
(356, 'Etre capable d''écrire un message électronique simple, une courte carte postale simple, par exemple de vacances.', 43),
(357, 'Etre capable de remplir un questionnaire d''identité extrêmement simple.', 43),
(358, 'Avoir compris et retenu quelques formules usuelles de communication correspondant aux fonctions de communication définies ci-dessus.', 43),
(359, 'Avoir compris et retenu la syntaxe et la morphosyntaxe.', 43),
(360, 'Avoir compris et retenu du lexique.', 43),
(361, 'Avoir compris et retenu l''organisation de la syntaxe de la phrase simple déclarative et interrogative.', 43),
(362, 'Avoir compris et retenu les moyens élémentaires de l''énonciation.', 43),
(363, 'Avoir compris et retenu l''opposition de l''unicité et du nombre.', 43),
(364, 'Avoir compris et retenu les moyens verbaux de la relation d''événements présents, passés ou à venir.', 43),
(365, 'Avoir compris et retenu les moyens d''exprimer la localisation.', 43),
(366, 'Avoir compris et retenu les comportements culturels dans les relations interpersonnelles liés aux fonctions de communication prévues au programme.', 43),
(367, 'Avoir compris et retenu la vie scolaire d''enfants du même âge dans le(s) pays ou région(s) concerné(s).', 43),
(368, 'Avoir compris et retenu le calendrier de l''année scolaire et civile, avec les événements les plus significatifs.', 43),
(369, 'Avoir compris et retenu le folklore, les personnages des légendes ou des contes des pays ou régions concernés.', 43),
(370, 'Avoir compris et retenu quelques repères culturels propres aux pays ou régions concernés.', 43),
(371, 'Etre capable de distinguer les grandes périodes historiques, pouvoir les situer chronologiquement, commencer à connaître pour chacune d''entre elles différentes formes de pouvoir, des groupes sociaux, ', 44),
(372, 'Etre capable de classer des documents selon leur nature, leur date et leur origine.', 44),
(373, 'Etre capable de savoir utiliser les connaissances historiques en éducation civique et dans les autres enseignements, en particulier dans le domaine artistique.', 44),
(374, 'Etre capable de consulter une encyclopédie et les pages de la toile.', 44),
(375, 'Etre capable d''utiliser à bon escient les temps du passé rencontrés dans les récits historiques', 44),
(376, 'Avoir compris et retenu une vingtaine d''événements et leurs dates (voir document d''application).', 44),
(377, 'Avoir compris et retenu le rôle des personnages et des groupes qui apparaissent dans les divers points forts, ainsi que les faits les plus significatifs, et pouvoir les situer dans leur période.', 44),
(378, 'Avoir compris et retenu le vocabulaire spécifique, pouvoir l''utiliser de façon exacte et appropriée.', 44),
(379, 'Etre capable d''effectuer une recherche dans un atlas imprimé et dans un atlas numérique.', 45),
(380, 'Etre capable de mettre en relation des cartes à différentes échelles pour localiser un phénomène.', 45),
(381, 'Etre capable de réaliser un croquis spatial simple.', 45),
(382, 'Etre capable de situer le lieu où se trouve l''école dans l''espace local et régional.', 45),
(383, 'Etre capable de situer la France dans l''espace mondial.', 45),
(384, 'Etre capable de situer les positions des principales villes françaises et des grands axes de communication français.', 45),
(385, 'Etre capable de situer l''Europe, ses principaux Etats, ses principales villes dans l''espace mondial.', 45),
(386, 'Etre capable d''appliquer les compétences acquises dans le domaine du calcul à l''usage de la monnaie (euros, centimes).', 45),
(387, 'Avoir compris et retenu le vocabulaire géographique de base (Etre capable de l''utiliser dans un contexte approprié).', 45),
(388, 'Avoir compris et retenu les grands types de paysages (Etre capable de les différencier).', 45),
(389, 'Avoir compris et retenu les grands ensembles humains (continentaux et océaniques) et pouvoir les reconnaître et les localiser sur un globe et sur un planisphère.', 45),
(390, 'Avoir compris et retenu les Etats qui participent à l''Union européenne.', 45),
(391, 'Etre capable d''utiliser ses connaissances pour traiter des problèmes.', 46),
(392, 'Etre capable de chercher et produire une solution originale dans un problème de recherche', 46),
(393, 'Etre capable de mettre en oeuvre un raisonnement, articuler les différentes étapes d''une solution.', 46),
(394, 'Etre capable de formuler et communiquer sa démarche et ses résultats par écrit et les exposer oralement.', 46),
(395, 'Etre capable de contrôler et discuter la pertinence ou la vraisemblance d''une solution.', 46),
(396, 'Etre capable d''identifier des erreurs dans une solution en distinguant celles qui sont relatives au choix d''une procédure de celles qui interviennent dans sa mise en oeuvre.', 46),
(397, 'Etre capable d''argumenter à propos de la validité d''une solution.', 46),
(398, 'Etre capable de résoudre des problèmes en utilisant les connaissances sur les nombres naturels et décimaux et sur les opérations étudiées.', 47),
(399, 'Etre capable de résoudre des problèmes relevant de la proportionnalité en utilisant des raisonnements personnels appropriés (dont des problèmes relatifs aux pourcentages, aux échelles, aux vitesses mo', 48),
(400, 'Etre capable d''organiser des séries de données (listes, tableaux...).', 49),
(401, 'Etre capable de lire, interpréter et construire quelques représentations : diagrammes, graphiques.', 49),
(402, 'Etre capable de déterminer la valeur de chacun des chiffres composant l''écriture d''un nombre entier en fonction de sa position.', 50),
(403, 'Etre capable de donner diverses décompositions d''un nombre en utilisant 10, 100, 1 000..., et retrouver l''écriture d''un nombre à partir d''une telle décomposition.', 50),
(404, 'Etre capable de produire des suites orales et écrites de 1 en 1, 10 en 10, 100 en 100, à partir de n''importe quel nombre.', 50),
(405, 'Etre capable d''associer la désignation orale et la désignation écrite (en chiffres) pour des nombres jusqu''à la classe des millions.', 50),
(406, 'Etre capable de comparer des nombres, les ranger en ordre croissant ou décroissant, les encadrer entre deux dizaines consécutives, deux centaines consécutives, deux milliers consécutifs....', 51),
(407, 'Etre capable d''utiliser les signes < et >> pour exprimer le résultat de la comparaison de deux nombres ou d''un encadrement.', 51),
(408, 'Etre capable de situer précisément ou approximativement des nombres sur une droite graduée de 10 en 10, de 100 en 100...', 51),
(409, 'Etre capable de connaître et utiliser des expressions telles que : double, moitié ou demi, triple, tiers, quadruple, quart ; trois quarts, deux tiers, trois demis d''un nombre entier.', 52),
(410, 'Etre capable de connaître et utiliser certaines relations entre des nombres d''usage courant : entre 5, 10, 25, 50, 75, 100 ; entre 50, 100, 200, 250, 500, 750, 1 000 ; entre 5, 15, 30, 45, 60, 90.', 52),
(411, 'Etre capable de reconnaître les multiples de 2, de 5 et de 10.', 52),
(412, 'Etre capable d''utiliser, dans des cas simples, des fractions ou des sommes d''entiers et de fractions pour coder des mesures de longueurs ou d''aires, une unité étant choisie, ou pour construire un segm', 53),
(413, 'Etre capable de nommer les fractions en utilisant le vocabulaire : demi, tiers, quart, dixième, centième....', 53),
(414, 'Etre capable d''encadrer une fraction simple par deux entiers consécutifs.', 53),
(415, 'Etre capable d''écrire une fraction sous forme de somme d''un entier et d''une fraction inférieure à 1.', 53),
(416, 'Etre capable de déterminer la valeur de chacun des chiffres composant une écriture à virgule, en fonction de sa position.', 54),
(417, 'Etre capable de passer, pour un nombre décimal, d''une écriture fractionnaire (fractions décimales) à une écriture à virgule (et réciproquement).', 54),
(418, 'Etre capable d''utiliser les nombres décimaux pour exprimer la mesure de la longueur d''un segment, celle de l''aire d''une surface (une unité étant donnée), ou pour repérer un point sur une droite gradué', 54),
(419, 'Etre capable d''écrire et interpréter sous forme décimale une mesure donnée avec plusieurs unités (et réciproquement).', 54),
(420, 'Etre capable de produire des décompositions liées à une écriture à virgule, en utilisant 10 ; 100 ; 1 000... et 0,1 ; 0,01 ; 0,001....', 54),
(421, 'Etre capable de produire des suites écrites ou orales de 0,1 en 0,1, de 0,01 en 0,01....', 54),
(422, 'Etre capable d''associer les désignations orales et l''écriture chiffrée d''un nombre décimal.', 54),
(423, 'Etre capable de comparer deux nombres décimaux donnés par leurs écritures à virgule.', 55),
(424, 'Etre capable d''encadrer un nombre décimal par deux entiers consécutifs ou par deux nombres décimaux.', 55),
(425, 'Etre capable d''intercaler des nombres décimaux entre deux nombres entiers consécutifs ou entre deux nombres décimaux.', 55),
(426, 'Etre capable d''utiliser les signes < et > pour exprimer le résultat de la comparaison de deux nombres ou d''un encadrement.', 55),
(427, 'Etre capable de donner une valeur approchée d''un nombre décimal à l''unité près, au dixième ou au centième près.', 55),
(428, 'Etre capable de situer exactement ou approximativement des nombres décimaux sur une droite graduée de 1 en 1, de 0,1 en 0,1.', 55),
(429, 'Etre capable de connaître et utiliser des écritures fractionnaires et décimales de certains nombres : 0,1 et 1/10 ; 0,01 et 1/100 ; 0,5 et 1/2 ; 0,25 et 1/4 ; 0,75 et 3/4 ; ', 56),
(430, 'Etre capable de connaître et utiliser les relations entre 1/4 (ou 0,25) et 1/2 (ou 0,5) entre 1/100 et 1/10  , entre et 1/1000 et 1/100', 56),
(431, 'Etre capable de connaître les tables d''addition (de 1 à 9) et de multiplication (de 2 à 9) et les utiliser pour calculer une somme, une différence ou un complément, un produit ou un quotient entier.', 57),
(432, 'Etre capable d''additionner ou soustraire mentalement des dizaines entières (nombres inférieurs à 100) ou des centaines entières (nombres inférieurs à 1 000).', 57),
(433, 'Etre capable de connaître le complément à la dizaine supérieure pour tout nombre inférieur à 100 ou le complément à l''entier immédiatement supérieur pour tout décimal ayant un chiffre après la virgule', 57),
(434, 'Etre capable de multiplier ou diviser un nombre entier ou décimal par 10, 100, 1000.', 57),
(435, 'Etre capable de calculer des sommes et des différences de nombres entiers ou décimaux, par un calcul écrit en ligne ou posé en colonnes.', 57),
(436, 'Etre capable de calculer le produit de deux entiers ou le produit d''un décimal par un entier (3 chiffres par 2 chiffres), par un calcul posé.', 57),
(437, 'Etre capable de calculer le quotient et le reste de la division euclidienne d''un nombre entier (d''au plus 4 chiffres) par un nombre entier (d''au plus 2 chiffres), par un calcul posé.', 57),
(438, 'Etre capable d''organiser et effectuer mentalement ou avec l''aide de l''écrit, sur des nombres entiers, un calcul additif, soustractif, multiplicatif, ou un calcul de division en s''appuyant sur des résu', 58),
(439, 'Etre capable d''organiser et effectuer des calculs du type 1,5 + 0,5 ; 2,8 + 0,2 ; 1,5 x 2 ; 0,5 x 3, en s''appuyant sur les résultats mémorisés et en utilisant de façon implicite les propriétés des nom', 58),
(440, 'Etre capable d''évaluer un ordre de grandeur d''un résultat, en utilisant un calcul approché, évaluer le nombre de chiffres d''un quotient entier.', 58),
(441, 'Etre capable de développer des moyens de contrôle des calculs instrumentés : chiffre des unités, nombre de chiffres (en particulier pour un quotient), calcul approché....', 58),
(442, 'Etre capable de savoir trouver mentalement le résultat numérique d''un problème à données simples.', 58),
(443, 'Etre capable d''utiliser à bon escient sa calculatrice pour obtenir un résultat numérique issu d''un problème et interpréter le résultat obtenu.', 59),
(444, 'Etre capable d''utiliser une calculatrice pour déterminer la somme, la différence de deux nombres entiers ou décimaux, le produit de deux nombres entiers ou celui d''un nombre décimal par un entier, le ', 59),
(445, 'Etre capable de connaître et utiliser certaines fonctionnalités de sa calculatrice pour gérer une suite de calculs : touches ''opérations'', touches ''mémoires'', touches ''parenthèses'', facteur constant.', 59),
(446, 'Etre capable de repérer une case ou un point sur un quadrillage.', 60),
(447, 'Etre capable d''utiliser un plan ou une carte pour situer un objet, anticiper ou réaliser un déplacement, évaluer une distance.', 60),
(448, 'Etre capable de vérifier, à l''aide des instruments : l''alignement de points (règle), l''égalité des longueurs de segments (compas ou instrument de mesure), la perpendicularité et le parallélisme entre ', 61),
(449, 'Etre capable d''effectuer les tracés correspondants.', 61),
(450, 'Etre capable de trouver le milieu d''un segment.', 61),
(451, 'Etre capable de percevoir qu''une figure possèd''un ou plusieurs axes de symétrie et le vérifier en utilisant différentes techniques (pliage, papier calque, miroir).', 61),
(452, 'Etre capable de compléter une figure par symétrie axiale en utilisant des techniques telles que pliage, papier calque, miroir.', 61),
(453, 'Etre capable de tracer, sur papier quadrillé, la figure symétrique d''une figure donnée par rapport à une droite donnée.', 61),
(454, 'Etre capable d''utiliser à bon escient le vocabulaire suivant : points alignés, droite, droites perpendiculaires, droites parallèles, segment, milieu, angle, figure symétrique d''une figure donnée par r', 61),
(455, 'Etre capable de reconnaître de manière perceptive une figure plane (en particulier dans une configuration plus complexe), en donner le nom, vérifier son existence en ayant recours aux propriétés et au', 62),
(456, 'Etre capable de décomposer une figure en figures plus simples.', 62),
(457, 'Etre capable de tracer une figure (sur papier uni, quadrillé ou pointé), soit à partir d''un modèle, soit à partir d''une description, d''un programme de construction ou d''un dessin à main levée.', 62),
(458, 'Etre capable de décrire une figure en vue de l''identifier dans un lot de figures ou de la faire reproduire sans équivoque.', 62),
(459, 'Etre capable d''utiliser à bon escient le vocabulaire suivant : triangle, triangle rectangle, triangle isocèle, triangle équilatéral, carré, rectangle, losange, cercle ; sommet, côté ; centre, rayon et', 62),
(460, 'Etre capable de percevoir un solide, en donner le nom, vérifier certaines propriétés relatives aux faces ou arêtes d''un solide à l''aide des instruments.', 63),
(461, 'Etre capable de décrire un solid''en vue de l''identifier dans un lot de solides divers ou de le faire reproduire sans équivoque.', 63),
(462, 'Etre capable de construire un cube ou un parallélépipède rectangle.', 63),
(463, 'Etre capable de reconnaître, construire ou compléter un patron de cube, de parallélépipède rectangle.', 63),
(464, 'Etre capable d''utiliser à bon escient le vocabulaire suivant : cube, parallélépipède rectangle ; sommet, arête, face.', 63),
(465, 'Etre capable de réaliser, dans des cas simples, des agrandissements ou des réductions de figures planes.', 64),
(466, 'Etre capable de contrôler si une figure est un agrandissement ou une réduction d''une autre figure.', 64),
(467, 'Etre capable d''utiliser des instruments pour mesurer des objets physiques ou géométriques.', 65),
(468, 'Etre capable d''exprimer le résultat d''un mesurage par un nombre ou un encadrement, l''unité (ou les unités) étant imposée(s) ou choisie(s) de façon appropriée.', 65),
(469, 'Etre capable de lire l''heure sur une montre à aiguilles ou une horloge.', 65),
(470, 'Etre capable de connaître les unités de mesure des durées (année, mois, semaine, jour, heure, minute, seconde) et leurs relations.', 65),
(471, 'Etre capable d''estimer une mesure (ordre de grandeur).', 65),
(472, 'Etre capable de construire ou réaliser un objet dont des mesures sont données.', 65),
(473, 'Etre capable de connaître les unités légales du système métrique pour les longueurs (mètre, ses multiples et ses sous-multiples usités), les masses (gramme, ses multiples et ses sous-multiples usités)', 65),
(474, 'Etre capable d''utiliser les équivalences entre les unités usuelles de longueur, de masse, de contenance, et effectuer des calculs simples sur les mesures, en tenant compte des relations entre les dive', 65),
(475, 'Etre capable d''utiliser le calcul pour obtenir la mesure d''une grandeur, en particulier : calculer le périmètre d''un polygone, calculer une durée à partir de la donnée de l''instant initial et de l''ins', 65),
(476, 'Etre capable de classer et ranger des surfaces (figures) selon leur aire (par superposition, découpage et recollement ou pavage par une surface de référence).', 66),
(477, 'Etre capable de construire une surface qui a même aire qu''une surface donnée (et qui ne lui est pas superposable).', 66),
(478, 'Etre capable de différencier aire et périmètre d''une surface, en particulier savoir que deux surfaces peuvent avoir la même aire sans avoir nécessairement le même périmètre et qu''elles peuvent avoir l', 66),
(479, 'Etre capable de mesurer l''aire d''une surface grâce à un pavage effectif à l''aide d''une surface de référence (dont l''aire est prise pour unité) ou grâce à l''utilisation d''un réseau quadrillé (le résult', 66),
(480, 'Etre capable de calculer l''aire d''un rectangle dont les côtés au moins sont de dimensions entières.', 66),
(481, 'Etre capable de connaître et utiliser les unités usuelles (cm2, dm2, m2 et km2) ainsi que quelques équivalences (1 m2 = 100 dm2, 1 dm2 =100 cm2, 1 km2 = 1 000 000 m2).', 66);

--#STEP(TRANSACTION)
INSERT INTO `COMPETENCES` (`COMPETENCE_ID`, `COMPETENCE_NOM`, `ID_MATIERE`) VALUES
(482, 'Etre capable de comparer des angles dessinés par superposition ou en utilisant un gabarit, en particulier des angles situés dans une figure (angles intérieurs d''un triangle, d''un quadrilatère...).', 67),
(483, 'Etre capable de reproduire un angle donné en utilisant un gabarit ou par report d''un étalon.', 67),
(484, 'Etre capable de tracer un angle droit, ainsi qu''un angle égal à la moitié, le quart ou le tiers d''un angle droit.', 67),
(485, 'Etre capable de poser des questions précises et cohérentes à propos d''une situation d''observation ou d''expérience.', 68),
(486, 'Etre capable d''imaginer et réaliser un dispositif expérimental susceptible de répondre aux questions que l''on se pose, en s''appuyant sur des observations, des mesures appropriées ou un schéma.', 68),
(487, 'Etre capable de réaliser un montage électrique à partir d''un schéma.', 68),
(488, 'Etre capable d''utiliser des instruments d''observation et de mesure : double décimètre, loupe, boussole, balance, chronomètre ou horloge, thermomètre.', 68),
(489, 'Etre capable de recommencer une expérience en ne modifiant qu''un seul facteur par rapport à l''expérience précédente.', 68),
(490, 'Etre capable de mettre en relation des données, en faire une représentation schématique et l''interpréter, mettre en relation des observations réalisées en classe et des savoirs que l''on trouve dans un', 68),
(491, 'Etre capable de participer à la préparation d''une enquête ou d''une visite en élaborant un protocole d''observation ou un questionnaire.', 68),
(492, 'Etre capable de rédiger un compte rendu intégrant schéma d''expérience ou dessin d''observation.', 68),
(493, 'Etre capable de produire, créer, modifier et exploiter un document à l''aide d''un logiciel de traitement de texte.', 68),
(494, 'Etre capable de communiquer au moyen d''une messagerie électronique.', 68),
(495, 'Avoir compris et retenu la conservation de la matière, dans les changements d''état de l''eau, les mélanges et la dissolution ; la matérialité de l''air.', 68),
(496, 'Avoir compris et retenu des fonctions du vivant qui en marquent l''unité et la diversité : développement et reproduction.', 68),
(497, 'Avoir compris et retenu les principes élémentaires des fonctions de nutrition et de mouvement à partir de leurs manifestations chez l''homme.', 68),
(498, 'Avoir compris et retenu une première approche des notions d''espèce et d''évolution.', 68),
(499, 'Avoir compris et retenu le rôle et la place des vivants dans leur environnement.', 68),
(500, 'Avoir compris et retenu quelques phénomènes astronomiques : ''course du Soleil'', durée des jours et des nuits, évolution au cours des saisons (calendrier), lien avec la boussole et les points cardinaux', 68),
(501, 'Avoir compris et retenu les principes élémentaires de fonctionnement de circuits électriques simples, de leviers, de balances, de systèmes de transmission du mouvement : quelques utilisations techniqu', 68),
(502, 'Etre capable d''utiliser le dessin dans ses différentes fonctions en utilisant diverses techniques.', 69),
(503, 'Etre capable de réaliser une production en deux ou trois dimensions, individuelle ou collective, menée à partir de consignes précises.', 69),
(504, 'Etre capable de choisir, manipuler et combiner des matériaux, des supports, des outils.', 69),
(505, 'Etre capable de témoigner d''une expérience, décrire une image, s''exprimer sur une oeuvre.', 69),
(506, 'Etre capable d''identifier différents types d''images en justifiant son point de vue.', 69),
(507, 'Etre capable de réinvestir dans d''autres disciplines les apports des arts visuels.', 69),
(508, 'Avoir compris et retenu les points communs et les différences entre les pratiques de la classe et les démarches des artistes ; repérer ce qui les distingue et ce qui les rapproche.', 69),
(509, 'Avoir compris et retenu identifier et nommer quelques références (oeuvres, personnalités, événements...) à partir des oeuvres de la liste nationale ; pouvoir les caractériser simplement et les situer ', 69),
(510, 'Etre capable de pouvoir interpréter de mémoire plus de dix chansons parmi celles qui ont été apprises.', 70),
(511, 'Etre capable de contrôler volontairement sa voix et son attitude corporelle pour chanter.', 70),
(512, 'Etre capable de tenir sa voix et sa place en formation chorale, notamment dans une polyphonie.', 70),
(513, 'Etre capable d''assumer son rôle dans un travail d''accompagnement.', 70),
(514, 'Etre capable de soutenir une écoute prolongée, utiliser des consignes d''écoute.', 70),
(515, 'Etre capable de repérer des éléments musicaux caractéristiques, les désigner et caractériser leur organisation (succession, simultanéité, ruptures...) en faisant appel à un lexique approprié.', 70),
(516, 'Etre capable de reconnaître une oeuvre du répertoire travaillé, la situer dans son contexte de création, porter à son égard un jugement esthétique.', 70),
(517, 'Etre capable de réemployer des savoir-faire au profit d''une production musicale ou chorégraphique inventée, personnelle ou collective.', 70),
(518, 'Etre capable de témoigner de son aisance à évoluer dans une danse collective et  dans des dispositifs scéniques divers.', 70),
(519, 'Etre capable d''exprimer son appréciation pour qualifier une réalisation dansée, chantée ou jouée, à la fois comme acteur et comme spectateur.', 70),
(520, 'Etre capable, dans différentes activités physiques, sportives et artistiques, de  réaliser une performance mesurée.', 71),
(521, 'Etre capable, dans différentes activités physiques, sportives et artistiques, d''adapter ses déplacements à différents types d''environnements.', 71),
(522, 'Etre capable, dans différentes activités physiques, sportives et artistiques, de  s''affronter individuellement ou collectivement.', 71),
(523, 'Etre capable, dans différentes activités physiques, sportives et artistiques, de concevoir et réaliser des actions à visée artistique, esthétique ou expressive.', 71),
(524, 'Etre capable de s''engager lucidement dans l''action.', 71),
(525, 'Etre capable de construire un projet d''action.', 71),
(526, 'Etre capable de mesurer et apprécier les effets de l''activité.', 71),
(527, 'Etre capable d''appliquer et construire des principes de vie collective.', 71),
(528, 'Avoir compris et retenu que l''on peut acquérir des connaissances spécifiques dans l''activité physique et sportive (sensations, émotions, savoirs sur les techniques de réalisation d''actions spécifiques', 71),
(529, 'Avoir compris et retenu des savoirs précis sur les différentes activités physiques et sportives rencontrées.', 71),
(530, 'Etre capable de nommer précisément les composants matériels et logiciels.', 72),
(531, 'Etre capable d''utiliser la souris et le clavier.', 72),
(532, 'Etre capable d''ouvrir un document, d''enregistrer un fichier dans un répertoire ou dossier.', 72),
(533, 'Etre capable de vérifier la pertinence et l''exactitude de données que l''élève a saisies lui-même.', 73),
(534, 'Etre capable de prendre l''habitude de s''interroger sur la pertinence et sur la validité des résultats obtenus à l''aide de l''ordinateur.', 73),
(535, 'Etre capable de reconnaître et respecter la propriété intellectuelle.', 73),
(536, 'Etre capable d''ouvrir, de consulter ou d''imprimer un document.', 74),
(537, 'Etre capable de saisir et modifier un texte en utilisant les fonctions de base d''un traitement de texte.', 74),
(538, 'Etre capable d''insérer des images à l''intérieur d''un texte.', 74),
(539, 'Etre capable d''utiliser le correcteur orthographique', 74),
(540, 'Etre capable de choisir le support approprié au résultat escompté.', 75),
(541, 'Etre capable de mener à terme une recherche.', 75),
(542, 'Etre capable de récupérer l''information recueillie.', 75),
(543, 'Etre capable de qualifier l''information recueillie.', 75),
(544, 'Etre capable d''adresser un message à un seul ou à plusieurs destinataires.', 76),
(545, 'Etre capable de consulter et d''imprimer un message reçu ou à envoyer.', 76),
(546, 'Etre capable de faire suivre ou de répondre à un message.', 76);

-- =============================================================================
--#TITLE(Activation des contraintes)

--#STEP(ALWAYS_RUN)
SET FOREIGN_KEY_CHECKS = 1;
