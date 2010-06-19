<?php
//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Fichier de configuration principal =====
require_once(PATH_CONF_INSTALL."/main.conf.php");

// ===== Les autres fichiers de configurations =====
require_once(PATH_CONFIG."/database.conf.php");

// ===== Les librairies et les classes =====
require_once(PATH_PHP_LIB."/utils.lib.php");
require_once(PATH_PHP_LIB."/database.class.php");
require_once(PATH_PHP_LIB."/formvalidation.class.php");
require_once(PATH_PHP_LIB."/message.class.php");

require_once(PATH_PHP_LIB."/install.class.php");

// ===== Session =====
session_name('INSTALL_PAGE');
session_start();

// ===== Connexion à la base =====
Database::setErrorHandler("nullDatabaseErrorHandler"); // Le gestionnaires d'erreurs (cf index.php)

Database::openConnection(DATABASE_LOGIN, DATABASE_PASSWORD, DATABASE_NAME, DATABASE_SERVER);

Database::execute("SET NAMES UTF8"); // On précise à la base qu'on travaille en UTF-8

//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Vérification des valeurs =====

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

// ===== Copie du fichier pour les exports =====

copy(PATH_CONFIG."/export.sample.conf.php", PATH_CONFIG."/export.conf.php");

// ===== Traitement final du changement de version =====

$nDateVersion = filemtime(PATH_PAGES."/release_notes.inc.php");

$sQuery = <<< _EOQ_
    UPDATE PARAMETRES
    SET DATE_VERSION = FROM_UNIXTIME({$nDateVersion})
_EOQ_;

Database::execute($sQuery);

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

// Rechargement
header("Location: ?step=5&mode=end");
return;
