<?php
//==============================================================================
// Préparation des données
//==============================================================================

define('PHP_VERSION_WANTED', "5.1.0");

$sMainConfigFileName     = PATH_INSTALL_ROOT."/config/main.conf.php";
$sDatabaseConfigFileName = PATH_INSTALL_ROOT."/config/database.conf.php";

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

$bAllTestOk = true;

// ===== Test de la version =====

$bPhpVersionResult = version_compare(PHP_VERSION, PHP_VERSION_WANTED, '>=');

$bAllTestOk = ($bPhpVersionResult == true) ? $bAllTestOk : false;

// ===== Test des "magic quotes" =====

$bMagicQuotesRuntime = (ini_get('magic_quotes_runtime') == "1") ? true : false;

$bAllTestOk = ($bMagicQuotesRuntime == false) ? $bAllTestOk : false;

// ===== Test des fichiers de configuration =====

$aMainConfigResult     = Install::checkConfigFileWritable($sMainConfigFileName);
$aDatabaseConfigResult = Install::checkConfigFileWritable($sDatabaseConfigFileName);

$bAllTestOk = ($aMainConfigResult['WRITABLE'] == true)     ? $bAllTestOk : false;
$bAllTestOk = ($aDatabaseConfigResult['WRITABLE'] == true) ? $bAllTestOk : false;

//==============================================================================
// Préparation de l'affichage
//==============================================================================

$GuiResultOk = "<img src=\"".URL_INSTALL_ROOT."/images/icons/16x16/action-success.png\" alt=\"Réussi\" />";
$GuiResultKo = "<img src=\"".URL_INSTALL_ROOT."/images/icons/16x16/action-fail.png\" alt=\"Echec\" />";

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Etape 1 - Vérification des pré-requis</h2>

<fieldset class="inline">
	<legend>Pré-requis pour l'application</legend>
	<table class="form">
		<tr>
			<th>PHP Version &gt;= <?php echo(PHP_VERSION_WANTED); ?> :</th>
			<td>
				<?php echo($bPhpVersionResult ? $GuiResultOk : $GuiResultKo); ?>
				(<?php echo(PHP_VERSION); ?>)
			</td>
		</tr>
	</table>
</fieldset>
<br />
<fieldset class="inline">
	<legend>Pré-requis pour l'installation</legend>
	<table class="form">
		<tr>
			<th>Fichier de configuration principale :</th>
			<td>
				<?php echo($aMainConfigResult['WRITABLE'] ? $GuiResultOk : $GuiResultKo); ?>
				(<?php echo($aMainConfigResult['EXIST'] ? "Modification" : "Création"); ?>)
			</td>
		</tr>
		<tr>
			<th>Fichier de configuration de la base :</th>
			<td>
				<?php echo($aDatabaseConfigResult['WRITABLE'] ? $GuiResultOk : $GuiResultKo); ?>
				(<?php echo($aDatabaseConfigResult['EXIST'] ? "Modification" : "Création"); ?>)
			</td>
		</tr>
		<tr>
			<th>magic_quotes_runtime = Off :</th>
			<td><?php echo($bMagicQuotesRuntime ? $GuiResultKo : $GuiResultOk); ?></td>
		</tr>
	</table>
</fieldset>
<?php if(($aMainConfigResult['WRITABLE'] == false) || ($aDatabaseConfigResult['WRITABLE'] == false)): ?>
	<p>Le dossier de configuration ou les fichiers qu'il contient ne sont pas accessibles en écriture.<br />
	Ce script ne peut créer/modifier les fichiers de configuration.
	</p>
<?php endif; ?>
<?php if($bMagicQuotesRuntime == true): ?>
	<p>Les "Magic quotes" sont une nuissance. Elles sont <a href="http://fr.php.net/manual/fr/security.magicquotes.php">condamnées à disparaître</a>.<br />
	Nous vous encourageons donc à les <a href="http://fr.php.net/manual/fr/security.magicquotes.disabling.php">désactiver</a>.
	</p>
<?php endif; ?>

<?php if($bAllTestOk == false): ?>
	<p><strong>Désolé, ces pré-requis sont indispensables au fonctionnement de l'application.<br />
	Corrigez les problèmes et relancer le processus d'installation en rechargeant la page (touche F5 ou le bouton "refresh").</strong>
	</p>
<?php endif; ?>

<?php if($bAllTestOk == true): ?>
	<br />
	<div><a href="?step=2">Suivant</a></div>
<?php endif; ?>
