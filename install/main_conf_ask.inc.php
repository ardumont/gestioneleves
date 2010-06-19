<?php
//==============================================================================
// Préparation des données
//==============================================================================

$sMainConfigFileName			= PATH_INSTALL_ROOT."/config/main.conf.php";
$sMainSampleConfigFileName		= PATH_INSTALL_ROOT."/config/main.sample.conf.php";
$sDatabaseConfigFileName		= PATH_INSTALL_ROOT."/config/database.conf.php";
$sDatabaseSampleConfigFileName	= PATH_INSTALL_ROOT."/config/database.sample.conf.php";

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

// ===== Recherche des informations pour le fichier "main.conf.php" =====

$sPathRoot	= "";
$sUrlRoot	= "";

if((isset($_SESSION['PAGE']['FORM']) == true) && (isset($_SESSION['PAGE']['FORCE_CALC_PATH']) == false))
{
	// Reprise des infos saisies
	$sPathRoot	= @$_SESSION['PAGE']['FORM']['path_root'];
	$sUrlRoot	= @$_SESSION['PAGE']['FORM']['url_root'];
}
elseif((is_file($sMainConfigFileName) == true) && (isset($_SESSION['PAGE']['FORCE_CALC_PATH']) == false))
{
	// Relecture du fichier de configuration existant

	ob_start();
	@include($sMainConfigFileName);

	$sPathRoot	= constant('PATH_ROOT');
	$sUrlRoot	= constant('URL_ROOT');
	ob_end_clean();
}
else // Determiner les chemins
{
	unset($_SESSION['PAGE']['FORCE_CALC_PATH']);

	// ===== Recherche de PATH_ROOT =====

	// Path of this file => PATH_ROOT/install
	$sPathRoot = dirname(__FILE__);

	// Change for absolute parent dir => PATH_ROOT
	$sPathRoot = realpath("{$sPathRoot}/..");

	// Change all '\' in '/' for windows user. '/' work on every platform with PHP
	$sPathRoot = str_replace("\\", "/", $sPathRoot);

	// ===== Recherche de URL_ROOT =====

	// Url of this page => /URL_ROOT/install/?step=2
	$sUrlRoot = $_SERVER['REQUEST_URI'];

	// Parse URL => /URL_ROOT/install/
	$aTemp = parse_url($sUrlRoot);
	$sUrlRoot = $aTemp['path'];

	// Only dirname  => /URL_ROOT
	$sUrlRoot = dirname($sUrlRoot);
} // END Determiner les chemins

// ===== Recherche des informations pour le fichier "database.conf.php" =====

$sDatabaseServer	= "";
$sDatabaseName		= "";
$sDatabaseLogin		= "";
$sDatabasePassword	= "";

if(isset($_SESSION['PAGE']['FORM']) == true)
{
	// Reprise des infos saisies
	$sDatabaseServer	= @$_SESSION['PAGE']['FORM']['database_server'];
	$sDatabaseName		= @$_SESSION['PAGE']['FORM']['database_name'];
	$sDatabaseLogin		= @$_SESSION['PAGE']['FORM']['database_login'];
	$sDatabasePassword	= @$_SESSION['PAGE']['FORM']['database_password'];
}
elseif(is_file($sDatabaseConfigFileName) == true)
{
	// Relecture du fichier de configuration existant

	ob_start();
	@include($sDatabaseConfigFileName);

	$sDatabaseServer = constant('DATABASE_SERVER');
	$sDatabaseName   = constant('DATABASE_NAME');
	$sDatabaseLogin  = constant('DATABASE_LOGIN');
	ob_end_clean();
}
else
{
	// Relecture du fichier de configuration d'exemple

	ob_start();
	@include($sDatabaseSampleConfigFileName);

	$sDatabaseServer = constant('DATABASE_SERVER');
	$sDatabaseName   = constant('DATABASE_NAME');
	ob_end_clean();
}

//==============================================================================
// Préparation de l'affichage
//==============================================================================

$GuiCheckPathResult = "";
if(isset($_SESSION['PAGE']['PATH_RESULT']) == true)
{
	$sButtonName = ($_SESSION['PAGE']['PATH_RESULT']) ? "success": "fail";
	$sAltButtonName = ($_SESSION['PAGE']['PATH_RESULT']) ? "Réussi": "Echec";
	$GuiCheckPathResult = "&nbsp;<img src=\"".URL_INSTALL_ROOT."/images/icons/16x16/action-{$sButtonName}.png\" alt=\"{$sAltButtonName}\" />";
}

