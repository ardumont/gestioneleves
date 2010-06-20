<?php

// ===== Les fichiers de configuration =====

// Le fichier principal
require_once("config/main.conf.php");
require_once("config/constantes.conf.php");

// Les autres fichiers de configurations
require_once(PATH_CONFIG . "/database.conf.php");
require_once(PATH_CONFIG . "/export.conf.php");

// ===== Les librairies et les classes =====

require_once(PATH_PHP_LIB . "/utils.lib.php");
require_once(PATH_PHP_LIB . "/database.class.php");
require_once(PATH_PHP_LIB . "/formvalidation.class.php");
require_once(PATH_PHP_LIB . "/message.class.php");
require_once(PATH_APP_LIB . "/profilmanager.class.php");

require_once(PATH_METIER . "/livret.class.php");
require_once(PATH_METIER . "/moyenne.class.php");

// ===== Format et timezone =====

// bcp de locales différentes car selon l'os, c'est géré différemment
setlocale(LC_TIME, 'french', 'fr', 'fr_FR', 'fr_FR.UTF8', 'fra', 'fra_fra');
// timezone
date_default_timezone_set('Europe/Paris');

// ===== La session =====

session_name('MAIN_PAGE');
session_start();

// Chargement des erreurs sauvegardés
Message::loadFromSession($_SESSION['ERROR_MESSAGE']);

// Connexion à la base
Database::openConnection(DATABASE_LOGIN, DATABASE_PASSWORD, DATABASE_NAME, DATABASE_SERVER);

// On précise à la base qu'on travaille en UTF-8
Database::execute("SET NAMES UTF8");

// ===== Le gestionnaire de profils =====

ProfilManager::loadRights();

//==============================================================================
// Préparation des données
//==============================================================================

$aMenuPage = array
(
	// ----- general -----
	'home'			=> "home.inc.php",
	'login_do'		=> "login_do.inc.php",
	'logout_do'		=> "logout_do.inc.php",
	'contributeurs'	=> "contributeurs.inc.php",
	'no_rights'		=> "error-rights.inc.php",

	// ----- Commentaires -----
	'commentaires' => array(
		'add_or_update' => "commentaires/add_or_update.inc.php",
	),
	// ----- Commentaires -----
	'conseil_maitres' => array(
		'add_or_update' => "conseil_maitres/add_or_update.inc.php",
	),
	);

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Navigation entre les pages =====
$oForm = new FormValidation();

$sPageId = $oForm->getValue('page', $_GET, 'is_string', "home");
$sMode   = $oForm->getValue('mode', $_GET, 'is_string', "");

//==============================================================================
// Actions du formulaire
//==============================================================================

// ===== Recherche de la page é afficher =====
$sPageName = "error404.inc.php";

if(array_key_exists($sPageId, $aMenuPage) == true)
{
	// Il faut étre identifié pour voir une page autre que 'login_do' ou 'home'
	if(($sPageId != "login_do") && ($sPageId != "home") && (isset($_SESSION['PROFESSEUR_ID']) != true))
	{
		$sPageId = "home";
	}

	$aSubMenuPage = $aMenuPage[$sPageId];

	// ===== Y a t'il des modes pour cette page ? =====
	if(is_array($aSubMenuPage) == true)
	{
		if(array_key_exists($sMode, $aSubMenuPage) == true)
		{
			$sPageName = $aSubMenuPage[$sMode];
		}
	}
	else
	{
		$sPageName = $aSubMenuPage;
	}

	// ===== Le fichier existe ? =====
	if((file_exists(PATH_PAGES."/".$sPageName) == false) || (is_file(PATH_PAGES."/".$sPageName) == false))
	{
		$sPageName = "error404.inc.php";
	}
}

include(PATH_PAGES . "/" . $sPageName);

// ===== Connection à la base =====
Database::closeConnection();
