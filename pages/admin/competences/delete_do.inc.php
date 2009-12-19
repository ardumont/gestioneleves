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

$oForm->read('MATIERE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"MATIERE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la mati&egrave;re !");
$oForm->testError0(null, 'convert_int',	"L'id de la mati&egrave;re doit &ecirc;tre un entier !");
$nMatiereId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($oForm->hasError() == true) break;

		// supprime uniquement le lien entre l'eleve et la classe
		$sQuery =
			"DELETE FROM COMPETENCES " .
			" WHERE COMPETENCE_ID = {$nCompetenceId} ".
			" AND ID_MATIERE = {$nMatiereId} ";
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
header("Location: ?page=competences");
return;
