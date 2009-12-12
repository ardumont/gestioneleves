<?php
/**
 * Classe du gestionnaire d'installation de l'application.
 *
 * @author Lionel SAURON
 * @version 1.0.0_dev0
 */
class Install
{
	/**
	 *
	 */
	public static function getAllVersions($sInstallPath)
	{
		// Version with SQL script
		$aDirContent = glob("{$sInstallPath}/*", GLOB_ONLYDIR);

		// Keep only the filename
		$aDirContent = array_map('basename', $aDirContent);

		// Version without script
		$sFileContent = file_get_contents("{$sInstallPath}/version.txt");

		$aFileContent = explode("\n", $sFileContent);

		// Merge data
		$aAllVersion = array_merge($aDirContent, $aFileContent);

		// Check name ('v' and 1 digit minimum) and ...
		$aAllVersion = preg_grep('/^\s*v(\d[\d\.-_\w]*)\s*$/', $aAllVersion);

		// ...and keep only version number
		$fctNumVersion = create_function('$sVersion', 'return substr(trim($sVersion), 1);');

		$aAllVersion = array_map($fctNumVersion, $aAllVersion);

		// Only unique value
		$aAllVersion = array_unique($aAllVersion);

		// Sort version with version_compare
		usort($aAllVersion, 'version_compare');

		return $aAllVersion;
	}

	/**
	 *
	 */
	public static function filterLowHightVersions($aVersions, $sLowVersion, $bLowStrict, $sHighVersion, $bHighStrict)
	{
		$aFilteredVersions = $aVersions;

		// Kep versions higher than low version
		if($sLowVersion !== null)
		{
			$sTest = ($bLowStrict == true) ? ">" : ">=";

			$fctFilterVersion = create_function('$sVersion', 'return version_compare($sVersion, "'.$sLowVersion.'", "'.$sTest.'");');

			$aFilteredVersions = array_filter($aFilteredVersions, $fctFilterVersion);
		}

		//Kep versions lower than high version
		if($sHighVersion !== null)
		{
			$sTest = ($bHighStrict == true) ? "<" : "<=";

			$fctFilterVersion = create_function('$sVersion', 'return version_compare($sVersion, "'.$sHighVersion.'", "'.$sTest.'");');

			$aFilteredVersions = array_filter($aFilteredVersions, $fctFilterVersion);
		}

		return $aFilteredVersions;
	}

	/**
	 * Check if the version is a release or a dev one.
	 * Release version only contain number and dot.
	 *
	 * @author Lionel SAURON
	 * @public
	 *
	 * @param $sVersion(string) Version number.
	 * @return <code>true</code>, <code>false</code>.
	 */
	public static function checkReleaseVersion($sVersion)
	{
		$nCount = preg_match('/^[\d\.]+$/', $sVersion);

		return ($nCount == 1);
	}

	/**
	 *
	 */
	public static function filterReleaseVersions($aVersions, $bKeepDevStart, $bKeepDevMiddle, $bKeepDevEnd)
	{
		$aOriginalVersions = $aVersions;
		$aFilteredVersions = array();

		$aVersionsStartOk  = array();
		$aVersionsMiddleOk = array();
		$sVersionsEndOk    = array();

		// ===== Beginning =====
		do
		{
			// Take first version
			$sVersion = array_shift($aVersions);

			if($sVersion === null) break;

			// Check it
			$bReleaseVersion = self::checkReleaseVersion($sVersion);

			// Keep version only if (it's a dev version and we need it) or if it's a release version
			if(($bReleaseVersion == false) && ($bKeepDevStart == true) || ($bReleaseVersion == true))
			{
				array_push($aVersionsStartOk, $sVersion);
			}

		} // "Beginning" stop when we find a release version or at the end of list
		while(($bReleaseVersion === false) && (count($aVersions) > 0));

		// ===== End =====

		do
		{
			// Take last version
			$sVersion = array_pop($aVersions);

			if($sVersion === null) break;

			// Check it
			$bReleaseVersion = self::checkReleaseVersion($sVersion);

			// Keep version only if (it's a dev version and we need it) or if it's a release version
			if(($bReleaseVersion == false) && ($bKeepDevEnd == true) || ($bReleaseVersion == true))
			{
				array_unshift($sVersionsEndOk, $sVersion);
			}

		} // "End" stop when we find a release version or at the end of list
		while(($bReleaseVersion === false) && (count($aVersions) > 0));

		// ===== Middle =====

		// Remove dev version in the rest of the list except if we want dev versions
		if($bKeepDevMiddle == false)
		{
			$aVersionsMiddleOk = array_filter($aVersions, array('self', 'checkReleaseVersion'));
		}

		// ===== Merge parts =====

		$aFilteredVersions = array_merge($aVersionsStartOk, $aVersionsMiddleOk, $sVersionsEndOk);

		// ===== Special case =====

		// If there is no version at the end, and if we need to keep one dev version
		// Then original version's array are full of dev version. So return it.
		if((count($aFilteredVersions) == 0) && (($bKeepDevStart == true) || ($bKeepDevMiddle == true) || ($bKeepDevEnd == true)))
		{
			$aFilteredVersions = $aOriginalVersions;
		}

		return $aFilteredVersions;
	}