$GuiCheckUrlResult = "";
if(isset($_SESSION['PAGE']['PATH_RESULT']) == true)
{
	// C'est crade au niveau accessibilité, car le texte ne correspond pas à l'image. Mais pour un voyant c'est parfait.
	// Si la variable $sUrlRoot est correct, alors il a l'image "OK" sinon il a le texte "Echec"
	$GuiCheckUrlResult = "&nbsp;<img src=\"{$sUrlRoot}/images/icons/16x16/action-success.png\" alt=\"Echec\" />";
}

$GuiConnectionResult = "";
if(isset($_SESSION['PAGE']['CONNECTION_RESULT']) == true)
{
	$sButtonName = ($_SESSION['PAGE']['CONNECTION_RESULT']) ? "success": "fail";
	$sAltButtonName = ($_SESSION['PAGE']['CONNECTION_RESULT']) ? "Réussi": "Echec";
	$GuiConnectionResult = "&nbsp;<img src=\"".URL_INSTALL_ROOT."/images/icons/16x16/action-{$sButtonName}.png\" alt=\"{$sAltButtonName}\" />";
}

if(is_file($sMainConfigFileName) == true)
{
	$sMessageInfo = "Les valeurs affichées sont reprises de votre fichier de configuration actuel.";
}
else
{
	$sMessageInfo = "Les valeurs affichées sont calculées par ce script.\n" .
					"Ne modifiez les valeurs par défaut que si vous avez déjà eu un problème avec celles-ci.";
}

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Etape 2 - Fichiers de configuration principale</h2>

<?php if(Message::hasError() == true): ?>
<ul class="messagebox_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<div class="messagebox_info"><?php echo(nl2br($sMessageInfo)); ?></div>

<form method="post" action="?step=2&amp;mode=do">
	<fieldset class="inline">
		<legend>Chemins et URLs</legend>
		<table class="form">
			<tr>
				<th><label for="form_path_root">Chemin absolu de la racine du site<?php printHtmlRequiredField(); ?> :</label></th>
				<td><input type="text" id="form_path_root" name="path_root" size="80" value="<?php echo($sPathRoot); ?>" /><?php echo($GuiCheckPathResult); ?></td>
			</tr>
			<tr>
				<th><label for="form_url_root">URL de la racine du site<?php printHtmlRequiredField(); ?> :</label></th>
				<td>http://<?php echo(getenv('SERVER_NAME')); ?>:<?php echo(getenv('SERVER_PORT')); ?>&nbsp;<input type="text" id="form_url_root" name="url_root" size="58" value="<?php echo($sUrlRoot); ?>" /><?php echo($GuiCheckUrlResult); ?></td>
			</tr>
			<tr>
				<th colspan="2" style="text-align:center;">
					<button type="submit" name="action" value="calculate_path">Calculer</button>&nbsp;
					<button type="submit" name="action" value="check_path">Tester</button>
				</th>
			</tr>
		</table>
	</fieldset>
	<br />
	<fieldset class="inline">
		<legend>Connexion à la base de données MySQL</legend>
		<table class="form">
			<tr>
				<th><label for="form_database_server">Nom du serveur<?php printHtmlRequiredField(); ?> :</label></th>
				<td><input type="text" id="form_database_server" name="database_server" value="<?php echo($sDatabaseServer); ?>" /><?php echo($GuiConnectionResult); ?></td>
			</tr>
			<tr>
				<th><label for="form_database_name">Nom de la base<?php printHtmlRequiredField(); ?> :</label></th>
				<td><input type="text" id="form_database_name" name="database_name" value="<?php echo($sDatabaseName); ?>" /><?php echo($GuiConnectionResult); ?></td>
			</tr>
			<tr>
				<th><label for="form_database_login">Nom de l'utilisateur de la base<?php printHtmlRequiredField(); ?> :</label></th>
				<td><input type="text" id="form_database_login" name="database_login" value="<?php echo($sDatabaseLogin); ?>" /><?php echo($GuiConnectionResult); ?></td>
			</tr>
			<tr>
				<th><label for="form_database_password">Mot de passe de l'utilisateur :</label></th>
				<td><input type="password" id="form_database_password" name="database_password" value="<?php echo($sDatabasePassword); ?>"/><?php echo($GuiConnectionResult); ?></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<button type="submit" name="action" value="check_connection">Tester</button>
				</td>
			</tr>
		</table>
	</fieldset>
	<div>
		<br />
		<a href="?step=1">Précédent</a>
		<button type="submit" name="action" value="next_step">Suivant</button>
	</div>
</form>
