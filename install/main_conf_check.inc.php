<?php
//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

// ===== Recherche de PATH_ROOT =====

// Path of this file => PATH_ROOT/install
$sDefaultPathRoot = dirname(__FILE__);

// Change for parent dir => PATH_ROOT
$sDefaultPathRoot = realpath("{$sDefaultPathRoot}/..");

// Change all '\' in '/' for windows user. '/' work on every platform with PHP
$sDefaultPathRoot = str_replace("\\", "/", $sDefaultPathRoot);

// ===== Recherche de URL_ROOT =====

// Url of this page => /URL_ROOT/install/?step=2
$sDefaultUrlRoot = $_SERVER['REQUEST_URI'];

// Parse URL => /URL_ROOT/install/
$aTemp = parse_url($sDefaultUrlRoot);
$sDefaultUrlRoot = $aTemp['path'];

// Only dirname  => /URL_ROOT
$sDefaultUrlRoot = dirname($sDefaultUrlRoot);

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Etape 2 - Fichier de configuration principale</h2>

<p>Les valeurs affichées sont calculées par ce script.<br />
Ne modifiez les valeurs par défaut que si vous avez déjà eu un problème avec celles-ci.</p>

<form method="post" action="?step=2&amp;mode=do">
	<table class="formulaire">
		<caption style="text-align:left;">Paramètres de configuration principale</caption>
		<tbody>
			<tr>
				<td><label for="form_path_root">Chemin absolu jusqu'à la racine du site</label></td>
				<td><input type="text" id="form_path_root" name="path_root" size="70" value="<?php echo($sDefaultPathRoot); ?>" /> ( pas de "/" à la fin)</td>
			</tr>
			<tr>
				<td><label for="form_url_root">URL de la racine du site</label></td>
				<td>http://<?php echo(getenv('SERVER_NAME')); ?>:<?php echo(getenv('SERVER_PORT')); ?><input type="text" id="form_url_root" name="url_root" size="51" value="<?php echo($sDefaultUrlRoot); ?>" /> ( pas de "/" à la fin)</td>
			</tr>
		</tbody>
	</table>
	<div>
		<a href="?step=1">Précédent</a>
		<input type="submit" name="action" value="Suivant" />
	</div>
</form>