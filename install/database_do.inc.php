<?php
//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Les fichiers de configuration =====

// Le fichier principal
require_once(PATH_INSTALL_ROOT."/config/main.conf.php");

// Les autres fichiers de configurations
require_once(PATH_CONFIG."/database.conf.php");

// ===== La base de données =====

// Le gestionnaire d'erreurs de la base (cf index.php)
Database::setErrorHandler("nullDatabaseErrorHandler");

// Connexion à la base
Database::openConnection(DATABASE_LOGIN, DATABASE_PASSWORD, DATABASE_NAME, DATABASE_SERVER);

// On précise à la base qu'on travaille en UTF-8
Database::execute("SET NAMES UTF8");

//==============================================================================
// Préparation des données
//==============================================================================

// Recherche et trie toutes les versions disponibles
$aAllVersions = Install::getAllVersions(PATH_ROOT."/install");

//==============================================================================
// Validation du formulaire
//==============================================================================

$objFormInstall = new FormValidation();

$sAction = $objFormInstall->getValue('action', $_GET, 'is_string', "");
$sAction = $objFormInstall->getValue('action', $_POST, 'is_string', $sAction);

$objFormInstall->read('current_version', $_POST);
$objFormInstall->testValue0(null, 'exist',     "0.0.0");
$objFormInstall->testError0(null, 'blank',     "Il manque la version courante !");
$objFormInstall->testError0(null, 'is_string', "La version courante doit être une chaîne de caractères !");
$sCurrentVersion = $objFormInstall->get(null);

// ===== Vérification des valeurs =====

//==============================================================================
// Actions du formulaire
//==============================================================================

//$sCurrentVersion       // La version de la base avant le début de l'install
$sInstallVersion = null; // La version à installer
$sScriptVersion  = null; // La version du script utilisé
$nScriptStep     = 0;    // Etape déjà installé. On reprend à celle d'après, on incrémente si l'étape est OK

$bUseScriptFile = true;
$sSqlFileName   = "";

switch(strtoupper($sAction))
{

	case 'NEW_INSTALL':
		// On garde $sCurrentVersion de l'IHM car c'est juste pour information

		// On garde toutes les versions car on va essayer de trouver le premier script d'install possible

		// On install la version la plus recente
		$sInstallVersion = array_pop($aAllVersions);

		// La version servant de réference pour le script à utliser
		$sScriptVersion = $sInstallVersion;

		$sSqlFileName = PATH_ROOT."/install/v{$sScriptVersion}/install.sql";

		// Si le fichier n'existe pas => version sans script => on fait avec le 1er script qui existe
		//  mais on mettra à jour la table PARAMETRE avec le n° initial
		while(($sScriptVersion !== null) && (is_file($sSqlFileName) == false))
		{
			$sScriptVersion = array_pop($aAllVersions);

			$sSqlFileName = PATH_ROOT."/install/v{$sScriptVersion}/install.sql";
		}

	break;

	// ----------
	case 'UPGRADE':
		// On récupère la version courante (on fait pas confiance à l'IHM)
		$sCurrentVersion = Database::fetchOneValue("SELECT VERSION FROM PARAMETRES");

		// On filtre suivant leur N° : version courante < les versions <= version à installer
		$aStepVersions = Install::filterLowHightVersions($aAllVersions, $sCurrentVersion, true, null, false);

		// Si la version courante est une version release, on doit supprimer les versions de dev du début.
		// On conserve les versions de dev de la fin (s'il y en a).
		$bCurrentIsRealease = Install::checkReleaseVersion($sCurrentVersion);
		$bKeepDevStart = ($bCurrentIsRealease == true) ? false : true;

		// On filtre les étapes suivant leur état (release ou développement (dev, alpha, beta, RC))
		$aStepVersions = Install::filterReleaseVersions($aStepVersions, $bKeepDevStart, false, true);

		// On récupére la version vers laquel on va faire l'upgrade
		$sInstallVersion = array_shift($aStepVersions);

		$sScriptVersion = $sInstallVersion; // La version servant de réference pour le script à utliser

		$sSqlFileName = PATH_ROOT."/install/v{$sScriptVersion}/upgrade.sql";

		// Pas de script ? => Alors on doit juste faire une mise à jour du N° de version
		if(is_file($sSqlFileName) == false)
		{
			$bUseScriptFile = false;
			$sScriptVersion = "increment_version";
		}

		// Si la version courante est "dev", et que la version à installer est "release"
		// => on change juste le numéro... on a déjà fait tout le boulot lors de la dernière version (normalement une RC)
		$bInstallIsRealease = Install::checkReleaseVersion($sInstallVersion);

		if(($bCurrentIsRealease == false) && ($bInstallIsRealease == true))
		{
			$bUseScriptFile = false;
			$sScriptVersion = "increment_version";
		}
	break;

	// ----------
	case 'RE_INSTALL':
		$aTemp = Database::fetchOneRow("SELECT * FROM INSTALL");

		$sCurrentVersion = $aTemp['CURRENT_VERSION'];
		$sInstallVersion = $aTemp['INSTALL_VERSION'];
		$sScriptVersion  = $aTemp['SCRIPT_VERSION'];
		$nScriptStep     = (int)$aTemp['SCRIPT_STEP'];

		if($aTemp['INSTALL_TYPE'] == 'NEW_INSTALL')
		{
			$sSqlFileName = PATH_ROOT."/install/v{$sScriptVersion}/install.sql";
		}
		else // 'UPGRADE'
		{
			$sSqlFileName = PATH_ROOT."/install/v{$sScriptVersion}/upgrade.sql";

			if($sScriptVersion == 'increment_version')
			{
				$bUseScriptFile = false;
			}
		} // END 'UPGRADE'

	break;

	// ----------
	case 'NOTHING':
		// Rechargement
		header("Location: ?step=5");
		return;
	break;

	// ----------
	default:
		Message::addError("L'action \"{$sAction}\" est inconnue !");

		// Sauvegarde des erreurs sauvegardés
		$_SESSION['ERROR_MESSAGE'] = Message::saveToSession();

		// Rechargement
		header("Location: ?step=4");
		return;
}

