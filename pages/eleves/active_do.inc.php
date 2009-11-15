<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objFormEleve = new FormValidation();

$sAction = $objFormEleve->getValue('action', $_POST, 'is_string', "");

$objFormEleve->read('ELEVE_ID', $_POST);
$objFormEleve->testError0(null, 'exist',		"Il manque le champ \"ELEVE_ID\" !");
$objFormEleve->testError0(null, 'blank',		"Il manque l'id de l'&eacute;l&egrave !");
$objFormEleve->testError0(null, 'convert_int',	"L'id de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nEleveId = $objFormEleve->get(null);

$objFormEleve->read('CLASSE_ID', $_POST);
$objFormEleve->testError0(null, 'exist',		"Il manque le champ \"CLASSE_ID\" !");
$objFormEleve->testError0(null, 'blank',		"Il manque l'id de la classe de l'&eacute;l&egrave;ve !");
$objFormEleve->testError0(null, 'convert_int',	"L'id de la classe de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nClasseId = $objFormEleve->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'activer':
		$objFormEleve->clearError();

		$sQuery =
			"UPDATE ELEVES" .
			" SET ELEVE_ACTIF = 1".
			" WHERE ELEVE_ID = {$nEleveId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=eleves&classe_id={$nClasseId}");
		return;
	break;

	// ----------
	case 'annuler':
		$objFormEleve->clearError();

		// Rechargement
		header("Location: ?page=eleves&classe_id={$nClasseId}");
		return;
	break;

	// ----------
	default:
		$objFormEleve->clearError();

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
Message::addErrorFromFormValidation($objFormEleve->getError());

// Rechargement
header("Location: ?page=eleves&classe_id={$nClasseId}");
return;
