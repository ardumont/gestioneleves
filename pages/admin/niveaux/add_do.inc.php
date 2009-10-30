<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// matiere de la competence
$objForm->read('ID_CYCLE', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ ID_CYCLE !");
$objForm->testError0(null, 'blank',	"Il manque l'id du cycle !");
$objForm->testError0(null, 'is_int',"L'id du cycle doit &ecirc;tre un entier!");
$nIdCycle = $objForm->get(null);

// nom du niveau
$objForm->read('NIVEAU_NOM', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ NIVEAU_NOM !");
$objForm->testError0(null, 'blank',		"Il manque le nom du niveau !");
$objForm->testError0(null, 'is_string',	"Le nom du niveau doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sNiveauNom = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	case 'ajouter':
		if($objForm->hasError() == true) break;

		// insertion du niveau		
		$sQuery =
			"INSERT INTO NIVEAUX(NIVEAU_NOM, ID_CYCLE)" .
			"VALUES(" .
				Database::prepareString($sNiveauNom) . "," . 
				$nIdCycle . 
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=niveaux&mode=add");
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
header("Location: ?page=niveaux&mode=add");
return;

?>