//==============================================================================
// Traitement des données
//==============================================================================

$bInstallOk = true; // L'installation se passe bien ?

$nCurrentStep  = 0;  // Etape courante d'installation. On incrémente au début de l'étape

$aInstallResults = array();
$bDatabaseReady = true;

// ===== Sauvegarde des données interessantes pour l'affichage =====

$_SESSION['INSTALL_VERSION'] = $sInstallVersion;

// ===== Lecture du fichier sql =====

$sFileContent = "";

if($bUseScriptFile == true)
{
	if(is_file($sSqlFileName) == false)
	{
		Message::addError("Le script SQL pour l'installation n'existe pas !");

		// Sauvegarde des erreurs sauvegardés
		$_SESSION['ERROR_MESSAGE'] = Message::saveToSession();

		// Rechargement
		header("Location: ?step=4");
		return;
	}

	$sFileContent = file_get_contents($sSqlFileName);

	// Enleve les commentaires du type "/* */"
	$sFileContent = preg_replace('/\/\*(?:(?!\*\/).)*\*\//sm', '', $sFileContent);
}

// ===== Ajout des requêtes de base  =====

$aLinesAdded = array();

//XXX : Si cette étape plante, il faut supprimer à la main la table INSTALL en BDD... Comment améliorer ?
$aLinesAdded[] = "-- ========================================";
$aLinesAdded[] = "--#TITLE(Préparation de l'installation)";
$aLinesAdded[] = "--#STEP()";

$aLinesAdded[] = "CREATE TABLE INSTALL" .
				 " (" .
				 "  INSTALL_TYPE    VARCHAR(20) NOT NULL," .
				 "  CURRENT_VERSION VARCHAR(20) NOT NULL," .
				 "  INSTALL_VERSION VARCHAR(20) NOT NULL," .
				 "  SCRIPT_VERSION  VARCHAR(20) NOT NULL," .
				 "  SCRIPT_STEP     INT         NOT NULL" .
				 " ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$aLinesAdded[] = "INSERT INTO INSTALL" .
				 " (" .
				 "  INSTALL_TYPE," .
				 "  CURRENT_VERSION," .
				 "  INSTALL_VERSION," .
				 "  SCRIPT_VERSION," .
				 "  SCRIPT_STEP" .
				 " )" .
				 " VALUES" .
				 " (" .
					Database::prepareString(strtoupper($sAction))."," .
					Database::prepareString($sCurrentVersion)."," .
					Database::prepareString($sInstallVersion)."," .
					Database::prepareString($sScriptVersion)."," .
				 "  0" .
				 " );";

// TODO : Enlever l'ajout manuel de la colonne dès que l'on enlève la compatibilité avec la v3.4.0

