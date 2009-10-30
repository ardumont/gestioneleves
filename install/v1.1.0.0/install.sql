-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 10, 2008 at 07:24 PM
-- Server version: 5.0.60
-- PHP Version: 5.2.6-pl2-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `baseeval`
--

DROP DATABASE IF EXISTS baseeval;
CREATE DATABASE baseeval;

USE baseeval;

-- --------------------------------------------------------

--
-- Table structure for table `CLASSES`
--

CREATE TABLE IF NOT EXISTS `CLASSES` (
  `CLASSE_ID` int(11) NOT NULL auto_increment,
  `CLASSE_NOM` varchar(10) NOT NULL,
  `CLASSE_ANNEE_SCOLAIRE` varchar(9) NOT NULL,
  `ID_ECOLE` int(11) NOT NULL,
  PRIMARY KEY  (`CLASSE_ID`),
  KEY `ID_ECOLE` (`ID_ECOLE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `CLASSES`
--

INSERT INTO `CLASSES` (`CLASSE_ID`, `CLASSE_NOM`, `CLASSE_ANNEE_SCOLAIRE`, `ID_ECOLE`) VALUES
(1, 'ce2c', '2006-2007', 2),
(2, 'ce1b', '2007-2008', 1),
(6, 'ce1b', '2008-2009', 1);

-- --------------------------------------------------------

--
-- Table structure for table `COMPETENCES`
--

CREATE TABLE IF NOT EXISTS `COMPETENCES` (
  `COMPETENCE_ID` int(11) NOT NULL auto_increment,
  `COMPETENCE_NOM` varchar(200) NOT NULL,
  `ID_MATIERE` int(11) NOT NULL,
  PRIMARY KEY  (`COMPETENCE_ID`),
  KEY `ID_MATIERE` (`ID_MATIERE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=193 ;

--
-- Dumping data for table `COMPETENCES`
--

INSERT INTO `COMPETENCES` (`COMPETENCE_ID`, `COMPETENCE_NOM`, `ID_MATIERE`) VALUES
(1, 'Etre capable d''interpréter de mémoire plus de 10 chansons parmi celles qui ont été apprises', 43),
(2, 'Contrôler volontairement sa voix et son attitude corporelle pour chanter', 43),
(3, 'Tenir sa voix et sa place en formation chorale', 43),
(4, 'Assumer son rôle dans un travail d''accompagnement', 43),
(5, 'Etre capable de répondre aux sollicitations de l''adulte en se faisant comprendre dès la fin de la première année de la scolarité', 1),
(6, 'Prendre l''initiative d''un échange et le tenir au-delà de la première réponse', 1),
(7, 'Participer à un échange collectif en acceptant d''écouter autrui en attendant son tour de parole et en restant dans le propos de l''échange', 1),
(8, 'répondre aux sollicitations de l''adulte', 1),
(9, 'prendre l''initiative d''un échange', 1),
(10, 'participer à un échange', 1),
(11, 'comprendre les consignes de la classe', 2),
(12, 'dire ce que l''on fait ou ce que fait un camarade', 2),
(13, 'prêter sa voix à une marionnette', 2),
(14, 'identifier et connaître le rôle des adultes de l''école', 45),
(15, 'respecter les règles de la vie commune', 45),
(16, 'adapter ses déplacements à différents types d''environnements', 5),
(17, 'coopérer et s''opposer individuellement et collectivement', 5),
(18, 'réaliser des actions à visée artistique, esthétique et expressive', 5),
(19, 'décrire, comparer, classer des perceptions élémentaires', 46),
(20, 'associer les organes des sens à des perceptions', 46),
(21, 'reconnaître  des matières et des objets', 47),
(22, 'utiliser des appareils à piles', 47),
(23, 'utiliser des objets programmables', 12),
(24, 'utiliser le dessin comme moyen d''expression', 48),
(25, 'réaliser une composition en plan ou en volume', 48),
(26, 'dire ce qu''on fait, ce qu''on voit', 48),
(27, 'avoir mémorisé un répertoire varié de chansons et comptines', 49),
(28, 'comprendre une histoire et la reformuler dans ses propres mots', 3),
(29, 'raconter un conte connu', 3),
(30, 'inventer une courte histoire', 3),
(31, 'écrire son prénom en capitales d''imprimerie', 4),
(32, 'reconnaître son prénom en capitales d''imprimerie', 4),
(33, 'connaître le nom des lettres de l''alphabet', 4),
(34, 'comparer des évènements en fonction de leur durée', 50),
(35, 'expliquer et comprendre la situation temporelle d''un évènements', 50),
(36, 'situer des évènements les uns par rapport aux autres', 50),
(37, 'comparer, classer et ranger des objets selon leur taille, leur masse, leur contenance', 51),
(38, 'reconnaître et classer et nommer des formes simples', 51),
(39, 'dénombrer une quantité en utilisant la suite des nombres connus', 52),
(40, 'associer forme orale et écrite des nombres', 52),
(41, 'résoudre des problèmes portant sur une quantité', 52),
(42, 'reconnaître globalement et exprimer une quantité', 52),
(43, 'Ecouter autrui, demander des explications et accepter les orientations de la discussion', 53),
(44, 'Exposer son point de vue et ses réactions dans un débat', 53),
(45, 'Faire des propositions d''interprétation pour oraliser un texte appris par coeur ou en le lisant', 53),
(46, 'Rendre compte oralement d''une démarche utilisée', 53),
(47, 'Résoudre des problèmes en utilisant une procédure experte', 20),
(48, 'Résoudre des problèmes en utilisant une procédure personnelle', 20),
(49, 'Désigner à l''oral et à l''écrit des nombres entiers naturels inférieurs à 1000', 54),
(50, 'Comparer, ranger, encadrer des nombres et situer des nombres', 54),
(51, 'Connaître les doubles et moitiés de nombres d''usage courant', 54),
(52, 'Se repérer dans son environnement proche, s''orienter, se déplacer', 55),
(53, 'Commencer à représenter l''environnement proche', 55),
(54, 'Décrire oralement et localiser les différents éléments d''un espace organisé', 55),
(55, 'Lire en la comprenant la description d''un paysage, d''un environnement', 55),
(56, 'Repérer les éléments étudiés sur des photographies prises de différents points de vue, sur des plans', 55),
(57, 'Savoir retrouver le rôle de l''homme dans la transformation d''un paysage', 55),
(58, 'Situer les milieux étudiés sur une carte simple ou un globe.', 55),
(59, 'Utiliser le dessin dans ses diverses fonctions (expression, anticipation, enregistrement)', 29),
(60, 'Expérimenter des matériaux, des supports, des outils, constater des effets produits et réinvestir tout ou partie des constats dans une nouvelle production', 29),
(61, 'Combiner plusieurs opérations plastiques pour réaliser une production en deux ou trois dimensions, individuelle ou collective', 29),
(62, 'Produire des images en visant la maîtrise des effets et du sens', 29),
(63, 'Etablir des relations entre les démarches et procédés repérés dans les oeuvres et sa propre production', 29),
(64, 'Reconnaître et nommer certaines oeuvres d''artistes et les mettre en relation les unes par rapport aux autres', 29),
(65, 'Comprendre les informations explicites d''un texte', 19),
(66, 'Dégager le thème d''un texte littéraire', 19),
(67, 'Avoir compris et retenu le système alphabétique de codage de l''écriture', 19),
(68, 'Avoir compris et retenu les correspondances régulières entre graphèmes et phonèmes', 19),
(69, 'Proposer une écriture possible (et phonétiquement correcte) pour un mot régulier', 19),
(70, 'Déchiffrer un mot que l''on ne connaît pas', 19),
(71, 'Identifier instantanément la plupart des mots courts et les mots longs les plus fréquents', 19),
(72, 'Lire à haute voix un court passage', 19),
(73, 'Relire seul un album illustré lu en classe', 19),
(74, 'Connaître  reconstruire et utiliser  rapidement les résultats des tables d''addition (de 1 à 9.)', 22),
(75, 'Trouver rapidement le complément d''un nombre à la dizaine immédiatement supérieure', 22),
(76, 'Connaître et utiliser les tables de multiplication par deux, cinq et dix', 22),
(77, 'Calculer des sommes en ligne ou par addition posée en colonne', 22),
(78, 'Organiser et traiter des calculs additifs, soustractifs et multiplicatifs sur les nombres entiers', 22),
(79, 'Utiliser une calculatrice.', 22),
(80, 'Connaître et utiliser le vocabulaire lié aux positions relatives d''objets', 23),
(81, 'Situer un objet, une personne par rapport à soi ou par rapport à une autre personne ou à un autre objet', 23),
(82, 'Situer des objets d''un espace réel sur une maquette ou un plan', 23),
(83, 'Repérer et coder des cases et des noeuds sur un quadrillage', 23),
(84, 'Percevoir et vérifier les relations : alignement, angle droit, axe de symétrie, égalité de longueurs', 23),
(85, 'Distinguer et décrire des solides : cube, pavé droit', 23),
(86, 'Distinguer, décrire et reproduire des figures planes : triangle,carré, rectangle, cercle', 23),
(87, 'Adapter ses déplacements à différents types d''environnements', 56),
(88, 'S''opposer individuellement et/ou collectivement', 56),
(89, 'Concevoir et réaliser des actions à visée artistique, esthétique et/ou expressive', 56),
(90, 'S''engager lucidement dans l''action', 56),
(91, 'Mesurer et apprécier les effets de l''activité', 56),
(92, 'Retenir des savoirs précis sur les différentes activités physiques et sportives rencontrées', 56),
(93, 'Orthographier la plupart des ''petits mots'' fréquents', 57),
(94, 'Ecrire la plupart des mots en respectant les caractères phoniques', 57),
(95, 'Utiliser correctement les marques typographiques de la phrase (point et majuscule), commencer à se servir des virgules', 57),
(96, 'Copier sans erreur un texte de quelques lignes et en utilisant une écriture cursive et lisible', 57),
(97, 'En situation d''écriture spontanée ou sous dictée, marquer les accords en nombre et en genre dans le groupe nominal régulier', 57),
(98, 'En situation d''écriture spontanée ou sous dictée, marquer l''accord en nombre du verbe et du sujet dans toutes les phrases où l''ordre syntaxique régulier est respecté.', 57),
(99, 'Comparer des objets selon leur longueur, leur masse ou leur volume', 24),
(100, 'Utiliser des instruments de mesure', 24),
(101, 'Connaître les unités usuelles : m, cm, kg, g,.l.', 24),
(102, 'Connaître les jours de la semaine et les mois de l''année', 24),
(103, 'Connaître la relation entre heure et minute', 24),
(104, 'Utiliser un calendrier, un sablier ou un chronomètre pour comparer des durées.', 24),
(105, 'Chanter juste en contrôlant l''intonation à l''oreille interpréter de mémoire une dizaine de chansons simples par année', 30),
(106, 'Mobiliser, soit de façon autonome, soit sur rappel, les habitudes corporelles pour chanter (posture physique, aisance respiratoire, anticipation'')', 30),
(107, 'Ecouter les autres, pratiquer l''écoute intérieure de courts extraits', 30),
(108, 'Isoler au travers d''écoutes répétées quelques éléments musicaux', 30),
(109, 'Produire des rythmes simples', 30),
(110, 'Traduire des productions sonores sous forme de représentations graphiques, après appui éventuel sur des évolutions corporelles.', 30),
(111, 'se servir des catalogues (papiers ou informatiques) de la BCD pour trouver un livre', 35),
(112, 'se servir des informations portées sur la couverture et la page de titre d''un livre pour savoir s''il correspond au livre que l''on cherche', 35),
(113, 'comprendre en le lisant silencieusement un texte littéraire court (petite nouvelle, extrait... ) de complexité adaptée à l''âge et à la culture des élèves en s''appuyant sur un traitement correct des su', 35),
(114, 'lire en le comprenant un texte littéraire long, mettre en mémoire ce qui a été lu (synthèses successives) en mobilisant ses souvenirs lors des reprises', 35),
(115, 'distinguer les grandes périodes historiques, pouvoir les situer chronologiquement, commencer à connaître pour chacune d''entre elles différentes formes de pouvoir, des groupes sociaux, et quelques prod', 58),
(116, 'classer des documents selon leur nature, leur date et leur origine', 58),
(117, 'savoir utiliser les connaissances historiques en éducation civique et dans les autres enseignements, en particulier dans le domaine artistique', 58),
(118, 'consulter une encyclopédie et les pages Internet', 58),
(119, 'utiliser à bon escient les temps du passé rencontrés dans les récits historiques', 58),
(120, 'résoudre des problèmes en utilisant les connaissances sur les nombres naturels et sur les opérations étudiées', 40),
(121, 'résoudre des problèmes relevant de la proportionnalité en utilisant des raisonnements personnels appropriés (dont des problèmes relatifs aux pourcentages, aux échelles, aux vitesses moyennes ou aux co', 40),
(122, 'organiser des séries de données (listes, tableaux...)', 40),
(123, 'lire, interpréter et construire quelques représentations : diagrammes, graphiques.', 40),
(124, 'poser des questions précises et cohérentes à propos d''une situation d''observation ou d''expérience', 41),
(125, 'imaginer et réaliser un dispositif expérimental susceptible de répondre aux questions que l''on se pose, en s''appuyant sur des observations, des mesures appropriées ou un schéma', 41),
(126, 'réaliser un montage électrique à partir d''un schéma', 41),
(127, 'utiliser des instruments d''observation et de mesure : double décimètre, loupe, boussole, balance, chronomètre ou horloge, thermomètre', 41),
(128, 'recommencer une expérience en ne modifiant qu''un seul facteur par rapport à l''expérience précédente', 41),
(129, 'mettre en relation des données, en faire une représentation schématique et l''interpréter, mettre en relation des observations réalisées en classe et des savoirs que l''on trouve dans une documentation', 41),
(130, 'participer à la préparation d''une enquête ou d''une visite en élaborant un protocole d''observation ou un questionnaire', 41),
(131, 'rédiger un compte rendu intégrant schéma d''expérience ou dessin d''observation', 41),
(132, 'produire, créer, modifier et exploiter un document à l''aide d''un logiciel de traitement de texte', 41),
(133, 'communiquer au moyen d''une messagerie électronique', 41),
(134, 'repérer une case ou un point sur un quadrillage', 40),
(135, 'utiliser un plan ou une carte pour situer un objet, anticiper ou réaliser un déplacement, évaluer une distance', 40),
(136, 'effectuer une recherche dans un atlas imprimé et dans un atlas numérique', 59),
(137, 'mettre en relation des cartes à différentes échelles pour localiser un phénomène', 59),
(138, 'réaliser un croquis spatial simple', 59),
(139, 'situer le lieu où se trouve l''école dans l''espace local et régional', 59),
(140, 'situer la France dans l''espace mondial', 59),
(141, 'situer les positions des principales villes françaises et des grands axes de communication français', 59),
(142, 'situer l''Europe, ses principaux Etats, ses principales villes dans l''espace mondial', 59),
(143, 'appliquer les compétences acquises dans le domaine du calcul à l''usage de la monnaie (euros, centimes)', 59),
(144, 'effectuer des manipulations dans un texte écrit (déplacement, remplacement, expansion, réduction)', 60),
(145, 'identifier les verbes dans une phrase  manipuler les différents types de compléments des verbes les plus fréquents', 60),
(146, 'identifier les noms dans une phrase', 60),
(147, 'manipuler les différentes déterminations du nom (articles, déterminants possessifs, démonstratifs, indéfinis)', 60),
(148, 'manipuler les différentes expansions du nom (adjectifs qualificatifs, relatives, compléments du nom)', 60),
(149, 'trouver le présent, le passé composé, l''imparfait, le passé simple, le futur, le conditionnel présent et le présent du subjonctif des verbes réguliers (à partir des règles d''engendrement)', 60),
(150, 'marquer l''accord sujet/verbe (situations régulières)', 60),
(151, 'repérer et réaliser les chaînes d''accords dans le groupe nominal', 60),
(152, 'utiliser un dictionnaire pour retrouver la définition d''un mot dans un emploi déterminé', 60),
(153, 'déterminer la valeur de chacun des chiffres composant une écriture à virgule, en fonction de sa position', 40),
(154, 'passer, pour un nombre décimal, d''une écriture fractionnaire (fractions décimales) à une écriture à virgule (et réciproquement)', 40),
(155, 'utiliser les nombres décimaux pour exprimer la mesure de la longueur d''un segment, celle de l''aire d''une surface (une unité étant donnée), ou pour repérer un point sur une droite graduée régulièrement', 40),
(156, 'écrire et interpréter sous forme décimale une mesure donnée avec plusieurs unités (et réciproquement)', 40),
(157, 'produire des décompositions liées à une écriture à virgule, en utilisant 10', 40),
(158, '100', 40),
(159, '1000...et0,l', 40),
(160, '0,01', 40),
(161, '0,001...', 40),
(162, 'produire des suites écrites ou orales de 0,1 en0,l,de0,01 en 0,0l...', 40),
(163, 'associer les désignations orales et l''écriture chiffrée d''un nombre décimal', 40),
(164, 'réaliser une production en deux ou trois dimensions, individuelle ou collective, menée à partir de consignes précises', 61),
(165, 'choisir, manipuler et combiner des matériaux, des supports, des outils', 61),
(166, 'témoigner d''une expérience, décrire une image, s''exprimer sur une oeuvre', 61),
(167, 'identifier différents types d''images en justifiant son point de vue', 61),
(168, 'réinvestir dans d''autres disciplines les apports des arts visuels pouvoir interpréter de mémoire plus de dix chansons parmi celles qui ont été apprises', 61),
(169, 'contrôler volontairement sa voix et son attitude corporelle pour chanter', 61),
(170, 'tenir sa voix et sa place en formation chorale, notamment dans une polyphonie', 61),
(171, 'assumer son rôle dans un travail d''accompagnement', 61),
(172, 'soutenir une écoute prolongée, utiliser des consignes d''écoute', 61),
(173, 'repérer des éléments musicaux caractéristiques, les désigner et caractériser leur organisation (succession, simultanéité, ruptures... ) en faisant appel à un lexique approprié', 61),
(174, 'reconnaître une oeuvre du répertoire travaillé, la situer dans son contexte de création, porter à son égard un jugement esthétique', 61),
(175, 'réemployer des savoir-faire au profit d''une production musicale ou chorégraphique inventée, personnelle ou collective', 61),
(176, 'témoigner de son aisance à évoluer dans une danse collective et dans des dispositifs scéniques divers', 61),
(177, 'exprimer son appréciation pour qualifier une réalisation dansée, chantée ou jouée, à la fois comme acteur et comme spectateur', 61),
(178, 'pouvoir interpréter de mémoire plus de dix chansons parmi celles qui ont été apprises   contrôler volontairement sa voix et son attitude corporelle pour chanter', 43),
(179, 'tenir sa voix et sa place en formation chorale, notamment dans une polyphonie', 43),
(180, 'soutenir une écoute prolongée, utiliser des consignes d''écoute', 43),
(181, 'repérer des éléments musicaux caractéristiques, les désigner et caractériser leur organisation (succession, simultanéité, ruptures... ) en faisant appel à un lexique approprié', 43),
(182, 'reconnaître une oeuvre du répertoire travaillé, la situer dans son contexte de création, porter à son égard un jugement esthétique  réemployer des savoir-faire au profit d''une production musicale ou c', 43),
(183, 'témoigner de son aisance à évoluer dans une danse collective et dans des dispositifs scéniques divers', 43),
(184, 'exprimer son appréciation pour qualifier une réalisation dansée, chantée ou jouée, à la fois comme acteur et comme spectateur', 43),
(185, 'avoir mémorisé un répertoire varié de chansons et comptines marquer la pulsation', 49),
(186, 'tenir sa place dans les activités collectives', 49),
(187, 's''exprimer sur un extrait musical entendu', 49),
(188, 'comprendre en le lisant silencieusement un texte littéraire court (petite nouvelle, extrait... ) de complexité adaptée à l''âge et à la culture des élèves en s''appuyant sur un traitement correct des su', 35),
(189, 'distinguer les grandes périodes historiques, pouvoir les situer chronologiquement, commencer à connaître pour chacune d''entre elles différentes formes de pouvoir, des groupes sociaux, et quelques prod', 58),
(190, 'résoudre des problèmes relevant de la proportionnalité en utilisant des raisonnements personnels appropriés (dont des problèmes relatifs aux pourcentages, aux échelles, aux vitesses moyennes ou aux co', 40),
(191, 'utiliser les nombres décimaux pour exprimer la mesure de la longueur d''un segment, celle de l''aire d''une surface (une unité étant donnée), ou pour repérer un point sur une droite graduée régulièrement', 40),
(192, 'reconnaître une oeuvre du répertoire travaillé, la situer dans son contexte de création, porter à son égard un jugement esthétique  réemployer des savoir-faire au profit d''une production musicale ou c', 43);

-- --------------------------------------------------------

--
-- Table structure for table `CYCLES`
--

CREATE TABLE IF NOT EXISTS `CYCLES` (
  `CYCLE_ID` int(11) NOT NULL auto_increment,
  `CYCLE_NOM` varchar(50) NOT NULL,
  PRIMARY KEY  (`CYCLE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `CYCLES`
--

INSERT INTO `CYCLES` (`CYCLE_ID`, `CYCLE_NOM`) VALUES
(1, 'I'),
(2, 'II'),
(3, 'III');

-- --------------------------------------------------------

--
-- Table structure for table `DOMAINES`
--

CREATE TABLE IF NOT EXISTS `DOMAINES` (
  `DOMAINE_ID` int(11) NOT NULL auto_increment,
  `DOMAINE_NOM` varchar(100) NOT NULL,
  `ID_CYCLE` int(11) NOT NULL,
  PRIMARY KEY  (`DOMAINE_ID`),
  KEY `ID_CYCLE` (`ID_CYCLE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `DOMAINES`
--

INSERT INTO `DOMAINES` (`DOMAINE_ID`, `DOMAINE_NOM`, `ID_CYCLE`) VALUES
(1, 'Le langage au coeur des apprentissages', 1),
(2, 'Vivre ensemble', 1),
(3, 'Agir et s''exprimer avec son corps', 1),
(4, 'Découverte du monde', 1),
(5, 'La sensibilité, l''imagination, la création', 1),
(6, 'Maîtrise du langage et de la langue française', 2),
(7, 'Mathématiques', 2),
(8, 'Vivre ensemble', 2),
(9, 'Découvrir le monde', 2),
(10, 'Langues étrangères ou régionales', 2),
(11, 'Education artistique', 2),
(12, 'Education physique et sportive', 2),
(13, 'Langue française, éducation littéraire et humaine', 3),
(14, 'Education scientifique', 3),
(15, 'Education artistique', 3),
(16, 'Education physique et sportive', 3),
(18, 'Découvrir le monde', 1),
(19, 'La sensibilité, l''imagination et la création', 1),
(21, 'eps', 2),
(22, 'Education artistique, arts visuels', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ECOLES`
--

CREATE TABLE IF NOT EXISTS `ECOLES` (
  `ECOLE_ID` int(11) NOT NULL auto_increment,
  `ECOLE_NOM` varchar(50) NOT NULL,
  `ECOLE_VILLE` varchar(50) NOT NULL,
  `ECOLE_DEPARTEMENT` varchar(5) NOT NULL,
  PRIMARY KEY  (`ECOLE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ECOLES`
--

INSERT INTO `ECOLES` (`ECOLE_ID`, `ECOLE_NOM`, `ECOLE_VILLE`, `ECOLE_DEPARTEMENT`) VALUES
(1, 'Edouard Vaillant', 'Le Blanc Mesnil', '93150'),
(2, 'Jules Vallès', 'Saint-Denis', '93200');

-- --------------------------------------------------------

--
-- Table structure for table `ELEVES`
--

CREATE TABLE IF NOT EXISTS `ELEVES` (
  `ELEVE_ID` int(11) NOT NULL auto_increment,
  `ELEVE_NOM` varchar(50) NOT NULL,
  `ELEVE_ACTIF` tinyint(1) default '1',
  `ELEVE_DATE_NAISSANCE` date default NULL,
  PRIMARY KEY  (`ELEVE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=72 ;

--
-- Dumping data for table `ELEVES`
--

INSERT INTO `ELEVES` (`ELEVE_ID`, `ELEVE_NOM`, `ELEVE_ACTIF`, `ELEVE_DATE_NAISSANCE`) VALUES
(1, 'ATIA Lara', 1, '1998-03-26'),
(2, 'BERNARDO Kévin', 1, '1998-10-12'),
(3, 'BONJEAN-TRAÃA Nahida', 1, '1998-08-01'),
(4, 'BORKOWSKI Souad', 1, '1998-01-10'),
(5, 'BOUNAROUF Nisrine', 1, '1998-12-10'),
(6, 'BROU Marie-Claude', 1, '1998-08-07'),
(7, 'CISSE Boubou', 1, '1998-05-04'),
(8, 'DIOURI Manal', 1, '1998-04-29'),
(9, 'DJORDJEVIC Sanéla', 1, '1998-07-02'),
(10, 'FARKAS Iosif', 1, '1997-02-24'),
(11, 'FERHAT Kathia', 1, '1998-01-10'),
(12, 'FORTUNE Axelle', 1, '1998-02-17'),
(13, 'HADJ SAÃD Nassim', 1, '1997-12-03'),
(14, 'KARAMOKO Sindou', 1, '1998-04-06'),
(15, 'KIMAKUIZA Brandon', 1, '1998-09-05'),
(16, 'LAGRAVE Délizia', 1, '1997-02-21'),
(17, 'OURTIRANE Sara', 1, '1998-10-22'),
(18, 'PERRONE Shana', 1, '1998-09-27'),
(19, 'SIMAO-MANUEL Calvin', 1, '1997-12-07'),
(20, 'SOUAM Lahlou', 1, '1998-06-02'),
(21, 'TOUBI Soumia', 1, '1998-10-08'),
(22, 'ANDRE Rachel', 1, '1999-11-19'),
(23, 'BALABAN Emré', 1, '2000-05-30'),
(24, 'BARTHOLET Prince', 1, '2000-08-09'),
(25, 'BOUABID Charaf', 1, '2000-07-08'),
(26, 'DIAS Margaux', 1, '1999-11-19'),
(27, 'HABASHY Marcelino', 1, '2000-03-14'),
(28, 'HAMILA Ahmed', 1, '2000-02-18'),
(29, 'IDRI Amanda', 1, '2000-10-27'),
(30, 'KARUNAKARAN Nivetha', 1, '2000-06-19'),
(31, 'KENNY Clovis', 1, '2000-09-06'),
(32, 'KUBEZYK Kelly', 1, '2000-03-13'),
(33, 'LAL Joyti', 1, '2000-05-04'),
(34, 'MAHIOUT Laurine', 1, '1999-07-08'),
(35, 'MALIK Iraj', 1, '2000-02-19'),
(36, 'MUNAWAR Junaid', 1, '1999-02-10'),
(37, 'NACER Yacine', 1, '2000-07-04'),
(38, 'RAHMOUN Ousama', 1, '2000-04-08'),
(39, 'SIMOES Sylvain', 1, '1999-12-31'),
(40, 'SOVILJ Stefan', 1, '2000-05-28'),
(41, 'TWAYA Enoch', 1, '2000-06-16'),
(42, 'WAGGEH Fatoumata', 1, '1998-03-16'),
(43, 'WIJAYAKUMAR Diepthyia', 1, '2000-06-16'),
(44, 'JAFER Akmez', 1, NULL),
(46, 'ARUDSELVAM Abinadahab', 1, NULL),
(47, 'BOUKERFA Yanis', 1, NULL),
(48, 'RAJENDRAN Sharan', 1, NULL),
(49, 'RAPOSO Brandon', 1, NULL),
(50, 'ICHELMAN Lukas', 1, NULL),
(51, 'FEHER Vasile', 1, NULL),
(52, 'DIAOUNE Demba', 1, NULL),
(53, 'ARMEN Moustafa', 1, NULL),
(54, 'MACHADO Flavio', 1, NULL),
(55, 'PADUCH Florent', 1, NULL),
(56, 'MAOUCHA Ryiad', 1, NULL),
(57, 'LOCOH DONOU Geoffrey', 1, NULL),
(58, 'SAFA Mohamed', 1, NULL),
(59, 'NAOUSSY Lindsay', 1, NULL),
(60, 'MELITI Alia', 1, NULL),
(61, 'AYADI Assiya', 1, NULL),
(62, 'SAINT CY Laura', 1, NULL),
(63, 'IDJERI Lehna', 1, NULL),
(64, 'SATGUNARAJAH Saranja', 1, NULL),
(65, 'ABOUELDEYAR Sarah', 1, NULL),
(66, 'JELLOUL Yasmine', 1, NULL),
(67, 'ESTEVES Alexia', 1, NULL),
(68, 'MAHI Inès', 1, NULL),
(69, 'VALERE Mélissa', 1, NULL),
(70, 'DOS SANTOS Loréna', 1, NULL),
(71, 'GARNIER Tamara', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ELEVE_CLASSE`
--

CREATE TABLE IF NOT EXISTS `ELEVE_CLASSE` (
  `ID_ELEVE` int(11) NOT NULL,
  `ID_CLASSE` int(11) NOT NULL,
  PRIMARY KEY  (`ID_ELEVE`,`ID_CLASSE`),
  KEY `ID_CLASSE` (`ID_CLASSE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ELEVE_CLASSE`
--

INSERT INTO `ELEVE_CLASSE` (`ID_ELEVE`, `ID_CLASSE`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(35, 6),
(44, 6),
(46, 6),
(47, 6),
(48, 6),
(49, 6),
(50, 6),
(51, 6),
(52, 6),
(53, 6),
(54, 6),
(55, 6),
(56, 6),
(57, 6),
(58, 6),
(59, 6),
(60, 6),
(61, 6),
(62, 6),
(63, 6),
(64, 6),
(65, 6),
(66, 6),
(67, 6),
(68, 6),
(69, 6),
(70, 6),
(71, 6);

-- --------------------------------------------------------

--
-- Table structure for table `EVALUATIONS_COLLECTIVES`
--

CREATE TABLE IF NOT EXISTS `EVALUATIONS_COLLECTIVES` (
  `EVAL_COL_ID` int(11) NOT NULL auto_increment,
  `EVAL_COL_NOM` varchar(50) NOT NULL,
  `EVAL_COL_DESCRIPTION` varchar(200) NOT NULL,
  `EVAL_COL_DATE` date NOT NULL,
  `ID_PERIODE` int(11) NOT NULL,
  `ID_CLASSE` int(11) NOT NULL,
  PRIMARY KEY  (`EVAL_COL_ID`),
  KEY `ID_PERIODE` (`ID_PERIODE`),
  KEY `ID_CLASSE` (`ID_CLASSE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `EVALUATIONS_COLLECTIVES`
--

INSERT INTO `EVALUATIONS_COLLECTIVES` (`EVAL_COL_ID`, `EVAL_COL_NOM`, `EVAL_COL_DESCRIPTION`, `EVAL_COL_DATE`, `ID_PERIODE`, `ID_CLASSE`) VALUES
(1, 'test ce1b', 'test', '2008-03-10', 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `EVALUATIONS_INDIVIDUELLES`
--

CREATE TABLE IF NOT EXISTS `EVALUATIONS_INDIVIDUELLES` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `EVALUATIONS_INDIVIDUELLES`
--

INSERT INTO `EVALUATIONS_INDIVIDUELLES` (`EVAL_IND_ID`, `EVAL_IND_COMMENTAIRE`, `EVAL_IND_DATE`, `ID_COMPETENCE`, `ID_EVAL_COL`, `ID_ELEVE`, `ID_NOTE`) VALUES
(1, '', '0000-00-00', 61, 1, 23, 1);

-- --------------------------------------------------------

--
-- Table structure for table `MATIERES`
--

CREATE TABLE IF NOT EXISTS `MATIERES` (
  `MATIERE_ID` int(11) NOT NULL auto_increment,
  `MATIERE_NOM` varchar(100) NOT NULL,
  `ID_DOMAINE` int(11) NOT NULL,
  PRIMARY KEY  (`MATIERE_ID`),
  KEY `ID_DOMAINE` (`ID_DOMAINE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

--
-- Dumping data for table `MATIERES`
--

INSERT INTO `MATIERES` (`MATIERE_ID`, `MATIERE_NOM`, `ID_DOMAINE`) VALUES
(1, 'Communication', 1),
(2, 'Langage d''accompagnement de l''action', 1),
(3, 'Langage d''évocation', 1),
(4, 'Langage écrit', 1),
(5, 'Réaliser une action que l''on peut mesurer', 3),
(6, 'Adapter ses déplacements à différents types d''environnements', 3),
(7, 'Coopérer et s''opposer individuellement et collectivement', 3),
(8, 'Réaliser des activités à visée artistique, esthétique ou expressive', 3),
(9, 'Découverte sensorielle', 4),
(10, 'Exploration du monde de la matière et  des objets', 3),
(11, 'Découvrir le monde du vivant, de l''environnement, de l''hygiène et de la santé.', 4),
(12, 'Repérages dans l''espace', 4),
(13, 'Le temps qui passe', 4),
(14, 'Découverte des formes et des grandeurs', 4),
(15, 'Approche des quantités et des nombres', 4),
(16, 'Le regard et le geste', 5),
(17, 'La voix et l''écoute', 5),
(18, 'Maîtrise du langage oral', 6),
(19, 'Lecture et écriture', 6),
(20, 'Exploitation de données numériques', 7),
(21, 'Connaissance des nombres entiers naturels', 7),
(22, 'Calcul', 7),
(23, 'Espace et géométrie', 7),
(24, 'Grandeurs et mesures', 7),
(25, 'Domaine de l''espace', 9),
(26, 'Domaine du temps', 9),
(27, 'Domaine du vivant', 9),
(28, 'Domaine de la matière, des objets et des techniques de l''information et de la communication', 9),
(29, 'Arts visuels', 11),
(30, 'Education musicale', 11),
(31, 'Réaliser une performance mesurée', 12),
(32, 'Adapter ses déplacements à différents types d''environnements', 12),
(33, 'S''opposer individuellement ou collectivement', 12),
(34, 'Concevoir et réaliser des actions à visée artistique, esthétique ou expressive', 12),
(35, 'Littérature', 13),
(36, 'ORLF', 13),
(37, 'Langue étrangère ou régionale', 13),
(38, 'Histoire et géographie', 13),
(39, 'Débat réglé', 13),
(40, 'Mathématiques', 14),
(41, 'Sciences expérimentales et technologie', 14),
(42, 'Arts visuels', 15),
(43, 'Education musicale', 15),
(45, 'jouer un rôle dans un groupe', 2),
(46, 'Domaine sensoriel', 18),
(47, 'Domaine de la matière et des objets', 18),
(48, 'Le regard et le geste', 19),
(49, 'La voix et l''écoute', 19),
(50, 'Domaine de la structuration du temps', 18),
(51, 'Formes et grandeurs', 18),
(52, 'Quantités et nombres', 18),
(53, 'Communiquer', 6),
(54, 'connaissance des nombres entiers', 7),
(55, 'Domaine de l''espace', 9),
(56, 'Réaliser une performance mesurée', 21),
(57, 'Ecriture et orthographe', 6),
(58, 'histoire', 13),
(59, 'géographie', 13),
(60, 'observation réfléchie de la langue française', 13),
(61, 'utiliser le dessin dans ses différentes fonctions en utilisant diverses techniques', 22);

-- --------------------------------------------------------

--
-- Table structure for table `NIVEAUX`
--

CREATE TABLE IF NOT EXISTS `NIVEAUX` (
  `NIVEAU_ID` int(11) NOT NULL auto_increment,
  `NIVEAU_NOM` varchar(50) NOT NULL,
  `ID_CYCLE` int(11) NOT NULL,
  PRIMARY KEY  (`NIVEAU_ID`),
  KEY `ID_CYCLE` (`ID_CYCLE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `NIVEAUX`
--

INSERT INTO `NIVEAUX` (`NIVEAU_ID`, `NIVEAU_NOM`, `ID_CYCLE`) VALUES
(1, 'ps', 1),
(2, 'ms', 1),
(3, 'gs', 2),
(4, 'cp', 2),
(5, 'ce1', 2),
(6, 'ce2', 3),
(7, 'cm1', 3),
(8, 'cm2', 3);

-- --------------------------------------------------------

--
-- Table structure for table `NIVEAU_CLASSE`
--

CREATE TABLE IF NOT EXISTS `NIVEAU_CLASSE` (
  `ID_NIVEAU` int(11) NOT NULL,
  `ID_CLASSE` int(11) NOT NULL,
  PRIMARY KEY  (`ID_NIVEAU`,`ID_CLASSE`),
  KEY `ID_CLASSE` (`ID_CLASSE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `NIVEAU_CLASSE`
--

INSERT INTO `NIVEAU_CLASSE` (`ID_NIVEAU`, `ID_CLASSE`) VALUES
(6, 1),
(5, 2),
(5, 6);

-- --------------------------------------------------------

--
-- Table structure for table `NOTES`
--

CREATE TABLE IF NOT EXISTS `NOTES` (
  `NOTE_ID` int(11) NOT NULL auto_increment,
  `NOTE_NOM` varchar(50) NOT NULL,
  `NOTE_LABEL` varchar(3) NOT NULL,
  `NOTE_NOTE` int(11) NOT NULL,
  PRIMARY KEY  (`NOTE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `NOTES`
--

INSERT INTO `NOTES` (`NOTE_ID`, `NOTE_NOM`, `NOTE_LABEL`, `NOTE_NOTE`) VALUES
(1, 'Acquis', 'A', 20),
(2, 'A renforcer', 'AR', 15),
(3, 'En cours d''acquisition', 'ECA', 10),
(4, 'Non Acquis', 'NA', 5);

-- --------------------------------------------------------

--
-- Table structure for table `PERIODES`
--

CREATE TABLE IF NOT EXISTS `PERIODES` (
  `PERIODE_ID` int(11) NOT NULL auto_increment,
  `PERIODE_NOM` varchar(10) NOT NULL,
  `PERIODE_DATE_DEBUT` varchar(16) NOT NULL,
  `PERIODE_DATE_FIN` varchar(16) NOT NULL,
  PRIMARY KEY  (`PERIODE_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `PERIODES`
--

INSERT INTO `PERIODES` (`PERIODE_ID`, `PERIODE_NOM`, `PERIODE_DATE_DEBUT`, `PERIODE_DATE_FIN`) VALUES
(1, 'p1', 'septembre', 'toussaint'),
(2, 'p2', 'toussaint', 'noël'),
(3, 'p3', 'noël', 'vacances d''hiver'),
(4, 'p4', 'vacances d''hiver', 'pâques'),
(5, 'p5', 'pâques', 'été');

-- --------------------------------------------------------

--
-- Table structure for table `PROFESSEURS`
--

CREATE TABLE IF NOT EXISTS `PROFESSEURS` (
  `PROFESSEUR_ID` int(11) NOT NULL auto_increment,
  `PROFESSEUR_NOM` varchar(30) NOT NULL,
  `PROFESSEUR_PWD` varchar(50) NOT NULL,  
  PRIMARY KEY  (`PROFESSEUR_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `PROFESSEURS`
--

INSERT INTO `PROFESSEURS` (`PROFESSEUR_ID`, `PROFESSEUR_NOM`) VALUES
(1, 'Christelle Héritier'),
(2, 'Arnaud Péchon'),
(3, 'Solange Rolland'),
(4, 'Julie Riccioz'),
(5, 'Elodie Romanelli'),
(6, 'Adrien Lange'),
(7, 'Anthony Pasquet');

UPDATE PROFESSEURS SET PROFESSEUR_PWD = MD5('PROFESSEUR_NAME');

-- --------------------------------------------------------

--
-- Table structure for table `PROFESSEUR_CLASSE`
--

CREATE TABLE IF NOT EXISTS `PROFESSEUR_CLASSE` (
  `ID_CLASSE` int(11) NOT NULL,
  `ID_PROFESSEUR` int(11) NOT NULL,
  PRIMARY KEY  (`ID_CLASSE`,`ID_PROFESSEUR`),
  KEY `ID_PROFESSEUR` (`ID_PROFESSEUR`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PROFESSEUR_CLASSE`
--

INSERT INTO `PROFESSEUR_CLASSE` (`ID_CLASSE`, `ID_PROFESSEUR`) VALUES
(1, 1),
(2, 1),
(6, 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `CLASSES`
--
ALTER TABLE `CLASSES`
  ADD CONSTRAINT `CLASSES_ibfk_1` FOREIGN KEY (`ID_ECOLE`) REFERENCES `ECOLES` (`ECOLE_ID`) ON DELETE CASCADE;

--
-- Constraints for table `COMPETENCES`
--
ALTER TABLE `COMPETENCES`
  ADD CONSTRAINT `COMPETENCES_ibfk_1` FOREIGN KEY (`ID_MATIERE`) REFERENCES `MATIERES` (`MATIERE_ID`) ON DELETE CASCADE;

--
-- Constraints for table `DOMAINES`
--
ALTER TABLE `DOMAINES`
  ADD CONSTRAINT `DOMAINES_ibfk_1` FOREIGN KEY (`ID_CYCLE`) REFERENCES `CYCLES` (`CYCLE_ID`) ON DELETE CASCADE;

--
-- Constraints for table `ELEVE_CLASSE`
--
ALTER TABLE `ELEVE_CLASSE`
  ADD CONSTRAINT `ELEVE_CLASSE_ibfk_1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `ELEVE_CLASSE_ibfk_2` FOREIGN KEY (`ID_ELEVE`) REFERENCES `ELEVES` (`ELEVE_ID`) ON DELETE CASCADE;

--
-- Constraints for table `EVALUATIONS_COLLECTIVES`
--
ALTER TABLE `EVALUATIONS_COLLECTIVES`
  ADD CONSTRAINT `EVALUATIONS_COLLECTIVES_ibfk_1` FOREIGN KEY (`ID_PERIODE`) REFERENCES `PERIODES` (`PERIODE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `EVALUATIONS_COLLECTIVES_ibfk_2` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE;

--
-- Constraints for table `EVALUATIONS_INDIVIDUELLES`
--
ALTER TABLE `EVALUATIONS_INDIVIDUELLES`
  ADD CONSTRAINT `EVALUATIONS_INDIVIDUELLES_ibfk_1` FOREIGN KEY (`ID_COMPETENCE`) REFERENCES `COMPETENCES` (`COMPETENCE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `EVALUATIONS_INDIVIDUELLES_ibfk_2` FOREIGN KEY (`ID_EVAL_COL`) REFERENCES `EVALUATIONS_COLLECTIVES` (`EVAL_COL_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `EVALUATIONS_INDIVIDUELLES_ibfk_3` FOREIGN KEY (`ID_NOTE`) REFERENCES `NOTES` (`NOTE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `EVALUATIONS_INDIVIDUELLES_ibfk_4` FOREIGN KEY (`ID_ELEVE`) REFERENCES `ELEVES` (`ELEVE_ID`) ON DELETE CASCADE;

--
-- Constraints for table `MATIERES`
--
ALTER TABLE `MATIERES`
  ADD CONSTRAINT `MATIERES_ibfk_1` FOREIGN KEY (`ID_DOMAINE`) REFERENCES `DOMAINES` (`DOMAINE_ID`) ON DELETE CASCADE;

--
-- Constraints for table `NIVEAUX`
--
ALTER TABLE `NIVEAUX`
  ADD CONSTRAINT `NIVEAUX_ibfk_1` FOREIGN KEY (`ID_CYCLE`) REFERENCES `CYCLES` (`CYCLE_ID`) ON DELETE CASCADE;

--
-- Constraints for table `NIVEAU_CLASSE`
--
ALTER TABLE `NIVEAU_CLASSE`
  ADD CONSTRAINT `NIVEAU_CLASSE_ibfk_1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `NIVEAU_CLASSE_ibfk_2` FOREIGN KEY (`ID_NIVEAU`) REFERENCES `NIVEAUX` (`NIVEAU_ID`) ON DELETE CASCADE;

--
-- Constraints for table `PROFESSEUR_CLASSE`
--
ALTER TABLE `PROFESSEUR_CLASSE`
  ADD CONSTRAINT `PROFESSEUR_CLASSE_ibfk_1` FOREIGN KEY (`ID_CLASSE`) REFERENCES `CLASSES` (`CLASSE_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `PROFESSEUR_CLASSE_ibfk_2` FOREIGN KEY (`ID_PROFESSEUR`) REFERENCES `PROFESSEURS` (`PROFESSEUR_ID`) ON DELETE CASCADE;
