<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

$objForm->read('EVAL_IND_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"EVAL_IND_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de la comp&eacute;tence individuelle !");
$objForm->testError0(null, 'convert_int',	"L'id de la comp&eacute;tence individuelle doit &ecirc;tre un entier !");
$nEvalIndId = $objForm->get(null);

$objForm->read('ID_NOTE', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"ID_NOTE\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de la note !");
$objForm->testError0(null, 'convert_int',	"L'id de la note doit &ecirc;tre un entier !");
$nEvalIndNoteId = $objForm->get(null);

$objForm->read('ID_COMPETENCE', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"ID_COMPETENCE\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de la comp&eacute;tence !");
$objForm->testError0(null, 'convert_int',	"L'id de la comp&eacute;tence doit &ecirc;tre un entier !");
$nEvalIndCompetenceId = $objForm->get(null);

// appreciation de l'eleve
$sEvalIndCommentaire = $objForm->getValue('EVAL_IND_COMMENTAIRE', $_POST, 'is_string', "");

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($objForm->hasError() == true) break;

		$sQuery =
			"UPDATE EVALUATIONS_INDIVIDUELLES " .
			" SET EVAL_IND_COMMENTAIRE = ".Database::prepareString($sEvalIndCommentaire) . "," .
			"     ID_COMPETENCE = {$nEvalIndCompetenceId}, " . 
			"     ID_NOTE = {$nEvalIndNoteId}".
			" WHERE EVAL_IND_ID = {$nEvalIndId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=evaluations_individuelles&mode=edit&eval_ind_id={$nEvalIndId}");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();
		
		// Rechargement
		header("Location: ?page=evaluations_individuelles");
		return;
	break;

	// ----------
	default:
		$objForm->clearError();

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
Message::addErrorFromFormValidation($objForm->getError());

// Rechargement
header("Location: ?page=evaluations_individuelles&mode=edit&eval_ind_id={$nEvalIndId}");
return;

?>
