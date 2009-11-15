<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// niveau a mettre a jour
$objForm->read('NIVEAU_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"NIVEAU_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id du niveau !");
$objForm->testError0(null, 'convert_int',	"L'id du niveau doit &ecirc;tre un entier !");
$nNiveauId = $objForm->get(null);

// nom du niveau
$objForm->read('NIVEAU_NOM', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ NIVEAU_NOM !");
$objForm->testError0(null, 'blank',		"Il manque le nom du niveau !");
$objForm->testError0(null, 'is_string',	"Le nom du niveau &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sNiveauNom = $objForm->get(null);

// cycle
$objForm->read('ID_CYCLE', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ ID_CYCLE !");
$objForm->testError0(null, 'blank',	"Il manque le cycle du domaine !");
$objForm->testError0(null, 'is_int',"L'id du cycle doit &ecirc;tre un entier!");
$nIdCycle = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($objForm->hasError() == true) break;

		$sQuery =
			"UPDATE NIVEAUX" .
			" SET NIVEAU_NOM = ".Database::prepareString($sNiveauNom) . "," .
			"     ID_CYCLE = {$nIdCycle}".
			" WHERE NIVEAU_ID = {$nNiveauId}";
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
header("Location: ?page=niveaux&mode=edit&niveau_id={$nNiveauId}");
return;
