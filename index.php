<?php
//=============================================================================
// Déclarations des gestionnaires d'erreurs
//=============================================================================

/**
 * Le gestionnaire d'erreurs pour la classe base de données.
 * Cette fonction remplace la fonction par défaut contenue dans la classe "Database".
 *
 * Le tableau reçu en paramètre :
 * - $aError['code']    = Le numéro d'erreur Mysql.
 * - $aError['message'] = Le message d'erreur.
 * - $aError['sqltext'] = La requête ayant déclenché l'erreur.
 *
 * @author Lionel SAURON
 *
 * @param $aError(array) Tableau décrivant l'erreur.
 */
function globalDatabaseErrorHandler($aError)
{
	// Pour eviter de reboucler s'il y a une erreur là en dessous.
	static $s_StopErrorHandler = false;

	if($s_StopErrorHandler == true) return;
	$s_StopErrorHandler = true;

	// ===== Le message pour l'écran =====
	Message::addError(trim($aError['message']));
	Message::addError(trim($aError['sqltext']));

	$s_StopErrorHandler = false;
}

/**
 * Le gestionnaire d'erreurs pour PHP remplaçant la fonction par défaut.
 * Traite les erreurs de configuration pouvant résulter d'une mauvaise installation de l'application.
 *
 * @author Lionel SAURON
 *
 * @param $nErrorNo(int) Le niveau d'erreur.
 * @param $sErrorMsg(string) Le message d'erreur.
 * @param $sErrorFile(string) Le nom du fichier dans lequel l'erreur a été identifiée.
 * @param $nErrorLine(int) Le numéro de ligne à laquelle l'erreur a été identifiée.
 * @param $aErrorContext(array) Toutes les variables qui existaient lorsque l'erreur a été déclenchée.
 */
function installScriptErrorHandler($nErrorNo, $sErrorMsg, $sErrorFile, $nErrorLine, $aErrorContext)
{
	// ===== On filtre les messages d'erreur =====
	// On supprime les erreurs de php 4.
	if(($nErrorNo == E_STRICT) && (strpos($sErrorMsg, "var: Deprecated") !== false)) return;

	include("pages/error-install.inc.php");
	die();
}

/**
 * Le gestionnaire d'erreurs pour PHP remplaçant la fonction par défaut.
 * Traite les erreurs lors de l'exécution normal du site.
 *
 * @author Lionel SAURON
 *
 * @param $nErrorNo(int) Le niveau d'erreur.
 * @param $sErrorMsg(string) Le message d'erreur.
 * @param $sErrorFile(string) Le nom du fichier dans lequel l'erreur a été identifiée.
 * @param $nErrorLine(int) Le numéro de ligne à laquelle l'erreur a été identifiée.
 * @param $aErrorContext(array) Toutes les variables qui existaient lorsque l'erreur a été déclenchée.
 */
function globalScriptErrorHandler($nErrorNo, $sErrorMsg, $sErrorFile, $nErrorLine, $aErrorContext)
{
	// Pour eviter de reboucler s'il y a une erreur là en dessous.
	static $s_StopErrorHandler = false;

	if($s_StopErrorHandler == true) return;
	$s_StopErrorHandler = true;

	// ===== Le message pour l'écran =====
	Message::addError($sErrorMsg);
	Message::addError("Ligne {$nErrorLine} dans le fichier {$sErrorFile}");

	$s_StopErrorHandler = false;
}

//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Bufferisation de sortie =====

ob_start('ob_gzhandler');

// Le gestionnaire d'erreurs d'install pour PHP
set_error_handler("installScriptErrorHandler");

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

// Le gestionnaire d'erreurs global pour PHP
set_error_handler("globalScriptErrorHandler");

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

// ===== La base de données =====