// Si la version présente en BDD est inférieure à la v3.4.0_dev1
// alors la BDD ne contient pas la colonne FIRST_INSTALL_VERSION donc on test.

$aTemp = Database::fetchOneRow("SHOW COLUMNS FROM PARAMETRES LIKE 'FIRST_INSTALL_VERSION'");
if((strtoupper($sAction) == 'UPGRADE') && ($aTemp === false))
{

	$aLinesAdded[] = <<< _EOS_
		--#STEP()
		ALTER TABLE PARAMETRES
			ADD FIRST_INSTALL_VERSION	VARCHAR(20)	NOT NULL	 FIRST;

		--#STEP(TRANSACTION)
		UPDATE PARAMETRES
		SET FIRST_INSTALL_VERSION = CONCAT(VERSION, '_before');
_EOS_;
}
else
{
	// Il faut que le nombre de step soit constant pour que la reprise en cas d'erreur fonctionne
	// Donc ici, les steps qui ne servent à rien.

	$aLinesAdded[] = <<< _EOS_
		--#STEP()
		SELECT 1;

		--#STEP(TRANSACTION)
		SELECT 1;
_EOS_;
}
// Fin de l'ajout manuel

$aLinesAdded[] = $sFileContent;

// Initialisation des variables SQL
$sSqlInstallVersion = Database::prepareString($sInstallVersion);
$sSqlInstallUID = Database::prepareString(uniqid(mt_rand()."_", true));

$aLinesAdded[] = <<< _EOS_
	-- ========================================
	--#TITLE(Ecriture de la version)
	--#STEP(TRANSACTION)

	UPDATE PARAMETRES
	SET VERSION = {$sSqlInstallVersion};

	UPDATE PARAMETRES
	SET FIRST_INSTALL_VERSION = {$sSqlInstallVersion}
	WHERE FIRST_INSTALL_VERSION = '0.0.0';
_EOS_;

// TODO : Enlever le test de l'existence de la colonne dès que l'on enlève la compatibilité avec la v3.4.0

// Si la version présente en BDD est inférieure à la v3.4.0_dev1
// alors la BDD ne contient pas la colonne INSTALL_UID donc on test.

$aTemp = Database::fetchOneRow("SHOW COLUMNS FROM PARAMETRES LIKE 'INSTALL_UID'");
if($aTemp !== false)
{

	$aLinesAdded[] = <<< _EOS_
		--#STEP(TRANSACTION)
		UPDATE PARAMETRES
		SET INSTALL_UID = {$sSqlInstallUID}
		WHERE INSTALL_UID = 'none';
_EOS_;
}
else
{
	// Il faut que le nombre de step soit constant pour que la reprise en cas d'erreur fonctionne
	// Donc ici, les steps qui ne servent à rien.

	$aLinesAdded[] = <<< _EOS_
		--#STEP(TRANSACTION)
		SELECT 1;
_EOS_;
}
// Fin de l'ajout manuel

$aLinesAdded[] = <<< _EOS_
	-- ========================================
	--#TITLE(Fin de l'installation)
	--#STEP()

	DROP TABLE INSTALL;
_EOS_;

$sFileContent = implode("\n", $aLinesAdded);

// ===== Parse le fichier sql =====

$aInstallOperations = Install::parseSqlInstallContent($sFileContent);

$sFileContent = null;

// ===== Execution des requêtes du fichier sql =====

$aInstallResults = array();
$bDatabaseReady = true;

$nAllStepCount = 0;

// On initialise le tableau de résultat
foreach($aInstallOperations as $aOperation)
{
	$aInstallResults[] = array
	(
		'TITLE'        => $aOperation['OPE_TITLE'],		// Le titre
		'RESULT'       => null,							// Null / OK / KO
		'STATUS'       => 'NOT_DONE',					// L'état de l'opération (NOT_DONE, OK, FAILED, IGNORED)
		'STEP_COUNT'   => $aOperation['STEPS_COUNT'],	// Nombre total d'étapes dans l'opération
		'STEP_DONE'    => 0,							// Nombre d'étapes réalisées dans l'opération
		'STEP_IGNORED' => 0								// Nombre d'étapes réalisées dans l'opération
	);

	$nAllStepCount += $aOperation['STEPS_COUNT'];
}

// Si on utilise wamp sur une clef USB, les requêtes prennent du temps.
// Sous windows, le temps passé à l'excution de la requête est décompté du temps d'éxecution
// Donc pour windows, on rajoute 5 sec par requête au temps d'éxécution.
if(substr_compare(PHP_OS, "WIN", 0, 3, true) == 0)
{
	set_time_limit(ini_get('max_execution_time') + ($nAllStepCount * 5));
}

