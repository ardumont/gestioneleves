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
$objForm->testError0(null, 'blank',	"Il manque le cycle du domaine !");
$objForm->testError0(null, 'is_int',"L'id du cycle doit &ecirc;tre un entier!");
$nIdCycle = $objForm->get(null);

// nom de la competence
$objForm->read('DOMAINE_NOM', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ DOMAINE_NOM !");
$objForm->testError0(null, 'blank',		"Il manque le nom du domaine !");
$objForm->testError0(null, 'is_string',	"Le nom du domaine &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sDomaineNom = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($objForm->hasError() == true) break;
		// insertion du domaine dans la table
		$sQuery =
			"INSERT INTO DOMAINES (DOMAINE_NOM, ID_CYCLE)" .
			"VALUES(" .
				Database::prepareString($sDomaineNom) . "," .
				$nIdCycle .
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=domaines&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=domaines&mode=add");
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
header("Location: ?page=domaines&mode=add");
return;
