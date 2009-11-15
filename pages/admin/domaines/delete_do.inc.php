<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

$objForm->read('DOMAINE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"DOMAINE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id du domaine !");
$objForm->testError0(null, 'convert_int',	"L'id du domaine doit &ecirc;tre un entier !");
$nDomaineId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

		// supprime le domaine
		$sQuery =
			"DELETE FROM DOMAINES " .
			" WHERE DOMAINE_ID = {$nDomaineId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=domaines");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=domaines");
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
header("Location: ?page=domaines");
return;
