<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

$objForm->read('CLASSE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"CLASSE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de la classe de l'&eacute;l&egrave;ve !");
$objForm->testError0(null, 'convert_int',	"L'id de la classe de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nClasseId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

		// supprime la classe
		$sQuery =
			"DELETE FROM CLASSES " .
			" WHERE CLASSE_ID = {$nClasseId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=classes");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=classes");
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
header("Location: ?page=classes");
return;
