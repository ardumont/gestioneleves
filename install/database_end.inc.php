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

$aInstallResults = $_SESSION['PAGE_RESULTS'];

$sInstallVersion = $_SESSION['INSTALL_VERSION'];

//==============================================================================
// Validation du formulaire
//==============================================================================

$objFormInstall = new FormValidation();

$bDatabaseReady = $objFormInstall->getValue('database_ready', $_GET, 'convert_bool', false);

// ===== Vérification des valeurs =====

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

// ===== Est-ce la fin ? ====

$aAllVersions = Install::getAllVersions(PATH_ROOT."/install");

// On filtre suivant leur N° : version que l'on vient d'installer <= les versions <= version finale à installer
$aStepVersions = Install::filterLowHightVersions($aAllVersions, $sInstallVersion, false, null, false);

// on force la renumérotation des clefs
$aStepVersions = array_merge($aStepVersions);

$bNeedMoreUpgrade = ($sInstallVersion == end($aStepVersions)) ? false : true;

//==============================================================================
// Préparation de l'affichage
//==============================================================================

// Le statut "user friendly"
foreach($aInstallResults as $vKey => $aOneRow)
{
	switch($aOneRow['STATUS'])
	{
		case 'OK':       $aInstallResults[$vKey]['GUI_STATUS'] = "OK !"; break;
		case 'FAILED':   $aInstallResults[$vKey]['GUI_STATUS'] = "KO !"; break;
		case 'NOT_DONE': $aInstallResults[$vKey]['GUI_STATUS'] = "---"; break;
		case 'IGNORED':  $aInstallResults[$vKey]['GUI_STATUS'] = "Ignoré !"; break;
		default:         $aInstallResults[$vKey]['GUI_STATUS'] = $aOneRow['STATUS']; break;
	}
}

// Les mises à jour pour l'upgrade
$sGuiUpgradeInformation = "";
if(($bDatabaseReady == true) && ($bNeedMoreUpgrade == true))
{
	$sFinalVersion = end($aStepVersions);

	if(count($aStepVersions) > 3)
	{
		$sGuiUpgradeInformation = sprintf("(v%s -> v%s -> ...%d versions intermédiaires ... -> v%s)", $sInstallVersion, $aStepVersions[1], (count($aStepVersions) - 3), $sFinalVersion);
	}
	elseif(count($aStepVersions) == 3)
	{
		$sGuiUpgradeInformation = sprintf("(v%s -> v%s -> v%s)", $sInstallVersion, $aStepVersions[1], $sFinalVersion);
	}
	else
	{
		$sGuiUpgradeInformation = sprintf("(v%s -> v%s)", $sInstallVersion, $sFinalVersion);
	}
}

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Etape 4 - La base de données MYSQL</h2>

<table class="list_tree">
	<caption>base de données pour la version <?php echo($sInstallVersion); ?></caption>
	<thead>
		<tr>
			<th>Opération</th>
			<th>Résultat</th>
			<th>Etapes</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($aInstallResults as $n => $aOneRow): ?>
		<tr class="level0_row<?php echo $n%2?>">
			<td><?php echo($aOneRow['TITLE']); ?></td>
			<td><?php echo($aOneRow['GUI_STATUS']); ?></td>
			<td><?php echo($aOneRow['STEP_DONE']+$aOneRow['STEP_IGNORED']); ?>/<?php echo($aOneRow['STEP_COUNT']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php if(($bDatabaseReady == true) && ($bNeedMoreUpgrade == true)): ?>
	<p>Il reste <em>encore des mises à jour</em> à faire. Nous vous <em>recommandons de les faire immédiatement</em>.<br />
	<?php echo($sGuiUpgradeInformation); ?><br />
	<br />
	<em>L'utilisation de l'application est fortement déconseillée sans ces mises à jour.</em><br />
	<br />
	 Cependant, si vous ne souhaitez pas réaliser maintenant ces mises à jour, vous pouvez arrÃªter ici<br />
	 et reprendre plus tard en relançant l'installation.<br />
<?php endif; ?>

<div>
	<a href="?step=4">Précédent</a>
	<?php if($bDatabaseReady == true): ?>
		<?php if($bNeedMoreUpgrade == false): ?>
			<a href="?step=5">Suivant</a>
		<?php else: ?>
			<a href="?step=4&amp;mode=do&amp;action=upgrade">Mise à niveau suivante</a>
		<?php endif; ?>
	<?php endif; ?>
</div>
