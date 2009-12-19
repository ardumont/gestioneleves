<?php
//=============================================================================
// Gestionnaires d'erreurs
//=============================================================================

/**
 * Le gestionnaire d'erreur pour la classe base de donnees.
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
	Message::addError(trim($aError['message']));
	Message::addError(trim($aError['sqltext']));

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
	// Pour eviter de reboucler s'il y a une erreur la en dessous.
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
	Message::addError($sErrorMsg);
	Message::addError("Ligne {$nErrorLine} dans le fichier {$sErrorFile}");

	$s_StopErrorHandler = false;
}

//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Bufferisation de sortie =====
ob_start('ob_gzhandler');

// ===== Fichier de configuration principal =====
require_once("config/main.conf.php");
require_once("config/constantes.conf.php");

// ===== Les autres fichiers de configurations =====
require_once(PATH_CONFIG . "/database.conf.php");
require_once(PATH_CONFIG . "/export.conf.php");

// ===== Les librairies et les classes =====
require_once(PATH_PHP_LIB . "/utils.lib.php");
require_once(PATH_PHP_LIB . "/prettyprint.class.php");
require_once(PATH_PHP_LIB . "/imports.lib.php");
require_once(PATH_PHP_LIB . "/database.class.php");
require_once(PATH_PHP_LIB . "/formvalidation.class.php");
require_once(PATH_PHP_LIB . "/message.class.php");
require_once(PATH_APP_LIB . "/profilmanager.class.php");

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

// ===== Chargement des erreurs sauvegardes =====
Message::loadFromSession($_SESSION['ERROR_MESSAGE']);

// On précise à la base qu'on travaille en UTF-8
Database::execute("SET NAMES UTF8");

// ===== Le gestionnaire de profil =====

ProfilManager::loadRights();

//==============================================================================
// Preparation des donnees
//==============================================================================

$aMenuPage = array(
	// ----- General -----
	'home'      => "home.inc.php",
	'no_rights' => "error-rights.inc.php",

	// ----- Applications -----
	'eleves' => array
	(
		''			=>	"admin/eleves/list.inc.php",
	),

	// ----- Les professeurs -----
	'professeurs' => array
	(
		''			=>	"admin/professeurs/list.inc.php",
		'add'		=>	"admin/professeurs/add.inc.php",
		'add_do'	=>	"admin/professeurs/add_do.inc.php",
		'delete'	=>	"admin/professeurs/delete.inc.php",
		'delete_do'	=>	"admin/professeurs/delete_do.inc.php",
		'edit'		=>	"admin/professeurs/edit.inc.php",
		'edit_do'	=>	"admin/professeurs/edit_do.inc.php",
	),

	// ----- Les classes -----
	'classes' => array
	(
		''			=>	"admin/classes/list.inc.php",
		'add'		=>	"admin/classes/add.inc.php",
		'add_do'	=>	"admin/classes/add_do.inc.php",
		'delete'	=>	"admin/classes/delete.inc.php",
		'delete_do'	=>	"admin/classes/delete_do.inc.php",
		'edit'		=>	"admin/classes/edit.inc.php",
		'edit_do'	=>	"admin/classes/edit_do.inc.php",
	),

	// ----- Les écoles -----
	'ecoles' => array
	(
		''			=>	"admin/ecoles/list.inc.php",
		'add'		=>	"admin/ecoles/add.inc.php",
		'add_do'	=>	"admin/ecoles/add_do.inc.php",
		'delete'	=>	"admin/ecoles/delete.inc.php",
		'delete_do'	=>	"admin/ecoles/delete_do.inc.php",
		'edit'		=>	"admin/ecoles/edit.inc.php",
		'edit_do'	=>	"admin/ecoles/edit_do.inc.php",
	),

	// ----- Les domaines -----
	'domaines' => array
	(
		''			=>	"admin/domaines/list.inc.php",
		'add'		=>	"admin/domaines/add.inc.php",
		'add_do'	=>	"admin/domaines/add_do.inc.php",
		'delete'	=>	"admin/domaines/delete.inc.php",
		'delete_do'	=>	"admin/domaines/delete_do.inc.php",
		'edit'		=>	"admin/domaines/edit.inc.php",
		'edit_do'	=>	"admin/domaines/edit_do.inc.php",
	),

	// ----- Les matieres -----
	'matieres' => array
	(
		''			=>	"admin/matieres/list.inc.php",
		'add'		=>	"admin/matieres/add.inc.php",
		'add_do'	=>	"admin/matieres/add_do.inc.php",
		'edit'		=>	"admin/matieres/edit.inc.php",
		'edit_do'	=>	"admin/matieres/edit_do.inc.php",
		'delete'	=>	"admin/matieres/delete.inc.php",
		'delete_do'	=>	"admin/matieres/delete_do.inc.php",
	),

	// ----- Les competences -----
	'competences' => array
	(
		''			=>	"admin/competences/list.inc.php",
		'add'		=>	"admin/competences/add.inc.php",
		'add_do'	=>	"admin/competences/add_do.inc.php",
		'edit'		=>	"admin/competences/edit.inc.php",
		'edit_do'	=>	"admin/competences/edit_do.inc.php",
		'delete'	=>	"admin/competences/delete.inc.php",
		'delete_do'	=>	"admin/competences/delete_do.inc.php",
	),

	// ----- Les cycles -----
	'cycles' => array
	(
		''			=>	"admin/cycles/list.inc.php",
		'add'		=>	"admin/cycles/add.inc.php",
		'add_do'	=>	"admin/cycles/add_do.inc.php",
		'edit'		=>	"admin/cycles/edit.inc.php",
		'edit_do'	=>	"admin/cycles/edit_do.inc.php",
		'delete'	=>	"admin/cycles/delete.inc.php",
		'delete_do'	=>	"admin/cycles/delete_do.inc.php",
	),

	// ----- Les niveaux -----
	'niveaux' => array
	(
		''			=>	"admin/niveaux/list.inc.php",
		'add'		=>	"admin/niveaux/add.inc.php",
		'add_do'	=>	"admin/niveaux/add_do.inc.php",
		'edit'		=>	"admin/niveaux/edit.inc.php",
		'edit_do'	=>	"admin/niveaux/edit_do.inc.php",
		'delete'	=>	"admin/niveaux/delete.inc.php",
		'delete_do'	=>	"admin/niveaux/delete_do.inc.php",
	),

	// ----- Les périodes -----
	'periodes' => array
	(
		''			=>	"admin/periodes/list.inc.php",
		'add'		=>	"admin/periodes/add.inc.php",
		'add_do'	=>	"admin/periodes/add_do.inc.php",
		'edit'		=>	"admin/periodes/edit.inc.php",
		'edit_do'	=>	"admin/periodes/edit_do.inc.php",
		'delete'	=>	"admin/periodes/delete.inc.php",
		'delete_do'	=>	"admin/periodes/delete_do.inc.php",
	),

	// ----- Les notes -----
	'notes' => array
	(
		''			=>	"admin/notes/list.inc.php",
// interdiction d'ajouter, supprimer ou éditer une note car des modules dépendent des valeurs fixes des notes
//		'add'		=>	"admin/notes/add.inc.php",
//		'add_do'	=>	"admin/notes/add_do.inc.php",
//		'edit'		=>	"admin/notes/edit.inc.php",
//		'edit_do'	=>	"admin/notes/edit_do.inc.php",
//		'delete'	=>	"admin/notes/delete.inc.php",
//		'delete_do'	=>	"admin/notes/delete_do.inc.php",
	),

	// ----- Les modules d'imports -----
	'imports' => array
	(
		'imports_csv'           => "admin/imports/imports_csv.inc.php",
		'imports_csv_do'        => "admin/imports/imports_csv_do.inc.php",
		'imports_xml'           => "admin/imports/imports_xml.inc.php",
		'imports_xml_do'        => "admin/imports/imports_xml_do.inc.php",
		'imports_xml_classe'    => "admin/imports/imports_xml_classe.inc.php",
		'imports_xml_classe_do' => "admin/imports/imports_xml_classe_do.inc.php",
	),

	// ----- Les profils et droits -----
	'profils' => array
	(
		''          => "admin/profils/list.inc.php",
		'add'       => "admin/profils/add.inc.php",
		'add_do'    => "admin/profils/add_do.inc.php",
		'edit'      => "admin/profils/edit.inc.php",
		'edit_do'   => "admin/profils/edit_do.inc.php",
		'delete'    => "admin/profils/delete.inc.php",
		'delete_do' => "admin/profils/delete_do.inc.php",
	),

);

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Navigation entre les pages =====
$objFormNavigation = new FormValidation();

$sPageId = $objFormNavigation->getValue('page', $_GET, 'is_string', "home");
$sMode   = $objFormNavigation->getValue('mode', $_GET, 'is_string', "");

//==============================================================================
// Actions du formulaire
//==============================================================================

// ===== Recherche de la page e afficher =====
$sPageName = "error404.inc.php";

if(array_key_exists($sPageId, $aMenuPage) == true)
{
	$aSubMenuPage = $aMenuPage[$sPageId];

	// ===== Y a t'il des modes pour cette page ? =====
	if(is_array($aSubMenuPage) == true)
	{
		if(array_key_exists($sMode, $aSubMenuPage) == true)
		{
			$sPageName = $aSubMenuPage[$sMode];
		}
	} else {
		$sPageName = $aSubMenuPage;
	}

	// ===== Le fichier existe ? =====
	if((file_exists(PATH_PAGES."/".$sPageName) == false)
	|| (is_file(PATH_PAGES."/".$sPageName) == false))
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title>Gestionnaire d'&eacute;l&eacute;ves (Administration)</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta content="Antoine Dumont" name="author" />

	<link rel="stylesheet" type="text/css" href="default.css" media="all" />
	<link rel="stylesheet" type="text/css" href="main.css" media="all" />
	<link rel="stylesheet" type="text/css" href="admin.css" media="all" />
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="main-ie.css" media="all" />
	<! endif -->
	<!-- JQuery -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/jquery-1.3.2.js"></script>
	<!-- fonctions utilitaires de javascript -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/utils.inc.js"></script>
	<!-- Utilitaires basés sur JQuery -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/utils_jquery.inc.js"></script>
</head>
<!-- ================================================== -->
<body>
	<div id="struct_left_panel">
		<div id="struct_identity">
			<h1><a href="javascript:void(0);" style="color:white;" onclick="$('#identite').toggle('slow');">Retour</a></h1>
			<div id="identite">
				<h4>
					<a href="index.php?page=home">
						<img src="<?php echo(URL_ICONS_16X16); ?>/home.png" />Accueil principal
					</a>
				</h4>
			</div>
		</div>
		<div id="struct_menu">
			<h1><a href="javascript:void(0);" style="color:white;" onclick="$('#administration').toggle('slow');">Administration</a></h1>
			<div id="administration">
				<h4>
					<a href="?page=home">
						<img src="<?php echo(URL_ICONS_16X16); ?>/home.png" />La page d'accueil
					</a>
				</h4>
				<h2><a href="javascript:void(0);" style="color:white;" onclick="$('#administrer').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Administrer</a></h2>
				<div id="administrer">
					<h4>
						<a href="?page=profils">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Profils
						</a>
					</h4>
				</div>
				<h2><a href="javascript:void(0);" style="color:white;" onclick="$('#gestion').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Gestion</a></h2>
				<div id="gestion">
					<h4>
						<a href="?page=professeurs">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Professeurs
						</a>
					</h4>
					<h4>
						<a href="?page=ecoles">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Ecoles
						</a>
					</h4>
					<h4>
						<a href="?page=classes">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Classes
						</a>
					</h4>
					<h4>
						<a href="?page=eleves">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Elèves
						</a>
					</h4>
					<h4>
						<a href="?page=cycles">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Cycles
						</a>
					</h4>
					<h4>
						<a href="?page=niveaux">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Niveaux
						</a>
					</h4>
					<h4>
						<a href="?page=domaines">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Domaines
						</a>
					</h4>
					<h4>
						<a href="?page=matieres">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Mati&egrave;res
						</a>
					</h4>
					<h4>
						<a href="?page=competences">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Comp&eacute;tences
						</a>
					</h4>
					<h4>
						<a href="?page=notes">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Notes
						</a>
					</h4>
					<h4>
						<a href="?page=periodes">
							<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />P&eacute;riodes
						</a>
					</h4>
				</div>
				<h2><a href="javascript:void(0);" style="color:white;" onclick="$('#imports').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Import</a></h2>
				<div id="imports">
					<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#imports_cycle').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Cycles</a></h3>
					<div id="imports_cycle">
						<h4>
							<a href="?page=imports&amp;mode=imports_csv">
								<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Import CSV
							</a>
						</h4>
						<h4>
							<a href="?page=imports&amp;mode=imports_xml">
								<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Import XML
							</a>
						</h4>
					</div>
					<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#imports_classe').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Classes</a></h3>
					<div id="imports_classe">
						<h4>
							<a href="?page=imports&amp;mode=imports_xml_classe">
								<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Import XML
							</a>
						</h4>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="struct_main" class="<?php echo($sPageId); ?>">
		<?php include(PATH_PAGES."/".$sPageName); ?>
	</div>
</body>
</html>
<?php
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
