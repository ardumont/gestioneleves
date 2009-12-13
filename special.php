<?php
//=============================================================================
// Declarations des gestionnaires d'erreurs
//=============================================================================

/**
 * Le gestionnaire d'erreur pour la classe base de donnee.
 * Cette fonction remplace la fonction par defaut contenue dans la classe "Database".
 *
 * Le tableau recu en parametre :
 * - $aError['code']    = Le numero d'erreur Mysql.
 * - $aError['message'] = Le message d'erreur.
 * - $aError['sqltext'] = La requete ayant declenche l'erreur.
 *
 * @author Lionel SAURON
 *
 * @param $aError(array) Tableau decrivant l'erreur.
 */
function globalDatabaseErrorHandler($aError)
{
	// Pour eviter de reboucler s'il y a une erreur le en dessous.
	static $s_StopErrorHandler = false;

	if($s_StopErrorHandler == true) return;
	$s_StopErrorHandler = true;

	// ===== Le message pour l'ecran =====

	$s_StopErrorHandler = false;
}

/**
 * Le gestionnaire d'erreur pour le script.
 * Cette fonction remplace la fonction par defaut.
 *
 * @author Lionel SAURON
 *
 * @param $nErrorNo(int) Le niveau d'erreur.
 * @param $sErrorMsg(string) Le message d'erreur.
 * @param $sErrorFile(string) Le nom du fichier dans lequel l'erreur a ete identifiee.
 * @param $nErrorLine(int) Le numero de ligne e laquelle l'erreur a ete identifiee.
 * @param $aErrorContext(array) Toutes les variables qui existaient lorsque l'erreur a ete declenchee.
 */
function globalScriptErrorHandler($nErrorNo, $sErrorMsg, $sErrorFile, $nErrorLine, $aErrorContext)
{
	// Pour eviter de reboucler s'il y a une erreur le en dessous.
	static $s_StopErrorHandler = false;

	if($s_StopErrorHandler == true) return;
	$s_StopErrorHandler = true;

	// ===== On filtre les messages d'erreur =====
	// On supprime les erreurs de php 4.
	if(strpos($sErrorMsg, "Non-static method Database::") >= 0)
	{
		$s_StopErrorHandler = false;
		return;
	}

	// ===== Le message pour l'ecran =====

	$s_StopErrorHandler = false;
}

//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Bufferisation de sortie =====
ob_start('ob_gzhandler');

// ===== Fichier de configuration principal =====
require_once("config/main.conf.php");
// ===== Fichier de configuration d'export =====
require_once("config/export.conf.php");

// Les autres fichiers de configurations
require_once("config/database.conf.php");
require_once("config/constantes.conf.php");

// ===== Les librairies et les classes =====

require_once(PATH_PHP_LIB."/utils.lib.php");
require_once(PATH_PHP_LIB."/database.class.php");
require_once(PATH_PHP_LIB."/formvalidation.class.php");
require_once(PATH_PHP_LIB."/message.class.php");

require_once(PATH_METIER . "/livret.class.php");
require_once(PATH_METIER . "/moyenne.class.php");

// ===== Les gestionnaires d'erreurs =====
set_error_handler("globalScriptErrorHandler");
Database::setErrorHandler("globalDatabaseErrorHandler");

// ===== Format et timezone =====
setlocale(LC_TIME, 'french', 'fr');

date_default_timezone_set('Europe/Paris');

// ===== Session =====
session_name('MAIN_PAGE');
session_start();

// ===== Connection e la base =====
Database::openConnection(DATABASE_LOGIN, DATABASE_PASSWORD, DATABASE_NAME, DATABASE_SERVER);
// précise à la base qu'on travaille en UTF-8
Database::execute("SET NAMES UTF8");

// ===== Chargement des erreurs sauvegardes =====
Message::loadFromSession($_SESSION['ERROR_MESSAGE']);

//==============================================================================
// Preparation des donnees
//==============================================================================

$aMenuPage = array
(
	// ----- General -----
	'export_livret_eleve_annuel'     => "special/export_livret_eleve_annuel.inc.php",
	'export_livret_eleve_period'     => "special/export_livret_eleve_period.inc.php",
	'export_livret_eleve_cycle'      => "special/export_livret_eleve_cycle.inc.php",
	'export_livret_eleve_annuel_all' => "special/export_livret_eleve_annuel_all.inc.php",
	'export_livret_eleve_period_all' => "special/export_livret_eleve_period_all.inc.php",
	'export_livret_eleve_cycle_all'  => "special/export_livret_eleve_cycle_all.inc.php",
	'consultation_period_competence' => "special/consultation_period_competence.inc.php",
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

// ===== Recherche de la page à afficher =====
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

//==============================================================================
// Traitement des donnees
//==============================================================================

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

include(PATH_PAGES."/".$sPageName);

//==============================================================================
// Cloture de la page
//==============================================================================

// ===== Sauvegarde des erreurs sauvegardes =====
$_SESSION['ERROR_MESSAGE'] = Message::saveToSession();

// ===== Connection e la base =====
Database::closeConnection();

// ===== Session =====
session_write_close();

// ===== Bufferisation de sortie =====
ob_end_flush();
