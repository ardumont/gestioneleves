<?php
//==============================================================================
// Préparation des données
//==============================================================================

$sConfigFileName       = PATH_CONF_INSTALL."/main.conf.php";
$sSampleConfigFileName = PATH_CONF_INSTALL."/main.sample.conf.php";

//==============================================================================
// Validation du formulaire
//==============================================================================

$sAction = "";

if(array_key_exists('action', $_POST) == true)
{
	if(is_string($_POST['action']))
	{
		$sAction = (string)$_POST['action'];
	}
}

$sPathRoot = "";

if(array_key_exists('path_root', $_POST) == true)
{
	if(is_string($_POST['path_root']))
	{
		$sPathRoot = (string)$_POST['path_root'];
	}
}

$sUrlRoot = "";

if(array_key_exists('url_root', $_POST) == true)
{
	if(is_string($_POST['url_root']))
	{
		$sUrlRoot = (string)$_POST['url_root'];
	}
}

// ===== Vérification des valeurs =====

//==============================================================================
// Actions du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'suivant':
		// ===== Ecriture du fichier de configuration =====
		
		$bConfigFileReady = copy($sSampleConfigFileName, $sConfigFileName);
		
		if($bConfigFileReady == true)
		{
			$sFileContent = file_get_contents($sConfigFileName);
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
		header("Location: ?step=2");
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
header("Location: ?step=2&mode=end&ready={$bConfigFileReady}");
return;

?>