// Le gestionnaire d'erreurs de la base
Database::setErrorHandler("globalDatabaseErrorHandler");

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

	// ----- gestion des eleves -----
	'eleves' => array
	(
		''				=> "eleves/list.inc.php",
		'add'			=> "eleves/add.inc.php",
		'add_do'		=> "eleves/add_do.inc.php",
		'edit'			=> "eleves/edit.inc.php",
		'edit_do'		=> "eleves/edit_do.inc.php",
		'delete'		=> "eleves/delete.inc.php",
		'delete_do'		=> "eleves/delete_do.inc.php",
		'desactive'		=> "eleves/desactive.inc.php",
		'desactive_do'	=> "eleves/desactive_do.inc.php",
		'active'		=> "eleves/active.inc.php",
		'active_do'		=> "eleves/active_do.inc.php",
	),

	// ----- gestion des evaluations collectives -----
	'evaluations_collectives' => array
	(
		''				=> "evaluations_collectives/list.inc.php",
		'add'			=> "evaluations_collectives/add.inc.php",
		'add_do'		=> "evaluations_collectives/add_do.inc.php",
		'edit'			=> "evaluations_collectives/edit.inc.php",
		'edit_do'		=> "evaluations_collectives/edit_do.inc.php",
		'delete'		=> "evaluations_collectives/delete.inc.php",
		'delete_do'		=> "evaluations_collectives/delete_do.inc.php",
	),

	// ----- gestion des evaluations individuelles -----
	'evaluations_individuelles' => array
	(
		''                   => "evaluations_individuelles/list.inc.php",
		'add'                => "evaluations_individuelles/add.inc.php",
		'add_do'             => "evaluations_individuelles/add_do.inc.php",
		'edit'               => "evaluations_individuelles/edit.inc.php",
		'edit_do'            => "evaluations_individuelles/edit_do.inc.php",
		'delete'             => "evaluations_individuelles/delete.inc.php",
		'delete_do'          => "evaluations_individuelles/delete_do.inc.php",
		'actions_multiples'  => "evaluations_individuelles/actions_multiples.inc.php",
		'delete_multiple_do' => "evaluations_individuelles/delete_multiple_do.inc.php",
		'edit_multiple_do'   => "evaluations_individuelles/edit_multiple_do.inc.php",
	),

	// ----- Profil -----
	'profils' => array
	(
		'edit'              => "profils/edit.inc.php",
		'edit_do'           => "profils/edit_do.inc.php",
	),

	// ----- Livrets -----
	'livrets' => array(
		'recap_annuel'     => "livrets/recap_annuel.inc.php",
		'recap_period'     => "livrets/recap_period.inc.php",
		'recap_cycle'      => "livrets/recap_cycle.inc.php",
		'recap_annuel_all' => "livrets/recap_annuel_all.inc.php",
		'recap_period_all' => "livrets/recap_period_all.inc.php",
	),

	// ----- Consultations -----
	'consultations' => array(
		'competences_period' => "consultations/competences_period.inc.php",
		'competences_annuel' => "consultations/competences_annuel.inc.php",
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

//==============================================================================
// Traitement des données
//==============================================================================

//==============================================================================
// Préparation de l'affichage
//==============================================================================

// Récupère le navigateur pour discriminer l'utilisation d'ie
$sAgent = $_SERVER['HTTP_USER_AGENT'];

// ===== Lancer l'install ? =====

$bNeedInstall = false;

// Si la version présente en BDD est égale ou inférieure à la v3.2.0
// alors la BDD ne contient pas la colonne DATE_VERSION donc on test.

$aTemp = Database::fetchOneRow("SHOW COLUMNS FROM PARAMETRES LIKE 'DATE_VERSION'");
if($aTemp !== false)
{
	$nDateVersion = Database::fetchOneValue("SELECT UNIX_TIMESTAMP(DATE_VERSION) FROM PARAMETRES");
	$nDateFileHome = filemtime(PATH_PAGES."/release_notes.inc.php");
	if($nDateVersion != $nDateFileHome)
	{
		$bNeedInstall = true;
	}
}
else
{
	$bNeedInstall = true;
}

// ===== Mise en forme pour la popup =====

$sGuiBodyCssClass = ($bNeedInstall == true) ? "popup_stop_scroll" : "";

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title>Gestionnaire d'élèves</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta content="Antoine Romain DUMONT" name="author" />

	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo URL_JAVASCRIPT; ?>/jscalendar-1.0/calendar-blue3.css" title="calendar-blue2" />
	<!-- main calendar program -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/jscalendar-1.0/calendar.js"></script>
	<!-- language for the calendar -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/jscalendar-1.0/lang/calendar-en.js"></script>
	<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/jscalendar-1.0/calendar-setup.js"></script>
	<!-- fonctions utilitaires de javascript -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/utils.inc.js"></script>
	<!-- JQuery -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/jquery.js"></script>
	<!-- Utilitaires basés sur JQuery -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/utils_jquery.inc.js"></script>

	<link rel="stylesheet" type="text/css" href="default.css" media="all" />
	<link rel="stylesheet" type="text/css" href="main.css" media="all" />
	<link rel="stylesheet" type="text/css" href="main2.css" media="all" />
	<!--[if gt IE 6]>
	<link rel="stylesheet" type="text/css" href="main-ie.css" media="all" />
	<![endif]-->
	<!--[if lte IE 6]>
	<link rel="stylesheet" type="text/css" href="main-ie6.css" media="all" />
	<![endif]-->
</head>
<!-- ================================================== -->
<body class="<?php echo($sGuiBodyCssClass); ?>">
	<?php if($bNeedInstall == true): ?>
	<div id="struct_popup_mask"></div>
	<div id="struct_popup">
		<h1>Problème de version</h1>
		<p>Il semblerait que vous soyez en train d'installer une nouvelle version du Gestionnaire d'élèves<br />
		car les versions de la base de données et de l'application ne correspondent pas.</p>
		<p>Afin de pouvoir utiliser cette application, veuillez suivre les indications de l'installeur.<br />
		<a href="install/">Mise à jour de l'application</a></p>
	</div>
	<?php endif; ?>
	<div id="struct_left_panel">
		<div id="struct_identity">
			<?php require_once(PATH_PAGES."/menu/user_identity.inc.php"); ?>
		</div>
		<div id="struct_menu">
			<?php if(isset($_SESSION['PROFESSEUR_ID']) == true): /* Utilisateur connecté */ ?>
				<?php require_once(PATH_PAGES."/menu/user_menu.inc.php"); ?>
			<?php else:  /* Utilisateur non connecté */ ?>
				<h1>Menu</h1>
				<p>Identification requise</p>
			<?php endif; ?>
		</div>
		<div id="struct_licence">
			<?php require_once(PATH_PAGES."/menu/licence.inc.php"); ?>
		</div>
		<?php if(preg_match("/microsoft internet explorer/i", $sAgent) || preg_match("/msie/i", $sAgent)): ?>
			<div style="text-align:left;color:red;">
				Ce site est optimisé pour Mozilla Firefox ou tout navigateur respectant <a href="http://www.w3c.org/">les standards web</a> (chromium, chrome, epiphany, icecat, konqueror, opera, seamonkey, etc...).<br />
				Votre navigateur étant Microsoft Internet Explorer ou l'une de ses moutures, vous risquez de perdre en ergonomie d'utilisation avec cette application.<br />
			</div>
		<?php endif; ?>
	</div>
	<div id="struct_main" class="<?php echo($sPageId); ?>">
		<?php include(PATH_PAGES . "/" . $sPageName); ?>
	</div>
</body>
</html>
<?php
//==============================================================================
// Cloture de la page
//==============================================================================

// ===== Sauvegarde des erreurs sauvegardés =====
$_SESSION['ERROR_MESSAGE'] = Message::saveToSession();

// ===== Connection à la base =====
Database::closeConnection();

// ===== Session =====
session_write_close();

// ===== Bufferisation de sortie =====
ob_end_flush();
