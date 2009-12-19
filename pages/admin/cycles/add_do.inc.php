<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// nom du cycle
$oForm->read('CYCLE_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ CYCLE_NOM !");
$oForm->testError0(null, 'blank',     "Il manque le nom du cycle !");
$oForm->testError0(null, 'is_string', "Le nom du cycle doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sCycleNom = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	case 'ajouter':
		if($oForm->hasError() == true) break;

		// insertion du nouveau cycle
		$sQuery =
			"INSERT INTO CYCLES(CYCLE_NOM) " .
			"VALUE(" .
				Database::prepareString($sCycleNom) .
			")";
		Database::execute($sQuery);

		// rechargement de la liste
		header("Location: ?page=cycles&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=cycles&mode=add");
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
header("Location: ?page=cycles&mode=add");
return;
