<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// cycle a supprimer
$objForm->read('CYCLE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"CYCLE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id du cycle !");
$objForm->testError0(null, 'convert_int',	"L'id du cycle doit &ecirc;tre un entier !");
$nCycleId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

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
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=cycles");
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
header("Location: ?page=cycles");
return;
?>
