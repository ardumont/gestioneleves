<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// matiere de la competence
$objForm->read('ID_MATIERE', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ ID_MATIERE !");
$objForm->testError0(null, 'blank',	"Il manque la mati&eacute;re de la comp&eacute;tence !");
$objForm->testError0(null, 'is_int',"L'id de la mati&eacute;re de la comp&eacute;tence doit &ecirc;tre un entier!");
$nIdMatiere = $objForm->get(null);

// nom de la competence
$objForm->read('COMPETENCE_NOM', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ COMPETENCE_NOM !");
$objForm->testError0(null, 'blank',		"Il manque le nom de la comp&eacute;tence !");
$objForm->testError0(null, 'is_string',	"Le nom de la comp&eacute;tence doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sCompetenceNom = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($objForm->hasError() == true) break;
		// insertion de la competence dans la table
		$sQuery =
			"INSERT INTO COMPETENCES (COMPETENCE_NOM, ID_MATIERE)" .
			"VALUES(" .
				Database::prepareString($sCompetenceNom) . "," .
				$nIdMatiere .
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=competences&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=competences&mode=add");
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
header("Location: ?page=competences&mode=add");
return;
