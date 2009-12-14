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
$sRetour = $oForm->getValue('fin_lien_retour', $_POST, 'convert_string', "");
$sRetour .= $oForm->getValue('retour', $_POST, 'convert_string', "");

// Id de la nouvelle note de la liste des evaluations individuelles
$nNoteId = $oForm->getValue('id_note', $_POST, 'convert_int', -1);
// Id de l'evaluation collective a laquelle l'evaluation individuelle est rattachee
$nEvalColId = $oForm->getValue('id_eval_col', $_POST, 'convert_int', -1);

$aEvalIndsToDel = isset($_POST['evals_inds_id']) && $_POST['evals_inds_id'] != false ? $_POST['evals_inds_id'] : array();
if($aEvalIndsToDel == false)
{
	$oForm->setError('evals_inds_id', 'liste', "La liste des évaluations individuelles doit être remplit avec au moins une entrée.");
}

if($nNoteId != -1)
{
	$sQuery = <<< ____EOQ
		SELECT
			1 EXIST
		FROM NOTES
		WHERE NOTE_ID = {$nNoteId}
____EOQ;

	$oForm->readArray('query1', Database::fetchOneRow($sQuery));
	$oForm->testError0('query1.EXIST', 'exist', "L'identifiant de la note \"{$nNoteId}\" n'est pas valide !");
}

if($nEvalColId != -1)
{
	$sQuery = <<< ____EOQ
		SELECT
			1 EXIST
		FROM EVALUATIONS_COLLECTIVES
		WHERE EVAL_COL_ID = {$nEvalColId}
____EOQ;

	$oForm->readArray('query2', Database::fetchOneRow($sQuery));
	$oForm->testError0('query2.EXIST', 'exist', "L'identifiant de l'évaluation collective \"{$nEvalColId}\" n'est pas valide !");
}

//==============================================================================
// Actions du formulaire
//==============================================================================

// Calcule la page de retour
$sPageReturn = "?page=evaluations_individuelles" . (($sRetour != "") ? "&mode={$sRetour}" : "");

switch(strtolower($sAction))
{
	case 'editer':
		if($oForm->hasError() == true) break;

		// Récupère la liste des ids des evaluations individuelles à mettre à jour
		$sQueryEvalToDel = implode(",", $aEvalIndsToDel);

		// Si la note est spécifiée
		if($nNoteId != -1)
		{
			$sQuery = <<< ____________EOQ
				UPDATE EVALUATIONS_INDIVIDUELLES
				SET ID_NOTE = {$nNoteId}
				WHERE EVAL_IND_ID IN ({$sQueryEvalToDel})
____________EOQ;
			Database::execute($sQuery);
		}
		
		// Si l'évaluation collective est spécifiée
		if($nEvalColId != -1)
		{
			$sQuery = <<< ____________EOQ
				UPDATE EVALUATIONS_INDIVIDUELLES
				SET ID_EVAL_COL = {$nEvalColId}
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
