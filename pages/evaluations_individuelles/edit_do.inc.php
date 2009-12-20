<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eval_ind_edit');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

$oForm->read('EVAL_IND_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"EVAL_IND_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la comp&eacute;tence individuelle !");
$oForm->testError0(null, 'convert_int',	"L'id de la comp&eacute;tence individuelle doit &ecirc;tre un entier !");
$nEvalIndId = $oForm->get(null);

$oForm->read('ID_NOTE', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"ID_NOTE\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la note !");
$oForm->testError0(null, 'convert_int',	"L'id de la note doit &ecirc;tre un entier !");
$nEvalIndNoteId = $oForm->get(null);

$oForm->read('ID_COMPETENCE', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"ID_COMPETENCE\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la comp&eacute;tence !");
$oForm->testError0(null, 'convert_int',	"L'id de la comp&eacute;tence doit &ecirc;tre un entier !");
$nEvalIndCompetenceId = $oForm->get(null);

// appreciation de l'eleve
$sEvalIndCommentaire = $oForm->getValue('EVAL_IND_COMMENTAIRE', $_POST, 'is_string', "");

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

		$sQuery =
			"UPDATE EVALUATIONS_INDIVIDUELLES " .
			" SET EVAL_IND_COMMENTAIRE = ".Database::prepareString($sEvalIndCommentaire) . "," .
			"     ID_COMPETENCE = {$nEvalIndCompetenceId}, " .
			"     ID_NOTE = {$nEvalIndNoteId}".
			" WHERE EVAL_IND_ID = {$nEvalIndId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=evaluations_individuelles");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=evaluations_individuelles");
		return;
	break;

	// ----------
	default:
		$oForm->clearError();

		Message::addError("L'action \"{$sAction}\" est inconnue !");
}

//==============================================================================
// Traitement des donnees
//==============================================================================

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

// On stocke toutes les erreurs de formulaire.
Message::addErrorFromFormValidation($oForm->getError());

// Rechargement
header("Location: ?page=evaluations_individuelles&mode=edit&eval_ind_id={$nEvalIndId}");
return;
