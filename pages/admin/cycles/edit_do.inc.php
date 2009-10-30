<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// cycle a mettre a jour
$objForm->read('CYCLE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"CYCLE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id du cycle !");
$objForm->testError0(null, 'convert_int',	"L'id du cycle doit &ecirc;tre un entier !");
$nCycleId = $objForm->get(null);

// nom du cycle
$objForm->read('CYCLE_NOM', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ \"CYCLE_NOM\" !");
$objForm->testError0(null, 'blank',     "Il manque le nom du cycle !");
$objForm->testError0(null, 'is_string', "Le nom du cycle doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sCycleNom = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($objForm->hasError() == true) break;

		// mise a jour du cycle
		$sQuery =
			"UPDATE CYCLES" .
			" SET CYCLE_NOM = ".Database::prepareString($sCycleNom) . 
			" WHERE CYCLE_ID = {$nCycleId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=cycles&mode=edit&cycle_id={$nCycleId}");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();
		
		// Rechargement
		header("Location: ?page=cycles");
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
header("Location: ?page=cycles&mode=edit&cycle_id={$nCycleId}");
return;

?>
