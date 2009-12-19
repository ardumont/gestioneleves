<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// domaine a mettre a jour
$oForm->read('DOMAINE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"DOMAINE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id du domaine !");
$oForm->testError0(null, 'convert_int',	"L'id du domaine doit &ecirc;tre un entier !");
$nDomaineId = $oForm->get(null);

// matiere de la competence
$oForm->read('ID_CYCLE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ ID_CYCLE !");
$oForm->testError0(null, 'blank',	"Il manque le cycle du domaine !");
$oForm->testError0(null, 'is_int',"L'id du cycle doit &ecirc;tre un entier!");
$nIdCycle = $oForm->get(null);

// nom de la competence
$oForm->read('DOMAINE_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ DOMAINE_NOM !");
$oForm->testError0(null, 'blank',		"Il manque le nom du domaine !");
$oForm->testError0(null, 'is_string',	"Le nom du domaine &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sDomaineNom = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

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
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=domaines");
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
header("Location: ?page=domaines&mode=edit&domaine_id={$nDomaineId}");
return;
