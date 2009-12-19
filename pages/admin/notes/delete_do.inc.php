<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// la note a supprimer
$oForm->read('NOTE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"NOTE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la note !");
$oForm->testError0(null, 'convert_int',	"L'id de la note doit &ecirc;tre un entier !");
$nNoteId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($oForm->hasError() == true) break;

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
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=notes");
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
header("Location: ?page=notes");
return;
