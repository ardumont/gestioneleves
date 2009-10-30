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

// ===== Chargement des erreurs sauvegardés =====
if(isset($_SESSION['ERROR_MESSAGE']))
{
	Message::loadFromSession($_SESSION['ERROR_MESSAGE']);
}

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

// ===== Recherche et trie toutes les versions disponibles =====

$aAllVersions = Install::getAllVersions(PATH_ROOT."/install");

// ===== Recherche de la version courante de la bdd =====

$bNewInstall    = false;
$bTooOldVersion = false;
$bReInstall     = false;

$sCurrentVersion = "";

//TODO : Enlever la gestion des vieilles versions sans table PARAMETRES (<=v3.1.0)
// lorsque l'on passera à la v4.0 et s'il y a suffisamment de version entre les 2.

// Nouvelle installation ?
$aTablesList = Database::fetchArray("SHOW TABLES");
if(count($aTablesList) == 0)
{
	$bNewInstall = true;
	$sCurrentVersion = "0.0.0";
}
else // Il y a déjà des tables
{
	// Install ratée déjà existante ?
	$sCurrentVersion = Database::fetchOneValue("SELECT CURRENT_VERSION FROM INSTALL");
	if($sCurrentVersion !== false)
	{
		$bReInstall = true;

		if($sCurrentVersion == "0.0.0")
		{
			$bNewInstall = true;
		}
		else // On a une version correcte
		{
			$bTooOldVersion = (in_array($sCurrentVersion, $aAllVersions) == true) ? false : true;
		}
	}
	else // Pas d'install raté
	{
		// Récupération de la version courante
		$sCurrentVersion = Database::fetchOneValue("SELECT VERSION FROM PARAMETRES");

		if($sCurrentVersion === false)
		{
			// Gestion des vielles versions... en temps normal on devrai considéré qu'il n'y a pas d'install
			$sCurrentVersion = "?.?.?";
			$bTooOldVersion = true;
		}
		else // On a une version correcte
		{
			// La version en cours doit être dans les étapes
			$bTooOldVersion = (in_array($sCurrentVersion, $aAllVersions) == true) ? false : true;
		}
	} // END Pas d'install raté
} // END Il y a déjà des tables

// ===== Recherche de la version à installer =====

if($bReInstall == false)
{
	// On récupère la dernière version que l'on peut installer
	$sInstallVersion = end($aAllVersions);
}
else
{
	$sInstallVersion = Database::fetchOneValue("SELECT INSTALL_VERSION FROM INSTALL");
}

// ===== Filtre les étapes =====

// On filtre suivant leur N° : version courante <= les versions <= version à installer
$aStepVersions = Install::filterLowHightVersions($aAllVersions, $sCurrentVersion, false, $sInstallVersion, false);

// On filtre les étapes suivant leur état (release ou développement (dev, alpha, beta, RC))

// Si la version courante est une version release, on doit supprimer les versions de dev du début.
// Si la version à installer est une version release, on doit supprimer les versions de dev de la fin.
$bKeepDevStart = (Install::checkReleaseVersion($sCurrentVersion) == true) ? false : true;
$bKeepDevEnd   = (Install::checkReleaseVersion($sInstallVersion) == true) ? false : true;

$aStepVersions = Install::filterReleaseVersions($aStepVersions, $bKeepDevStart, false, $bKeepDevEnd);

// ===== Recherche des actions possibles =====

$bDowngrade   = version_compare($sCurrentVersion, $sInstallVersion, '>');
$bNothingToDo = version_compare($sCurrentVersion, $sInstallVersion, '=');
$bCanUpgrade  = version_compare($sCurrentVersion, $sInstallVersion, '<');

// Si on a pas de BDD => Pas d'upgrade possible
if($bNewInstall == true)
{
	$bCanUpgrade = false;
}

// Si la version est trop vieille, on ne peut rien faire
if($bTooOldVersion == true)
{
	$bCanUpgrade  = false;
	$bNothingToDo = false;

	// Sauf si on donwgrade => c'est normale de ne pas avoir trouver la version
	if($bDowngrade == true)
	{
		$bTooOldVersion = false;
	}
}

// Install ratée => seulement new-intall ou re-install
if($bReInstall == true)
{
	$bCanUpgrade  = false;
	$bNothingToDo = false;
	$bDowngrade   = false;
}

//==============================================================================
// Préparation de l'affichage
//==============================================================================

$sGuiUpgradeDisabled = ($bCanUpgrade == false) ? "disabled=\"disabled\"" : "";

$sGuiNewInstallChecked  = (($bNewInstall == true) && ($bReInstall == false)) ? "checked=\"checked\"" : "";
$sGuiUpgradeChecked     = ($bCanUpgrade == true) ? "checked=\"checked\"" : "";
$sGuiNothingToDoChecked = ($bNothingToDo == true) ? "checked=\"checked\"" : "";

