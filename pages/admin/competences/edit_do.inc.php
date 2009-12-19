<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

$oForm->read('COMPETENCE_ID', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ \"COMPETENCE_ID\" !");
$oForm->testError0(null, 'blank',		"Il manque l'id de la comp&eacute;tence !");
$oForm->testError0(null, 'convert_int',	"L'id de la comp&eacute;tence doit &ecirc;tre un entier !");
$nCompetenceId = $oForm->get(null);

$oForm->read('COMPETENCE_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ \"COMPETENCE_NOM\" !");
$oForm->testError0(null, 'blank',		"Il manque le nom de la comp&eacute;tence !");
$oForm->testError0(null, 'is_string',	"Le nom de la comp&eacute;tence doit &ecirc;tre une cha&icirc;ne de caract&egrave;res !");
$sCompetenceNom = $oForm->get(null);

$oForm->read('ID_MATIERE', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"ID_MATIERE\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la mati&egrave;re !");
$oForm->testError0(null, 'convert_int',	"L'id de la mati&egrave;re doit &ecirc;tre un entier !");
$nMatiereId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

		$sQuery =
			"UPDATE COMPETENCES" .
			" SET COMPETENCE_NOM = ".Database::prepareString($sCompetenceNom) . "," .
			"	  ID_MATIERE = {$nMatiereId} " .
			" WHERE COMPETENCE_ID = {$nCompetenceId} ";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=competences");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=competences");
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
header("Location: ?page=competences&mode=edit&competence_id={$nCompetenceId}&matiere_id={$nMatiereId}");
return;
