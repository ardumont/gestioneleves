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
$objForm->testError0(null, 'blank',			"Il manque l'id de l'&eacute;valuation individuelle !");
$objForm->testError0(null, 'convert_int',	"L'id de l'&eacute;valuation individuelle doit &ecirc;tre un entier !");
$nEvalIndId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

		$sQuery =
			"DELETE FROM EVALUATIONS_INDIVIDUELLES " .
			" WHERE EVAL_IND_ID = {$nEvalIndId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=evaluations_individuelles");
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
header("Location: ?page=evaluations_individuelles");
return;