$sGuiNewInstallInformation = "";
if(($bNewInstall == false) || ($bReInstall == true))
{
	$sGuiNewInstallInformation = "<em>(Attention : Perte des données déjà présentes)</em>";
}

$sGuiUpgradeInformation = "";
if($bCanUpgrade == true)
{
	if(count($aStepVersions) > 3)
	{
		$sGuiUpgradeInformation = sprintf("(v%s -> v%s -> ...%d versions intermédiaires ... -> v%s)", $sCurrentVersion, $aStepVersions[1], (count($aStepVersions) - 3), $sInstallVersion);
	}
	elseif(count($aStepVersions) == 3)
	{
		$sGuiUpgradeInformation = sprintf("(v%s -> v%s -> v%s)", $sCurrentVersion, $aStepVersions[1], $sInstallVersion);
	}
	else
	{
		$sGuiUpgradeInformation = sprintf("(v%s -> v%s)", $sCurrentVersion, $sInstallVersion);
	}
}

$sGuiReInstallInformation = "";
if($bReInstall == true)
{
	$aTemp = Database::fetchOneRow("SELECT * FROM INSTALL");

	$nScriptStep = (int)$aTemp['SCRIPT_STEP'] + 1;
	$sReInstallType = $aTemp['INSTALL_TYPE'];

	if($sReInstallType == 'NEW_INSTALL')
	{
		$sGuiReInstallInformation = sprintf("(Installation complète v%s étape %d)", $sInstallVersion, $nScriptStep);
	}
	else
	{
		$sGuiReInstallInformation = sprintf("(Mise à jour v%s -> v%s étape %d)", $sCurrentVersion, $sInstallVersion, $nScriptStep);
	}
}

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Etape 4 - La base de données MYSQL</h2>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if($bTooOldVersion == true): ?>
	<p>Votre base de données est en version v<?php echo($sCurrentVersion); ?>.<br />
	Ce script ne peut traiter une mise à jour qu'à partir de la version v<?php echo($aStepVersions[0]); ?>.<br />
	<strong>Contactez l'équipe chargée du developpement pour avoir la procédure permettant d'effectuer une mise à jour.</strong></p>
<?php elseif($bReInstall == true): ?>
	<p>Il y a eu une erreur lors de l'installation de la version <?php echo($sInstallVersion); ?>.<br />
	Vous pouvez essayer de <em>reprendre l'installation</em> là où elle s'est arretée.</p>
<?php elseif($bDowngrade == true): ?>
	<p>Vous essayez de <em>descendre de version</em>, aucune modification à la base de données ne va être apportée.<br />
	Nous n'avons pas prévu ce cas, des erreurs pourraient avoir lieu dans l'application.<br />
	<strong>Nous vous déconseillons de continuer.</strong></p>
<?php elseif($bNothingToDo == true): ?>
	<p>Votre base de données <em>est à jour</em>.</p>
<?php elseif($bNewInstall == true): ?>
	<p>Il n'y a <em>pas de base de données existante</em>. La base de données va être <em>créée</em>.</p>
<?php else: ?>
	<p>Vous avez le choix entre <em>mettre à jour</em> votre base existante ou <em>Réinstaller</em> la base.</p>
<?php endif; ?>

<form method="post" action="?step=4&amp;mode=do">
	<fieldset>
		<legend>Opération sur la base de données MYSQL</legend>
		<input id="form_database_new_install" type="radio" name="action" value="new_install" <?php echo($sGuiNewInstallChecked); ?> />
		<label for="form_database_new_install">Installation complète / Réinstallation</label> <?php echo($sGuiNewInstallInformation); ?><br />
		<input id="form_database_upgrade" type="radio" name="action" value="upgrade" <?php echo($sGuiUpgradeDisabled); ?> <?php echo($sGuiUpgradeChecked); ?> />
		<label for="form_database_upgrade">Mise à jour</label> <?php echo($sGuiUpgradeInformation); ?><br />
		<?php if($bNothingToDo == true): ?>
			<input id="form_database_nothing" type="radio" name="action" value="nothing" checked="checked" />
			<label for="form_database_nothing">Ne rien faire</label><br />
		<?php endif; ?>
		<?php if($bReInstall == true): ?>
			<input id="form_database_re_install" type="radio" name="action" value="re_install" checked="checked" />
			<label for="form_database_re_install">Reprendre l'installation</label> <?php echo($sGuiReInstallInformation); ?><br />
		<?php endif; ?>
	</fieldset>

	<div>
		<input type="hidden" name="current_version" value="<?php echo($sCurrentVersion); ?>" />

		<a href="?step=3">Précédent</a>
		<input type="submit" value="Installer" />
	</div>
</form>
<?php
//==============================================================================
// Cloture de la page
//==============================================================================

// ===== Sauvegarde des erreurs sauvegardés =====
$_SESSION['ERROR_MESSAGE'] = Message::saveToSession();

// ===== Connexion à la base =====
Database::closeConnection();

// ===== Session =====
session_write_close();

?>