// On execute
foreach($aInstallOperations as $nOpekey => &$pOperation)
{
	// $pOperation is a pointer to a row of operations array

	$bOperationOk          = null;			// Null / OK / KO
	$sOperationStatus      = 'NOT_DONE';	// L'état de l'opération (NOT_DONE, OK, FAILED, IGNORED)
	$nOperationStepDone    = 0;				// Nombre d'étapes réalisées dans l'opération
	$nOperationStepIgnored = 0;				// Nombre d'étapes réalisées dans l'opération

	foreach($pOperation['STEPS'] as &$pStep)
	{
		// $pStep is a pointer to a row of steps array of current operation

		$bStepResult = true;
		$bStepForced = false; // Pour indiquer si l'éxecution de cette étape est forcée

		// N° de l'étape
		$nCurrentStep++;

		// Existe-il des étapes de type ALWAYS_RUN ou TRANSACTION ?
		$bStepOptionAlwaysRun   = (in_array('ALWAYS_RUN',  $pStep['OPTIONS']) == true) ? true : false;
		$bStepOptionTransaction = (in_array('TRANSACTION', $pStep['OPTIONS']) == true) ? true : false;

		// A t'on le droit de traiter cette étape ?
		if($nCurrentStep <= $nScriptStep)
		{
			// Etape à lancer tout le temps ?
			if($bStepOptionAlwaysRun == true)
			{
				$bStepForced = true;
			}
			else
			{
				$nOperationStepIgnored++;
				continue;
			}
		}

		// Doit-on faire une transaction ?
		if($bStepOptionTransaction == true)
		{
			$nRowCount = Database::execute("START TRANSACTION");
			if($nRowCount === false)
			{
				$bStepResult = false;
			}
		} // END début de transaction

		// On traite chaque requête séparemment
		foreach($pStep['QUERIES'] as $sQuery)
		{
			$nRowCount = Database::execute($sQuery);
			if($nRowCount === false)
			{
				$bStepResult = false;
				break;
			}
		} // END chaque requête

		// Une transaction est en cours ?
		if($bStepOptionTransaction == true)
		{
			$sQuery = ($bStepResult == true) ? "COMMIT" : "ROLLBACK";

			$nRowCount = Database::execute($sQuery);
			if($nRowCount === false)
			{
				$bStepResult = false;
			}
		} // END fin de transaction

		// On traite le résultat de l'étape
		if($bStepResult == true)
		{
			// Attention si cette étape est forcée => traitement spécial
			if($bStepForced == false)
			{
				$bOperationOk = true;
				$nScriptStep = $nCurrentStep;
				$nOperationStepDone++;
			}
			else
			{
				// On ne touche pas au n° de l'étape installé
				// car on fait une reprise et que cette étape est forcée
				$bOperationOk = true;
				$nOperationStepDone++;
			}
		}
		else
		{
			$bOperationOk = false;
			break; // fin des étapes.
		}

	} // END chaque étape

	// On stocke le résultat de l'opération
	if($bOperationOk === true)
	{
		$sOperationStatus = 'OK';
		$bInstallOk = true;
	}
	else if($bOperationOk === false)
	{
		$sOperationStatus = 'FAILED';
		$bInstallOk = false;
	}
	else // $bOperationOk === null
	{
		$sOperationStatus = ($nOperationStepIgnored > 0) ? 'IGNORED' : 'NOT_DONE';
	}

	$aInstallResults[$nOpekey]['RESULT']		= $bOperationOk;
	$aInstallResults[$nOpekey]['STATUS']		= $sOperationStatus;
	$aInstallResults[$nOpekey]['STEP_DONE']		= $nOperationStepDone;
	$aInstallResults[$nOpekey]['STEP_IGNORED']	= $nOperationStepIgnored;

	if($bInstallOk == false)
	{
		break; // Fin des étapes

	}

} // END chaque opération

// ===== Sauvegarde pour reprise =====

if($bInstallOk == false)
{
	$sQuery = "UPDATE INSTALL" .
			  " SET SCRIPT_STEP = {$nScriptStep}";

	Database::execute($sQuery);
}

// ===== On stocke les résultats en session =====

$_SESSION['PAGE_RESULTS'] = $aInstallResults;

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

// Rechargement
header("Location: ?step=4&mode=end&database_ready={$bInstallOk}");
return;
