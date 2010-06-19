<?php
//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Les fichiers de configuration =====

// Le fichier principal
require_once(PATH_INSTALL_ROOT."/config/main.conf.php");

// Les autres fichiers de configurations
require_once(PATH_CONFIG."/database.conf.php");

// ===== La base de données =====

// Le gestionnaire d'erreurs de la base (cf index.php)
Database::setErrorHandler("nullDatabaseErrorHandler");

// Connexion à la base
Database::openConnection(DATABASE_LOGIN, DATABASE_PASSWORD, DATABASE_NAME, DATABASE_SERVER);

// On précise à la base qu'on travaille en UTF-8
Database::execute("SET NAMES UTF8");

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

?>
