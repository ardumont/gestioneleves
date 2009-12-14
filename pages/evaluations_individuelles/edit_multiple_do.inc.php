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

$oForm->read('id_note', $_POST);
$oForm->testError0(null, 'exist', "Il manque le champ id_note !");
$oForm->testError0(null, 'blank', "Il manque l'id de la note !");
$oForm->testError0(null, 'convert_int',	"L'id de la note doit &ecirc;tre un entier !");
$nNoteId = $oForm->get(null, -1);

$aEvalIndsToDel = isset($_POST['evals_inds_id']) && $_POST['evals_inds_id'] != false ? $_POST['evals_inds_id'] : array();
if($aEvalIndsToDel == false)
{
	$oForm->setError('evals_inds_id', 'liste', "La liste des évaluations individuelles doit être remplit avec au moins une entrée.");
}

if($nNoteId != -1)
{
	// ===== La liste des notes =====
	$sQuery = <<< ____EOQ
		SELECT
			1 EXIST
		FROM NOTES
		WHERE NOTE_ID = {$nNoteId}
____EOQ;

	$oForm->readArray('query1', Database::fetchOneRow($sQuery));
	$oForm->testError0('query1.EXIST', 'exist', "L'identifiant de la note \"{$nNoteId}\" n'est pas valide !");
}

//==============================================================================
// Actions du formulaire
//==============================================================================

$sPageReturn = "?page=evaluations_individuelles" . (($sRetour != "") ? "&mode={$sRetour}" : "");

switch(strtolower($sAction))
{
	case 'editer':
		if($oForm->hasError() == true) break;

		$sQueryEvalToDel = implode(",", $aEvalIndsToDel);

		if($nNoteId != -1)
		{
			$sQuery = <<< ____________EOQ
				UPDATE EVALUATIONS_INDIVIDUELLES
				SET ID_NOTE = {$nNoteId}
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