	/**
	 * Parse a sql file to extract operations/steps/queries
	 *
	 * @author Lionel SAURON
	 * @public
	 *
	 * @param $sSqlInstallContent(string) SQL script.
	 * @return
	 */
	public static function parseSqlInstallContent($sSqlInstallContent)
	{
		$aInstallOperations = array();

		// ===== Nettoyage =====

		// On supprime tout les commentaires de type "-- ..." (on match sur le début et fin de ligne)
		$sSqlInstallContent = preg_replace('/^\s*-- .*$/m', '', $sSqlInstallContent);
		$sSqlInstallContent = trim($sSqlInstallContent);

		// ===== Recherche des opérations =====

		// On cherche les titres des opérations à effectuer => "--#TITLE(...)" (on match sur le début de ligne)
		$aMatches = array();
		$nOperationsCount = preg_match_all('/^\s*--#TITLE\(([^)]*)\)/m', $sSqlInstallContent, $aMatches, PREG_SET_ORDER);

		// On sépare les opérations en se basant sur le tag
		$aOperations = preg_split('/^\s*--#TITLE.*$/m', $sSqlInstallContent, -1, PREG_SPLIT_NO_EMPTY);

		// Vérification. Chaque opération doit avoir un titre.
		if($nOperationsCount != count($aOperations))
		{
			$aInstallOperations['ERROR'] = "INVALID_OPERATIONS_COUNT";
			return $aInstallOperations;
		}

		// On stocke chaque operation
		for($i = 0; $i < $nOperationsCount; $i++)
		{
			$aInstallOperations[] = array
			(
				'OPE_TITLE'		=> trim($aMatches[$i][1]),	// Operation's title
				'STEPS_COUNT'	=> 0,						// Steps count
				'STEPS'			=> array(),					// Steps parsed
				'RAW_OPE'		=> trim($aOperations[$i]),	// Raw operation content not yet parsed
			);
		} // END for($i = 0; $i <= $nOperationsCount; $i++)

		// ===== Traitement de chaque opération =====

		foreach($aInstallOperations as &$pOperation)
		{
			// $pOperation is a pointer to a row of operations array

			// On cherche les particularités des étapes => "--#STEP(...)" (on match sur le début de ligne)
			$aMatches = array();
			$nStepsCount = preg_match_all('/^\s*--#STEP\(([^)]*)\)/m', $pOperation['RAW_OPE'], $aMatches, PREG_SET_ORDER);

			// On sépare les étapes en se basant sur le tag
			$aSteps = preg_split('/^\s*--#STEP.*$/m', $pOperation['RAW_OPE'], -1, PREG_SPLIT_NO_EMPTY);

			// Vérification.
			if($nStepsCount != count($aSteps))
			{
				$aInstallOperations['ERROR'] = "INVALID_STEPS_COUNT";
				return $aInstallOperations;
			}

			// On stocke
			$pOperation['STEPS_COUNT'] = $nStepsCount;
			unset($pOperation['RAW_OPE']); // Clean raw data

			for($i = 0; $i < $nStepsCount; $i++)
			{
				// Les options de l'étape
				$aStepOptions = array();

				if(trim($aMatches[$i][1]) != "")
				{
					$aStepOptions = explode(',', trim($aMatches[$i][1]));
					$aStepOptions = array_map('trim', $aStepOptions);
				}

				$pOperation['STEPS'][] = array
				(
					'OPTIONS'		=> $aStepOptions,		// Step's option
					'QUERIES_COUNT'	=> 0,					// Queries count
					'QUERIES'		=> array(),				// Queries parsed
					'RAW_STEP'		=> trim($aSteps[$i]),	// Raw operation content not yet parsed
				);
			} // END for($i = 0; $i <= $nStepsCount; $i++)

		} // END chaque opération

		// ===== Traitement de chaque step dans chaque operation =====

		foreach($aInstallOperations as &$pOperation)
		{
			// $pOperation is a pointer to a row of operations array

			foreach($pOperation['STEPS'] as &$pStep)
			{
				// $pStep is a pointer to a row of steps array of current operation

				// On cherche les requêtes (elles se terminent par ";")
				$aQueries = preg_split('/;\s*$/m', $pStep['RAW_STEP'], -1, PREG_SPLIT_NO_EMPTY);

				// On stocke
				$pStep['QUERIES_COUNT'] = count($aQueries);
				$pStep['QUERIES'] = array_map('trim', $aQueries);
				unset($pStep['RAW_STEP']); // Clean raw data

			} // END chaque étape

		} // END chaque opération

		return $aInstallOperations;
	}
}
?>