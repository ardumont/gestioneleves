<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

$objForm->read('ELEVE_ID', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ \"ELEVE_ID\" !");
$objForm->testError0(null, 'blank',		"Il manque l'id de l'&eacute;l&egrave !");
$objForm->testError0(null, 'convert_int',	"L'id de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nEleveId = $objForm->get(null);

$objForm->read('CLASSE_ID', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ \"CLASSE_ID\" !");
$objForm->testError0(null, 'blank',		"Il manque l'id de la classe de l'&eacute;l&egrave;ve !");
$objForm->testError0(null, 'convert_int',	"L'id de la classe de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nClasseId = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($objForm->hasError() == true) break;

		// supprime uniquement le lien entre l'eleve et la classe
		$sQuery =
			"DELETE FROM ELEVE_CLASSE " .
			" WHERE ID_ELEVE = {$nEleveId} ".
			" AND ID_CLASSE = {$nClasseId} ";
		Database::execute($sQuery);

		// verifie si l'eleve en question appartient a d'autres classes
		$sQuery =
			"SELECT ID_ELEVE " .
			" FROM ELEVE_CLASSE " .
			" WHERE ID_ELEVE = {$nEleveId} ";
		$aEleves = Database::fetchArray($sQuery);

		// s'il n'appartient pas a d'autres classes (donc aucun resultat
		// pour la requete precedente), on le supprime de la base
		if(count($aEleves) <= 0)
		{
			$sQuery =
				"DELETE FROM ELEVES " .
				" WHERE ELEVE_ID = {$nEleveId} ";
			Database::execute($sQuery);
		}

		// Rechargement
		header("Location: ?page=eleves&classe_id={$nClasseId}");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=eleves&classe_id={$nClasseId}");
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
header("Location: ?page=eleves&classe_id={$nClasseId}");
return;
