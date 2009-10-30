<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// nom du cycle
$objForm->read('CYCLE_NOM', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ CYCLE_NOM !");
$objForm->testError0(null, 'blank',     "Il manque le nom du cycle !");
$objForm->testError0(null, 'is_string', "Le nom du cycle doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sCycleNom = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	case 'ajouter':
		if($objForm->hasError() == true) break;

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
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=cycles&mode=add");
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
header("Location: ?page=cycles&mode=add");
return;

?>
