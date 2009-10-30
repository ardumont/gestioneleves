<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// la note a supprimer
$objForm->read('NOTE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"NOTE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de la note !");
$objForm->testError0(null, 'convert_int',	"L'id de la note doit &ecirc;tre un entier !");
$nNoteId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

		// supprime la periode
		$sQuery =
			"DELETE FROM NOTES " .
			" WHERE NOTE_ID = {$nNoteId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=notes");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=notes");
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
header("Location: ?page=notes");
return;
?>
