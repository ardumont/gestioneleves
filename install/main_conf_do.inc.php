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

$objFormInstall = new FormValidation();

$sAction = $objFormInstall->getValue('action', $_POST, 'is_string', "");

$objFormInstall->read('path_root', $_POST);
$objFormInstall->testError0(null, 'exist',     "Il manque le champ \"path_root\" !");
$objFormInstall->testError0(null, 'is_string', "Le chemin absolu de la racine du site doit être une chaîne de caractères !");
$sPathRoot = $objFormInstall->get(null);

$objFormInstall->read('url_root', $_POST);
$objFormInstall->testError0(null, 'exist',     "Il manque le champ \"path_root\" !");
$objFormInstall->testError0(null, 'is_string', "L'URL de la racine du site doit être une chaîne de caractères !");
$sUrlRoot = $objFormInstall->get(null);

$objFormInstall->read('database_server', $_POST);
$objFormInstall->testError0(null, 'exist',     "Il manque le champ \"database_server\" !");
$objFormInstall->testError0(null, 'blank',     "Il manque le nom du serveur !");
$objFormInstall->testError0(null, 'is_string', "Le nom du serveur doit être une chaîne de caractères !");
$sDatabaseServer = $objFormInstall->get(null);

$objFormInstall->read('database_name', $_POST);
$objFormInstall->testError0(null, 'exist',     "Il manque le champ \"database_name\" !");
$objFormInstall->testError0(null, 'blank',     "Il manque le nom de la base !");
$objFormInstall->testError0(null, 'is_string', "Le nom de la base doit être une chaîne de caractères !");
$sDatabaseName = $objFormInstall->get(null);

$objFormInstall->read('database_login', $_POST);
$objFormInstall->testError0(null, 'exist',     "Il manque le champ \"database_login\" !");
$objFormInstall->testError0(null, 'blank',     "Il manque le nom de l'utilisateur !");
$objFormInstall->testError0(null, 'is_string', "Le nom de l'utilisateur doit être une chaîne de caractères !");
$sDatabaseLogin = $objFormInstall->get(null);

$objFormInstall->read('database_password', $_POST);
$objFormInstall->testError0(null, 'exist',     "Il manque le champ \"database_password\" !");
$objFormInstall->testError0(null, 'is_string', "Le mot de passe de l'utilisateur doit être une chaîne de caractères !");
$sDatabasePassword = $objFormInstall->get(null);

$_SESSION['PAGE']['FORM']['path_root'] = $sPathRoot;
$_SESSION['PAGE']['FORM']['url_root'] = $sUrlRoot;
$_SESSION['PAGE']['FORM']['database_server'] = $sDatabaseServer;
$_SESSION['PAGE']['FORM']['database_name'] = $sDatabaseName;
$_SESSION['PAGE']['FORM']['database_login'] = $sDatabaseLogin;
$_SESSION['PAGE']['FORM']['database_password'] = $sDatabasePassword;

// ===== Vérification des valeurs =====

switch($sAction)
{
	// ----------
	case 'calculate_path':
		$objFormInstall->clearError();
	break;

	// ----------
	case 'check_path':
		$objFormInstall->clearError('database_server');
		$objFormInstall->clearError('database_name');
		$objFormInstall->clearError('database_login');
		$objFormInstall->clearError('database_password');
	break;

	// ----------
	case 'check_connection':
		$objFormInstall->clearError('path_root');
		$objFormInstall->clearError('url_root');
	break;

	// ----------
	case 'next_step':
	break;

	// ----------
	default:
		$objFormInstall->clearError();
		Message::addError("L'action \"{$sAction}\" est inconnue !");

		// Retour à la page précédente
		header("Location: ?step=2");
		return;

} // END switch($sAction)

if($objFormInstall->hasError() == true)
{
	// On stocke toutes les erreurs de formulaire.
	Message::addErrorFromFormValidation($objFormInstall->getError());

	// Retour à la page précédente
	header("Location: ?step=2");
	return;
}

//==============================================================================
// Actions du formulaire
//==============================================================================

