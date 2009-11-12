<?php
//==============================================================================
// Les fonctions pour cette page
//==============================================================================

/**
 * Permet de tester si on va pouvoir créer/modifier un fichier de configuration
 *
 * Le tableau retourné contient 2 valeurs :
 * - 'EXIST'    => Pour indiquer si le fichier existe
 * - 'WRITABLE' => Pour indiquer si le fichier pourra être créer/modifier
 *
 * @author Lionel SAURON
 * @version 1.0
 * @public
 *
 * @param $sConfigFileName(string) Le nom du fichier à tester
 * @return (array) Tableau résultat
 */
function testConfigFile($sConfigFileName)
{
	$aConfigFile = array
	(
		'EXIST'    => false,
		'WRITABLE' => false,
	);

	// ===== Existe t'il ? =====
	if(is_file($sConfigFileName) == true)
	{
		$aConfigFile['EXIST'] = true;
	}

	// ===== Peut t'on modifier le fichier, ou le créer ? =====

	// Attention si le fichier n'existe pas, il faut si on peut ecrire dans le dossier
	if($aConfigFile['EXIST'] == false)
	{
		$sConfigFileName = dirname($sConfigFileName);
	}

	if((file_exists($sConfigFileName) == true) && (is_writable($sConfigFileName) == true))
	{
		$aConfigFile['WRITABLE'] = true;
	}

	return $aConfigFile;
}

//==============================================================================
// Préparation des données
//==============================================================================

define('PHP_VERSION_WANTED', "5.1.0");

$sMainConfigFileName     = PATH_CONF_INSTALL."/main.conf.php";
$sDatabaseConfigFileName = PATH_CONF_INSTALL."/database.conf.php";

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

$aMainConfigResult     = testConfigFile($sMainConfigFileName);
$aDatabaseConfigResult = testConfigFile($sDatabaseConfigFileName);

$bAllTestOk = ($aMainConfigResult['WRITABLE'] == true)     ? $bAllTestOk : false;
$bAllTestOk = ($aDatabaseConfigResult['WRITABLE'] == true) ? $bAllTestOk : false;

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Etape 1 - Vérification des pré-requis</h2>

<table class="formulaire">
	<caption>Pré-requis pour l'application</caption>
	<tbody>
		<tr>
			<th>PHP Version &gt;= <?php echo(PHP_VERSION_WANTED); ?></th>
			<td><?php echo($bPhpVersionResult ? "OK !" : "KO !"); ?></td>
			<td>(<?php echo(PHP_VERSION); ?>)</td>
		</tr>
	</tbody>
</table>
<br />
<table class="formulaire">
	<caption>Pré-requis pour l'installation</caption>
	<tbody>
		<tr>
			<th>Fichier de configuration principale</th>
			<td><?php echo($aMainConfigResult['WRITABLE'] ? "OK !" : "KO !"); ?></td>
			<td>(<?php echo($aMainConfigResult['EXIST'] ? "Modification" : "Création"); ?>)</td>
		</tr>
		<tr>
			<th>Fichier de configuration de la base</th>
			<td><?php echo($aDatabaseConfigResult['WRITABLE'] ? "OK !" : "KO !"); ?></td>
			<td>(<?php echo($aDatabaseConfigResult['EXIST'] ? "Modification" : "Création"); ?>)</td>
		</tr>
		<tr>
			<th>magic_quotes_runtime = Off</th>
			<td><?php echo($bMagicQuotesRuntime ? "KO !" : "OK !"); ?></td>
			<td></td>
		</tr>
	</tbody>
</table>
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
