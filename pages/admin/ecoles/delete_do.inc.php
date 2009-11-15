<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

$objForm->read('ECOLE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"ECOLE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de l'&eacute;cole !");
$objForm->testError0(null, 'convert_int',	"L'id de l'&eacute;cole doit &ecirc;tre un entier !");
$nEcoleId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

		// supprime l'ecole
		$sQuery =
			"DELETE FROM ECOLES " .
			" WHERE ECOLE_ID = {$nEcoleId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=ecoles");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=ecoles");
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
header("Location: ?page=ecoles");
return;
