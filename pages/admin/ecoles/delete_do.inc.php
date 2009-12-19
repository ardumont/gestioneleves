<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

$oForm->read('ECOLE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"ECOLE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de l'&eacute;cole !");
$oForm->testError0(null, 'convert_int',	"L'id de l'&eacute;cole doit &ecirc;tre un entier !");
$nEcoleId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($oForm->hasError() == true) break;

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
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=ecoles");
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
header("Location: ?page=ecoles");
return;
