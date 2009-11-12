<?php
//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Fichier de configuration principal =====
require_once(PATH_CONF_INSTALL."/main.conf.php");

//==============================================================================
// Préparation des données
//==============================================================================

$sConfigFileName       = PATH_CONFIG."/database.conf.php";
$sSampleConfigFileName = PATH_CONFIG."/database.sample.conf.php";

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

// ===== Relecture du fichier de configuration =====

$sDatabaseServer = "";
$sDatabaseName   = "";
$sDatabaseLogin  = "";

if(is_file($sConfigFileName) == true)
{
	include($sConfigFileName);

	$sDatabaseServer = constant('DATABASE_SERVER');
	$sDatabaseName   = constant('DATABASE_NAME');
	$sDatabaseLogin  = constant('DATABASE_LOGIN');
}
else
{
	include($sSampleConfigFileName);

	$sDatabaseServer = constant('DATABASE_SERVER');
	$sDatabaseName   = constant('DATABASE_NAME');
}

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Etape 3 - Fichier de configuration de la base MYSQL</h2>

<p>Les valeurs affichées sont reprises de votre fichier de configuration actuel.</p>

<form method="post" action="?step=3&amp;mode=do">
	<table class="formulaire">
		<caption>Paramètres de la base de données MySQL</caption>
		<tbody>
			<tr>
				<th><label for="form_database_server">Nom du serveur</label></th>
				<td><input type="text" id="form_database_server" name="database_server" value="<?php echo($sDatabaseServer); ?>" /></td>
			</tr>
			<tr>
				<th><label for="form_database_name">Nom de la base</label></th>
				<td><input type="text" id="form_database_name" name="database_name" value="<?php echo($sDatabaseName); ?>" /></td>
			</tr>
			<tr>
				<th><label for="form_database_login">Nom de l'utilisateur de la base</label></th>
				<td><input type="text" id="form_database_login" name="database_login" value="<?php echo($sDatabaseLogin); ?>" /></td>
			</tr>
			<tr>
				<th><label for="form_database_password">Mot de passe de l'utilisateur</label></th>
				<td><input type="password" id="form_database_password" name="database_password" /></td>
			</tr>
		</tbody>
	</table>
	<div>
		<br />
		<a href="?step=2">Précédent</a>
		<input type="submit" name="action" value="Suivant" />
	</div>
</form>