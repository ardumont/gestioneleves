<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

$oForm->read('MATIERE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"MATIERE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la mati&egrave;re !");
$oForm->testError0(null, 'convert_int',	"L'id de la mati&egrave;re doit &ecirc;tre un entier !");
$nMatiereId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($oForm->hasError() == true) break;

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
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=matieres");
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
header("Location: ?page=matieres");
return;
