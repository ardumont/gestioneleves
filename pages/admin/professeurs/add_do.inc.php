<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('professeur_add');
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

// nom de la classe
$oForm->read('PROFESSEUR_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ PROFESSEUR_NOM !");
$oForm->testError0(null, 'blank',     "Il manque le nom du nouveau professeur !");
$oForm->testError0(null, 'is_string', "Le nom du nouveau professeur doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sProfesseurNom = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute le professeur
	case 'ajouter':
		if($oForm->hasError() == true) break;

		$sQueryProfNom = Database::prepareString($sProfesseurNom);
		// insertion de l'eleve dans la table
		$sQuery = <<< ________EOQ
			INSERT INTO PROFESSEURS(PROFESSEUR_NOM)
			VALUES({$sQueryProfNom})
________EOQ;
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=professeurs&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=professeurs&mode=add");
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
header("Location: ?page=professeurs&mode=add");
return;
