<?php
//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Fichier de configuration principal =====
require_once(PATH_CONF_INSTALL."/main.conf.php");

// ===== Les librairies et les classes =====
require_once(PATH_PHP_LIB."/utils.lib.php");
require_once(PATH_PHP_LIB."/database.class.php");
require_once(PATH_PHP_LIB."/formvalidation.class.php");
require_once(PATH_PHP_LIB."/message.class.php");

//==============================================================================
// Préparation des données
//==============================================================================

$sConfigFileName       = PATH_CONFIG."/database.conf.php";
$sSampleConfigFileName = PATH_CONFIG."/database.sample.conf.php";

//==============================================================================
// Validation du formulaire
//==============================================================================

$objFormInstall = new FormValidation();

$sAction = $objFormInstall->getValue('action', $_POST, 'is_string', "");

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

// On stocke toutes les erreurs de formulaire.
Message::addErrorFromFormValidation($objFormInstall->getError());

//==============================================================================
// Actions du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'suivant':
		if($objFormInstall->hasError() == true) break;

		Database::setErrorHandler("nullDatabaseErrorHandler"); // Le gestionnaires d'erreurs (cf index.php)

		$bConnectionOk = Database::openConnection($sDatabaseLogin, $sDatabasePassword, $sDatabaseName, $sDatabaseServer);

		if($bConnectionOk != true) break;

		// ===== Ecriture du fichier de configuration =====

		$bConfigFileReady = copy($sSampleConfigFileName, $sConfigFileName);

		if($bConfigFileReady == true)
		{
			$sFileContent = file_get_contents($sConfigFileName);
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

				$nByteWrited = file_put_contents($sConfigFileName, $sFileContent);

				$bConfigFileReady = ($nByteWrited !== false) ? true : false;
			}
			else
			{
				$bConfigFileReady = false;
			}
		}
		else
		{
			$bConfigFileReady = false;
		}

	break;

	// ----------
	default:
		// Rechargement
		header("Location: ?step=3");
		return;
}

//==============================================================================
// Traitement des données
//==============================================================================

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

// Rechargement
header("Location: ?step=3&mode=end&connection={$bConnectionOk}&ready={$bConfigFileReady}");
return;
