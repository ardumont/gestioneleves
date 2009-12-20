<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('domaine_delete');
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

$oForm->read('DOMAINE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"DOMAINE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id du domaine !");
$oForm->testError0(null, 'convert_int',	"L'id du domaine doit &ecirc;tre un entier !");
$nDomaineId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($oForm->hasError() == true) break;

		// supprime le domaine
		$sQuery =
			"DELETE FROM DOMAINES " .
			" WHERE DOMAINE_ID = {$nDomaineId} ";
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
header("Location: ?page=domaines");
return;
