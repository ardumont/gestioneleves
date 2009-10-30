<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// niveau a supprimer
$objForm->read('NIVEAU_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"NIVEAU_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id du niveau !");
$objForm->testError0(null, 'convert_int',	"L'id du niveau doit &ecirc;tre un entier !");
$nNiveauId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

		// supprime le niveau
		$sQuery =
			"DELETE FROM NIVEAUX " .
			" WHERE NIVEAU_ID = {$nNiveauId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=niveaux");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=niveaux");
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
header("Location: ?page=niveaux");
return;
?>
