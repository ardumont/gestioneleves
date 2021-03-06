<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('niveau_edit');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// niveau a mettre a jour
$oForm->read('NIVEAU_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"NIVEAU_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id du niveau !");
$oForm->testError0(null, 'convert_int',	"L'id du niveau doit &ecirc;tre un entier !");
$nNiveauId = $oForm->get(null);

// nom du niveau
$oForm->read('NIVEAU_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ NIVEAU_NOM !");
$oForm->testError0(null, 'blank',		"Il manque le nom du niveau !");
$oForm->testError0(null, 'is_string',	"Le nom du niveau &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sNiveauNom = $oForm->get(null);

// cycle
$oForm->read('ID_CYCLE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ ID_CYCLE !");
$oForm->testError0(null, 'blank',	"Il manque le cycle du domaine !");
$oForm->testError0(null, 'is_int',"L'id du cycle doit &ecirc;tre un entier!");
$nIdCycle = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

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
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=niveaux");
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
header("Location: ?page=niveaux&mode=edit&niveau_id={$nNiveauId}");
return;
