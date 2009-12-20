<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('niveau_delete');
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

// niveau a supprimer
$oForm->read('NIVEAU_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"NIVEAU_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id du niveau !");
$oForm->testError0(null, 'convert_int',	"L'id du niveau doit &ecirc;tre un entier !");
$nNiveauId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($oForm->hasError() == true) break;

		// supprime le niveau
		$sQuery =
			"DELETE FROM NIVEAUX " .
			" WHERE NIVEAU_ID = {$nNiveauId} ";
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
header("Location: ?page=niveaux");
return;
