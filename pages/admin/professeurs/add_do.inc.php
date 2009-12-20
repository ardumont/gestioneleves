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

// Nom du professeur
$oForm->read('PROFESSEUR_NOM', $_POST);
$oForm->testError0(null, 'exist', "Il manque le champ PROFESSEUR_NOM !");
$oForm->testError0(null, 'blank', "Il manque le nom du nouveau professeur !");
$oForm->testError0(null, 'is_string', "Le nom du nouveau professeur doit être une chaîne de caractères !");
$sProfesseurNom = $oForm->get(null);

// Id du profil
$oForm->read('profil_id', $_POST);
$oForm->testError0(null, 'exist', "Il manque le champ profil_id !");
$oForm->testError0(null, 'blank', "Il manque l'id du profil !");
$oForm->testError0(null, 'is_string', "L'id du profil du nouveau professeur doit être un entier !");
$nIdProfil = $oForm->get(null);

// On ne fait plus de test s'il y a eu une erreur.
$oForm->setStopAll($oForm->hasError());

// Vérification de l'existence de toutes les tâches saisies
$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PROFILS
	WHERE PROFIL_ID = {$nIdProfil}
EOQ;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant du profil \"{$nIdProfil}\" n'est pas valide !");

//==============================================================================
// Actions du formulaire
//==============================================================================

if($oForm->hasError() == true)
{
	// On stocke toutes les erreurs de formulaire.
	Message::addErrorFromFormValidation($oForm->getError());

	// Retourne sur la page appelante
	header("Location: ?page=professeurs&mode=add");
	return;
}

switch(strtolower($sAction))
{
	// ajoute le professeur
	case 'ajouter':
		if($oForm->hasError() == true) break;

		// Préparation du nom du professeur
		$sQueryProfNom = Database::prepareString($sProfesseurNom);

		// Insertion de l'eleve dans la table
		$sQuery = <<< ________EOQ
			INSERT INTO PROFESSEURS(PROFESSEUR_NOM, PROFESSEUR_PROFIL_ID)
			VALUES({$sQueryProfNom}, {$nIdProfil})
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
