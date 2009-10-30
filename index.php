<?php
/**
 * Page d'indexation.
 * Point d'entree de tout le site (sauf partie administration).
 * @author $Author$
 * @version $Version$
 */

//=============================================================================
// Déclarations des gestionnaires d'erreurs
//=============================================================================

/**
 * Le gestionnaire d'erreur pour la classe base de donnée.
 * Cette fonction remplace la fonction par défaut contenue dans la classe "Database".
 *
 * Le tableau passé en paramétre :
 * - $aError['code']    = Le numéro d'erreur Mysql.
 * - $aError['message'] = Le message d'erreur.
 * - $aError['sqltext'] = La requéte ayant déclenché l'erreur.
 *
 * @author Lionel SAURON
 *
 * @param $aError(array) Tableau décrivant l'erreur.
 */
function globalDatabaseErrorHandler($aError)
{
	// Pour eviter de reboucler s'il y a une erreur lé en dessous.
	static $s_StopErrorHandler = false;

	if($s_StopErrorHandler == true) return;
	$s_StopErrorHandler = true;

	// ===== Le message pour l'écran =====
	Message::addError(trim($aError['message']));
	Message::addError(trim($aError['sqltext']));

	$s_StopErrorHandler = false;
}

/**
 * Le gestionnaire d'erreur pour le script.
 * Cette fonction remplace la fonction par défaut.
 *
 * @author Lionel SAURON
 *
 * @param $nErrorNo(int) Le niveau d'erreur.
 * @param $sErrorMsg(string) Le message d'erreur.
 * @param $sErrorFile(string) Le nom du fichier dans lequel l'erreur a été identifiée.
 * @param $nErrorLine(int) Le numéro de ligne é laquelle l'erreur a été identifiée.
 * @param $aErrorContext(array) Toutes les variables qui existaient lorsque l'erreur a été déclenchée.
 */
