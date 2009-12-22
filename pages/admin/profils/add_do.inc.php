<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('admin_profil_add');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

$oForm->read('profil_name', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le nom du profil !");
$oForm->testError0(null, 'blank',     "Il manque le nom du profil !");
$oForm->testError0(null, 'is_string', "Le nom du profil doit être une chaîne de caractères !");
$sName = $oForm->get(null);

$oForm->read('profil_description', $_POST);
$oForm->testValue0(null, 'exist', null);
$oForm->testValue0(null, 'blank', null);
$oForm->testError0(null, 'is_string', "La description du profil doit être une chaîne de caractères !");
$sDescription = $oForm->get(null);

$oForm->read('profil_rights', $_POST);
$oForm->testValue0(null, 'exist', array());
$oForm->testValue0(null, 'blank', array());
$oForm->testError0(null, 'is_array', "La liste des droits du profil doit être une liste !");
$aRights = $oForm->get(null);

// ===== Vérification des valeurs =====

//==============================================================================
// Actions du formulaire
//==============================================================================

switch(strtoupper($sAction))
{
	case 'VALIDER':
		if($oForm->hasError() == true) break;

		// Initialisation des variables SQL
		$sSqlName = Database::prepareString($sName);
		$sSqlDescription = Database::prepareString($sDescription);

		$sQuery = <<< _EOQ_
			INSERT INTO PROFILS
			(
				PROFIL_ID,
				PROFIL_NAME,
				PROFIL_COMMENT
			)
			VALUES
			(
				NULL,
				{$sSqlName},
				{$sSqlDescription}
			)
_EOQ_;

		$nNbRow = Database::execute($sQuery);

		if($nNbRow !== false)
		{
			$sQuery = <<< _EOQ_
				SELECT LAST_INSERT_ID();
_EOQ_;

			$nProfilId = Database::fetchOneValue($sQuery);

			$aQueryValue = array();

			foreach($aRights as $sRight => $dummy)
			{
				// Initialisation des variables SQL
				$sSqlRight = Database::prepareString($sRight);

				$aQueryValue[] = "({$nProfilId},{$sSqlRight})";

			} // END foreach($aRights as $sRight)

			// Initialisation des variables SQL
			$sSqlQueryValue = implode(",", $aQueryValue);

			$sQuery = <<< _EOQ_
				INSERT INTO PROFILS_REL_RIGHTS
				(
					PROFIL_ID,
					PROFIL_RIGHT
				)
				VALUES
					{$sSqlQueryValue}
_EOQ_;

			if(count($aQueryValue) > 0)
			{
				Database::execute($sQuery);
			}

		} // END if INSERT PROFIL is ok

		// Rechargement
		header("Location: ?page=profils");
		return;
	break;

	// ----------
	case 'ANNULER':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=profils");
		return;
	break;

	// ----------
	default:
		$oForm->clearError();

		Message::addError("L'action \"{$sAction}\" est inconnue !");
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

// On stocke toutes les erreurs de formulaire.
Message::addErrorFromFormValidation($oForm->getError());

// Rechargement
header("Location: ?page=profils&mode=add");
return;