switch($sAction)
{
	// ----------
	case 'calculate_path':

		$_SESSION['PAGE']['FORCE_CALC_PATH'] = true;
		unset($_SESSION['PAGE']['PATH_RESULT']);

	break;

	// ----------
	case 'check_path':

		// Change for absolute dir
		$sPathRoot = realpath($sPathRoot);

		// Change all '\' in '/' for windows user. '/' work on every platform with PHP
		$sPathRoot = str_replace("\\", "/", $sPathRoot);

		$bResult = is_dir($sPathRoot);
		if($bResult == false)
		{
			unset($_SESSION['PAGE']['FORM']['path_root']);
		}

		$_SESSION['PAGE']['PATH_RESULT'] = $bResult;
	break;

	// ----------
	case 'check_connection':

		// Le gestionnaires d'erreurs qui ne fait rien (cf index.php)
		Database::setErrorHandler("nullDatabaseErrorHandler");

		$bConnectionOk = Database::openConnection($sDatabaseLogin, $sDatabasePassword, $sDatabaseName, $sDatabaseServer);

		$_SESSION['PAGE']['CONNECTION_RESULT'] = $bConnectionOk;

		if($bConnectionOk != true)
		{
			unset($_SESSION['PAGE']['FORM']['database_password']);
		}

	break;

	// ----------
	case 'next_step':

		// ===== Test du chemin =====
		// On en peut pas tester l'Url. Dommage !

		$sPathRootTemp = $sPathRoot;

		// Change for absolute dir
		$sPathRootTemp = realpath($sPathRootTemp);

		// Change all '\' in '/' for windows user. '/' work on every platform with PHP
		$sPathRootTemp = str_replace("\\", "/", $sPathRootTemp);

		$bResult = is_dir($sPathRootTemp);
		if($bResult == false)
		{
			$_SESSION['PAGE']['PATH_RESULT'] = false;

			Message::addError("Le chemin n'est pas correct. Renseigner le chemin absolu de la racine de site !");

			break;
		}

		// Suppression des "/" superflus à la fin du chemin et de l'url
		$sPathRoot = preg_replace('@/+$@', '', $sPathRoot);
		$sUrlRoot = preg_replace('@/+$@', '', $sUrlRoot);

		// ===== Test de la connexion =====

		// Le gestionnaires d'erreurs qui ne fait rien (cf index.php)
		Database::setErrorHandler("nullDatabaseErrorHandler");

		$bConnectionOk = Database::openConnection($sDatabaseLogin, $sDatabasePassword, $sDatabaseName, $sDatabaseServer);

		if($bConnectionOk != true)
		{
			$_SESSION['PAGE']['CONNECTION_RESULT'] = false;
			unset($_SESSION['PAGE']['FORM']['database_password']);

			Message::addError("La connexion à la base de données est impossible avec les paramètres saisis !");

			break;
		}

		// ===== Création des fichiers de configuration =====

		$bMainConfigFileReady = copy($sMainSampleConfigFileName, $sMainConfigFileName);
		$bDatabaseConfigFileReady = copy($sDatabaseSampleConfigFileName, $sDatabaseConfigFileName);

		if(($bMainConfigFileReady != true) || ($bDatabaseConfigFileReady != true))
		{
			if($bMainConfigFileReady != true)
			{
				Message::addError("Imposssible de créer/modifier le fichier \"{$sMainConfigFileName}\" à partir de \"{$sMainSampleConfigFileName}\" !");
			}

			if($bDatabaseConfigFileReady != true)
			{
				Message::addError("Imposssible de créer/modifier le fichier \"{$sDatabaseConfigFileName}\"  à partir de \"{$sDatabaseSampleConfigFileName}\" !");
			}

			break;
		}

		// ===== Ecriture du fichier de configuration "main.conf.php" =====

		$bConfigFileReady = false;

		$sFileContent = file_get_contents($sMainConfigFileName);
		if($sFileContent !== false)
		{
			$aPattern = array
			(
				'/^\s*define\s*\(\s*\'PATH_ROOT\'\s*,\s*"[^"]*"\s*\)\s*;/m',
				'/^\s*define\s*\(\s*\'URL_ROOT\'\s*,\s*"[^"]*"\s*\)\s*;/m'
			);

			$aReplace = array
			(
				'define(\'PATH_ROOT\', "'.$sPathRoot.'");',
				'define(\'URL_ROOT\', "'.$sUrlRoot.'");'
			);

			$sFileContent = preg_replace($aPattern, $aReplace, $sFileContent);

			$nByteWrited = file_put_contents($sMainConfigFileName, $sFileContent);

			$bConfigFileReady = ($nByteWrited !== false) ? true : false;
		}

		if($bConfigFileReady == false)
		{
			Message::addError("Impossible d'enregistrer le fichier \"{$sMainConfigFileName}\" !");

			break;
		}

		// ===== Ecriture du fichier de configuration "database.conf.php" =====

		$bConfigFileReady = false;

		$sFileContent = file_get_contents($sDatabaseConfigFileName);
		if($sFileContent !== false)
		{
			$aPattern = array
			(
				'/^\s*define\s*\(\s*\'DATABASE_SERVER\'\s*,\s*"[^"]*"\s*\)\s*;/m',
				'/^\s*define\s*\(\s*\'DATABASE_NAME\'\s*,\s*"[^"]*"\s*\)\s*;/m',
				'/^\s*define\s*\(\s*\'DATABASE_LOGIN\'\s*,\s*"[^"]*"\s*\)\s*;/m',
				'/^\s*define\s*\(\s*\'DATABASE_PASSWORD\'\s*,\s*"[^"]*"\s*\)\s*;/m',
			);

			$aReplace = array
			(
				'define(\'DATABASE_SERVER\', "'.$sDatabaseServer.'");',
				'define(\'DATABASE_NAME\', "'.$sDatabaseName.'");',
				'define(\'DATABASE_LOGIN\', "'.$sDatabaseLogin.'");',
				'define(\'DATABASE_PASSWORD\', "'.$sDatabasePassword.'");',
			);

			$sFileContent = preg_replace($aPattern, $aReplace, $sFileContent);

			$nByteWrited = file_put_contents($sDatabaseConfigFileName, $sFileContent);

			$bConfigFileReady = ($nByteWrited !== false) ? true : false;
		}

		if($bConfigFileReady == false)
		{
			Message::addError("Impossible d'enregistrer le fichier \"{$sDatabaseConfigFileName}\" !");

			break;
		}

		// On efface les variables de session spécifique à la page traitée.
		unset($_SESSION['PAGE']);

		// Page suivante
		header("Location: ?step=4");
		return;
	break;

} // END switch($sAction)

//==============================================================================
// Traitement des données
//==============================================================================

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

// Retour à la page précédente
header("Location: ?step=2");

?>
