<?php
//=============================================================================
// Déclarations des gestionnaires d'erreurs
//=============================================================================

/**
 * Le gestionnaire d'erreurs pour la classe base de donnée.
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
function nullDatabaseErrorHandler($aError)
{
	// C'est normal si on ne traite pas l'erreur ici.
	// Ce handler est là pour les cas particuliers liés à l'installation.
}

//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Bufferisation de sortie =====

ob_start('ob_gzhandler');

// ===== Les fichiers de configuration =====

// On ne peut pas inclure les lib ici,
//  car on doit partir du principe que l'on ne peut rien faire sur ce serveur.

// ===== Constantes pour l'installeur =====

define('PATH_CONF_INSTALL', "../config");

// ===== Format et timezone =====

// bcp de locales différentes car selon l'os, c'est géré différemment
setlocale(LC_TIME, 'french', 'fr', 'fr_FR', 'fr_FR.UTF8', 'fra', 'fra_fra');
// timezone
date_default_timezone_set('Europe/Paris');

//==============================================================================
// Préparation des données
//==============================================================================

// ===== Le tableau contenant les informations pour la navigation =====
$aNavigationPageData = array
(
	// ----- Basic -----
	'step0' => "general_check.inc.php",
	'step1' => "general_check.inc.php",

	'step2' => array
	(
		''    => "main_conf_check.inc.php",
		'do'  => "main_conf_do.inc.php",
		'end' => "main_conf_end.inc.php",
	),

	'step3'  => array
	(
		''    => "database_conf_ask.inc.php",
		'do'  => "database_conf_do.inc.php",
		'end' => "database_conf_end.inc.php",
	),

	'step4'  => array
	(
		''    => "database_ask.inc.php",
		'do'  => "database_do.inc.php",
		'end' => "database_end.inc.php",
	),

	'step5'  => array
	(
		''    => "constants_conf_do.inc.php",
		'end' => "constants_conf_end.inc.php",
	),

);

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Navigation entre les pages =====
$nStep = 0;

if(array_key_exists('step', $_GET) == true)
{
	if(is_numeric($_GET['step']))
	{
		$nStep = (int)$_GET['step'];
	}
}

if($nStep < 0)
{
	$nStep = 0;
}

$sPageId = "step{$nStep}";

$sMode = "";

if(array_key_exists('mode', $_GET) == true)
{
	$sMode = "{$_GET['mode']}";
}

//==============================================================================
// Actions du formulaire
//==============================================================================

// ===== Recherche de la page à afficher =====
$sPageName = null;

if(array_key_exists($sPageId, $aNavigationPageData) == true)
{

	$aNavigationModeData = $aNavigationPageData[$sPageId];

	// ===== Y a t'il des modes pour cette page ? =====
	if(is_array($aNavigationModeData) == true)
	{
		if(array_key_exists($sMode, $aNavigationModeData) == true)
		{
			$sPageName = $aNavigationModeData[$sMode];
		}
	}
	else
	{
		$sPageName = $aNavigationModeData;
	}

	// ===== Le fichier existe ? =====
	if(is_file($sPageName) == false)
	{
		$sPageName = null;
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

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title>Installation du gestionnaire d'élèves</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta content="Lionel SAURON" name="author" />

	<!-- fonctions utilitaires de javascript -->
	<script type="text/javascript" src="../libraries/javascript/utils.inc.js"></script>
	<!-- JQuery -->
	<script type="text/javascript" src="../libraries/javascript/jquery-1.3.2.js"></script>
	<!-- Utilitaires basés sur JQuery -->
	<script type="text/javascript" src="../libraries/javascript/utils_jquery.inc.js"></script>

	<link rel="stylesheet" type="text/css" href="../default.css" media="all" />
	<link rel="stylesheet" type="text/css" href="../main.css" media="all" />
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="../main-ie.css" media="all" />
	<![endif]-->
</head>
<!-- ================================================== -->
<body>
	<div id="struct_left_panel">
		<div id="struct_identity">
			<h1>Installeur</h1><br />
			Installation en cours
		</div>
		<div id="struct_licence">
			<table>
				<tr>
					<td colspan="2">
						<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">
							<img src="../images/pub/button-cc.gif" alt="Creative Commons License" style="border:0;height:15px;width:80px;" />
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://validator.w3.org/check?uri=referer" rel="nofollow">
							<img src="../images/pub/button-xhtml.png" alt="Valid XHTML 1.0" style="border:0;height:15px;width=80px;" />
						</a>
					</td>
					<td>
						<a href="http://jigsaw.w3.org/css-validator/">
							<img src="../images/pub/button-css.png" alt="Valid CSS" style="border:0;height:15px;width=80px;" />
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://httpd.apache.org/">
							<img src="../images/pub/button-apache.png" alt="Powered By Apache" style="border:0;height:15px;width=80px;" />
						</a>
					</td>
					<td>
						<a href="http://www.php.net/">
							<img src="../images/pub/button-php.png" alt="Powered By PHP" style="border:0;height:15px;width=80px;" />
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.mysql.com/">
							<img src="../images/pub/button-mysql.png" alt="Powered By Mysql" style="border:0;height:15px;width=80px;" />
						</a>
					</td>
					<td>
						<a href="http://www.mozilla-europe.org/fr/firefox/">
							<img src="../images/pub/button-firefox.png" alt="Developped for Firefox" style="border:0;height:15px;width:80px;" />
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
	<div id="struct_main">
		<h1><a href="javascript:void(0)" onclick="showOrHideMenu('../images/icons/16x16/arrow_left.png', '../images/icons/16x16/arrow_right.png');"><img id="img_arrow" src="../images/icons/16x16/arrow_left.png" /></a>Installation du gestionnaire d'élèves</h1>
		<?php if($sPageName !== null): ?>
			<?php include($sPageName); ?>
		<?php endif; ?>
	</div>
</body>
</html>
<?php
//==============================================================================
// Cloture de la page
//==============================================================================

// ===== Bufferisation de sortie =====
ob_end_flush();

?>
