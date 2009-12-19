<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// la periode a supprimer
$oForm->read('PERIODE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"PERIODE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la p&eacute;riode !");
$oForm->testError0(null, 'convert_int',	"L'id de la p&eacute;riode doit &ecirc;tre un entier !");
$nPeriodeId = $oForm->get(null);

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
			"DELETE FROM PERIODES " .
			" WHERE PERIODE_ID = {$nPeriodeId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=periodes");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=periodes");
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
header("Location: ?page=periodes");
return;
