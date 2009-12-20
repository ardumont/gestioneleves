<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('competence_add');
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

// matiere de la competence
$oForm->read('ID_MATIERE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ ID_MATIERE !");
$oForm->testError0(null, 'blank',	"Il manque la mati&eacute;re de la comp&eacute;tence !");
$oForm->testError0(null, 'is_int',"L'id de la mati&eacute;re de la comp&eacute;tence doit &ecirc;tre un entier!");
$nIdMatiere = $oForm->get(null);

// nom de la competence
$oForm->read('COMPETENCE_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ COMPETENCE_NOM !");
$oForm->testError0(null, 'blank',		"Il manque le nom de la comp&eacute;tence !");
$oForm->testError0(null, 'is_string',	"Le nom de la comp&eacute;tence doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sCompetenceNom = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($oForm->hasError() == true) break;
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
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=competences&mode=add");
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
header("Location: ?page=competences&mode=add");
return;
