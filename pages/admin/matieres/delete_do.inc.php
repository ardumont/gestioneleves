<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

$objForm->read('MATIERE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"MATIERE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de la mati&egrave;re !");
$objForm->testError0(null, 'convert_int',	"L'id de la mati&egrave;re doit &ecirc;tre un entier !");
$nMatiereId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

		// supprime la matiere
		$sQuery =
			"DELETE FROM MATIERES " .
			" WHERE MATIERE_ID = {$nMatiereId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=matieres");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=matieres");
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
header("Location: ?page=matieres");
return;
