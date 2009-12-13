<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

//$bHasRight = ProfilManager::hasRight('project_delete');
//if($bHasRight == false)
//{
//	// Redirection
//	header("Location: ?page=no_rights");
//	return;
//}

//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");
$sRetour = $oForm->getValue('retour', $_POST, 'is_string', "");

$aEvalIndsToDel = isset($_POST['evals_inds_id']) && $_POST['evals_inds_id'] != false ? $_POST['evals_inds_id'] : array();

if($aEvalIndsToDel == false)
{
	$oForm->setError('evals_inds_id', 'liste', "La liste des évaluations individuelles doit être remplit avec au moins une entrée.");
}

//==============================================================================
// Actions du formulaire
//==============================================================================

$sPageReturn = "?page=evaluations_individuelles" . (($sRetour != "") ? "&mode={$sRetour}" : "");

switch(strtolower($sAction))
{
	case 'supprimer':
		if($oForm->hasError() == true) break;

		if(count($aEvalIndsToDel) > 0)
		{
			$sQueryEvalToDel = implode(",", $aEvalIndsToDel);

			$sQuery = <<< ____________EOQ
				DELETE FROM EVALUATIONS_INDIVIDUELLES
				WHERE EVAL_IND_ID IN ({$sQueryEvalToDel})
____________EOQ;
			Database::execute($sQuery);
		}

		// Rechargement
		header("Location: {$sPageReturn}");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: {$sPageReturn}");
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
header("Location: {$sPageReturn}");
return;