function globalScriptErrorHandler($nErrorNo, $sErrorMsg, $sErrorFile, $nErrorLine, $aErrorContext)
{
	// Pour eviter de reboucler s'il y a une erreur lé en dessous.
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

// ===== Fichier de configuration principal =====
require_once("config/main.conf.php");
require_once("config/constantes.conf.php");

// ===== Les autres fichiers de configurations =====
require_once(PATH_CONFIG."/database.conf.php");
require_once(PATH_CONFIG."/export.conf.php");

// ===== Les librairies et les classes =====
require_once(PATH_PHP_LIB."/utils.lib.php");
require_once(PATH_PHP_LIB."/prettyprint.class.php");
require_once(PATH_PHP_LIB."/database.class.php");
require_once(PATH_PHP_LIB."/formvalidation.class.php");
require_once(PATH_PHP_LIB."/message.class.php");

// ===== Les gestionnaires d'erreurs =====
set_error_handler("globalScriptErrorHandler");
Database::setErrorHandler("globalDatabaseErrorHandler");

// ===== Format et timezone =====
setlocale(LC_TIME, 'french', 'fr');

date_default_timezone_set('Europe/Paris');

// ===== Session =====
session_name('MAIN_PAGE');
session_start();

// ===== Connection é la base =====
Database::openConnection(DATABASE_LOGIN, DATABASE_PASSWORD, DATABASE_NAME, DATABASE_SERVER);
// précise à la base qu'on travaille en UTF-8
Database::execute("SET NAMES UTF8");

// ===== Chargement des erreurs sauvegardés =====
Message::loadFromSession($_SESSION['ERROR_MESSAGE']);

//==============================================================================
// Préparation des données
//==============================================================================

$aMenuPage = array
(
	// ----- general -----
	'home'			=> "home.inc.php",
	'login_do'		=> "login_do.inc.php",
	'logout_do'		=> "logout_do.inc.php",
	'contributeurs'	=>	"contributeurs.inc.php",

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
		''				=> "evaluations_individuelles/list.inc.php",
		'add'			=> "evaluations_individuelles/add.inc.php",
		'add_do'		=> "evaluations_individuelles/add_do.inc.php",
		'edit'			=> "evaluations_individuelles/edit.inc.php",
		'edit_do'		=> "evaluations_individuelles/edit_do.inc.php",
		'delete'		=> "evaluations_individuelles/delete.inc.php",
		'delete_do'		=> "evaluations_individuelles/delete_do.inc.php",
	),

	// ----- Profil -----
	'profils' => array
	(
		'edit'              => "profils/edit.inc.php",
		'edit_do'           => "profils/edit_do.inc.php",
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

// ===== Recherche de la page é afficher =====
$sPageName = "error404.inc.php";

if(array_key_exists($sPageId, $aMenuPage) == true)
{
	// Il faut étre identifié pour voir une page autre que 'login_do' ou 'home'
	if(($sPageId != "login_do") && ($sPageId != "home")
	&& (isset($_SESSION['PROFESSEUR_ID']) != true))
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
	if((file_exists(PATH_PAGES."/".$sPageName) == false)
	|| (is_file(PATH_PAGES."/".$sPageName) == false))
	{
		$sPageName = "error404.inc.php";
	}
}

// ===== La liste des utilisateurs (pour la combobox) =====
$sQuery = "SELECT" .
		  "  PROFESSEUR_ID," .
		  "  PROFESSEUR_NOM" .
		  " FROM PROFESSEURS" .
		  " ORDER BY PROFESSEUR_NOM";

$aUsers = Database::fetchColumnWithKey($sQuery);
// $aUsers[Id] = Nom

//==============================================================================
// Traitement des données
//==============================================================================

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title>Gestionnaire d'&eacute;valuations</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta content="Antoine DUMONT" name="author" />

	<link rel="stylesheet" type="text/css" href="default.css" media="all" />
	<link rel="stylesheet" type="text/css" href="main.css" media="all" />
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="main-ie.css" media="all" />
	<! endif -->
	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo URL_JAVASCRIPT; ?>/jscalendar-1.0/calendar-blue3.css" title="calendar-blue2" />
	<!-- main calendar program -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/jscalendar-1.0/calendar.js"></script>
	<!-- language for the calendar -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/jscalendar-1.0/lang/calendar-en.js"></script>
	<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
	<script type="text/javascript" src="<?php echo URL_JAVASCRIPT; ?>/jscalendar-1.0/calendar-setup.js"></script>
</head>
<!-- ================================================== -->
<body>
	<!-- pour ameliorer l'affichage. -->
	<script type="text/javascript">
		/**
		 * Montre un bloc d'id 'id' s'il est cache,
		 * le cache s'il est visible.
		 */
		function showOrHide(id)
		{
			var objElement = document.getElementById(id);

			objElement.style.display = (objElement.style.display != 'none') ? 'none' : '';
		}

		/**
		 * Montre l'objet d'id 'id'.
		 */
		function showId(id)
		{
			var objElement = document.getElementById(id);
			objElement.style.display = '';
		}

		/**
		 * Cache l'objet d'id id.
		 */
		function hideId(id)
		{
			var objElement = document.getElementById(id);
			objElement.style.display = 'none';
		}

		/**
		 * Affiche les ids du tableau aIds.
		 */
		function showIds(aIds)
		{
			for(var i=0; i<aIds.length; i++)
			{
				showId(aIds[i]);
			}
		}

		/**
		 * Cache les ids du tableau aIds.
		 */
		function hideIds(aIds)
		{
			for(var i=0; i<aIds.length; i++)
			{
				hideId(aIds[i]);
			}
		}

		/**
		 * Montre ou cache qqch en fonction du champ du select.
		 */
		function showOrHideSelect(select_id, to_hide_id)
		{
			// si le champ du select n'est pas a 0, on cache le champ to_hide_id
			if(document.getElementById(select_id).value != 0)
			{
				hideId(to_hide_id);
			} else {// sinon on le montre
				showId(to_hide_id);
			}
		}
	</script>
	<div id="struct_left_panel">
		<div id="struct_identity">
			<?php if(!isset($_SESSION['PROFESSEUR_ID'])): /* utilisateur non connecté */ ?>
			<h1>Identification</h1>
			<h4>
			<form method="post" action="?page=login_do">
				<table>
					<tr>
						<td><label for="form_auth_name">Professeur</label></td>
						<td>
							<select id="form_auth_name" name="professeur_id">
							<?php foreach($aUsers as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"<?php echo ( isset($_SESSION['PROFESSEUR_ID']) && ( $nKey == $_SESSION['PROFESSEUR_ID'] ) ) ? ' selected="selected"' : '';?>><?php echo($sValue); ?></option>
							<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="form_auth_password">Mot de passe</label></td>
						<td><input type="password" size="15" maxlength="15" name="professeur_password" /></td>
					</tr>
				</table>
				<div>
					<!-- pour le trou du cul d'ie qui sinon n'integre pas le champ action dans le formulaire -->
					<input type="hidden" name="action" value="Valider" />
					<input type="submit" name="action" value="Valider" />
				</div>
			</form>
			</h4>
			<?php else: /* Utilisateur connecté */ ?>
				<h1><?php echo $_SESSION['PROFESSEUR_NOM']; ?></h1>
					<h4><a href="?page=profils&amp;mode=edit&amp;user_id=<?php echo $_SESSION['PROFESSEUR_ID']; ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/user.png"/>Modification du profil</a></h4>
					<h4><a href="?page=logout_do"><img src="<?php echo(URL_ICONS_16X16); ?>/out.png"/>Se d&eacute;connecter</a></h4>
			<?php endif; ?>
		</div>
		<div id="struct_menu">
			<h1>Menu</h1>
			<?php if(isset($_SESSION['PROFESSEUR_ID'])): /* utilisateur non connecté */?>
			<h4><a href="?page=home"><img src="<?php echo(URL_ICONS_16X16); ?>/home.png"/>Accueil</a></h4>
			<h5>&nbsp;</h5>
			<h3>Elèves</h3>
				<h4><a href="?page=eleves"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png"/>Lister</a></h4>
				<h4><a href="?page=eleves&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png"/>Ajouter</a></h4>
			<h3>Evaluations collectives</h3>
				<h4><a href="?page=evaluations_collectives"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png"/>Lister</a></h4>
				<h4><a href="?page=evaluations_collectives&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png"/>Ajouter</a></h4>
			<h3>Evaluations individuelles</h3>
				<h4><a href="?page=evaluations_individuelles"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png"/>Lister</a></h4>
				<h4><a href="?page=evaluations_individuelles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png"/>Ajouter</a></h4>
			<h3>Aide/Info</h3>
				<h4><a href="?page=contributeurs"><img src="<?php echo(URL_ICONS_16X16); ?>/contributeur.png"/>Contributeurs</a></h4>
				<h4><a href="admin.php"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png"/>Page d'administration</a></h4>
			<?php else: ?>
				Identification requise
			<?php endif; ?>
		</div>
		<div id="struct_licence">
			<table>
				<tr>
					<td colspan="2">
						<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">
							<img src="./images/pub/button-cc.gif" alt="Creative Commons License"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://validator.w3.org/check?uri=referer" rel="nofollow">
							<img src="./images/pub/button-xhtml.png" alt="Valid XHTML 1.0"/>
						</a>
					</td>
					<td>
						<a href="http://jigsaw.w3.org/css-validator/">
							<img src="./images/pub/button-css.png" alt="Valid CSS"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://httpd.apache.org/">
							<img src="./images/pub/button-apache.png" alt="Powered By Apache"/>
						</a>
					</td>
					<td>
						<a href="http://www.php.net/">
							<img src="./images/pub/button-php.png" alt="Powered By PHP"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.mysql.com/">
							<img src="./images/pub/button-mysql.png" alt="Powered By Mysql"/>
						</a>
					</td>
					<td>
						<a href="http://www.mozilla-europe.org/fr/firefox/">
							<img src="./images/pub/button-firefox.png" alt="Developped for Firefox"/>
						</a>
					</td>
				</tr>
			</table>
		</div>
		<?php if(preg_match("/microsoft internet explorer/i", $sAgent) || preg_match("/msie/i", $sAgent)): ?>
			<div style="text-align:left;color:red;">
				Ce site est optimisé pour Mozilla Firefox ou tout navigateur respectant <a href="http://www.w3c.org/">les standards web</a> (chromium, chrome, epiphany, icecat, konqueror, opera, seamonkey, etc...).<br />
				Votre navigateur étant Microsoft Internet Explorer ou l'une de ses moutures, vous risquez de perdre en ergonomie d'utilisation avec cette application.<br />
			</div>
		<?php endif; ?>
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

// ===== Sauvegarde des erreurs sauvegardés =====
$_SESSION['ERROR_MESSAGE'] = Message::saveToSession();

// ===== Connection à la base =====
Database::closeConnection();

// ===== Session =====
session_write_close();

// ===== Bufferisation de sortie =====
ob_end_flush();
?>
