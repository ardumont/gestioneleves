<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// cycle a supprimer
$oForm->read('CYCLE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"CYCLE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id du cycle !");
$oForm->testError0(null, 'convert_int',	"L'id du cycle doit &ecirc;tre un entier !");
$nCycleId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($oForm->hasError() == true) break;

		// supprime le cycle
		$sQuery =
			"DELETE FROM CYCLES " .
			" WHERE CYCLE_ID = {$nCycleId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=cycles");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=cycles");
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
header("Location: ?page=cycles");
return;
