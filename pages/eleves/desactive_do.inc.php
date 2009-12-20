<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eleve_active');
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

$oForm->read('ELEVE_ID', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ \"ELEVE_ID\" !");
$oForm->testError0(null, 'blank',		"Il manque l'id de l'&eacute;l&egrave !");
$oForm->testError0(null, 'convert_int',	"L'id de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nEleveId = $oForm->get(null);

$oForm->read('CLASSE_ID', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ \"CLASSE_ID\" !");
$oForm->testError0(null, 'blank',		"Il manque l'id de la classe de l'&eacute;l&egrave;ve !");
$oForm->testError0(null, 'convert_int',	"L'id de la classe de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nClasseId = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'desactiver':
		$oForm->clearError();

		$sQuery =
			"UPDATE ELEVES" .
			" SET ELEVE_ACTIF = 0".
			" WHERE ELEVE_ID = {$nEleveId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=eleves&classe_id={$nClasseId}");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=eleves&classe_id={$nClasseId}");
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
header("Location: ?page=eleves&classe_id={$nClasseId}");
return;
