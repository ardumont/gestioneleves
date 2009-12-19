<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// matiere a mettre a jour
$oForm->read('MATIERE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"MATIERE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la mati&egrave;re !");
$oForm->testError0(null, 'convert_int',	"L'id de la mati&egrave;re doit &ecirc;tre un entier !");
$nMatiereId = $oForm->get(null);

// matiere de la competence
$oForm->read('ID_DOMAINE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ ID_DOMAINE !");
$oForm->testError0(null, 'blank',	"Il manque le domaine de la mati&eacute;re !");
$oForm->testError0(null, 'is_int',"L'id du domaine de la mati&eacute;re doit &ecirc;tre un entier!");
$nIdDomaine = $oForm->get(null);

// nom de la competence
$oForm->read('MATIERE_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ MATIERE_NOM !");
$oForm->testError0(null, 'blank',		"Il manque le nom de la mati&egrave;re !");
$oForm->testError0(null, 'is_string',	"Le nom de la mati&egrave;re doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sMatiereNom = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

		$sQuery =
			"UPDATE MATIERES " .
			" SET MATIERE_NOM = ".Database::prepareString($sMatiereNom) . "," .
			"     ID_DOMAINE = {$nIdDomaine}".
			" WHERE MATIERE_ID = {$nMatiereId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=matieres");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=matieres");
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
header("Location: ?page=matieres&mode=edit&matiere_id={$nMatiereId}");
return;
