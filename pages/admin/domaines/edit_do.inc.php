<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// domaine a mettre a jour
$objForm->read('DOMAINE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"DOMAINE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id du domaine !");
$objForm->testError0(null, 'convert_int',	"L'id du domaine doit &ecirc;tre un entier !");
$nDomaineId = $objForm->get(null);

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
	// ----------
	case 'modifier':
		if($objForm->hasError() == true) break;

		$sQuery =
			"UPDATE DOMAINES" .
			" SET DOMAINE_NOM = ".Database::prepareString($sDomaineNom) . "," .
			"     ID_CYCLE = {$nIdCycle}".
			" WHERE DOMAINE_ID = {$nDomaineId}";
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
header("Location: ?page=domaines&mode=edit&domaine_id={$nDomaineId}");
return;
