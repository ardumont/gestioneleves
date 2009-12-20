<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('professeur_edit');
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

// Id du professeur à modifier
$oForm->read('PROFESSEUR_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"PROFESSEUR_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id du professeur !");
$oForm->testError0(null, 'convert_int',	"L'id du professeur doit &ecirc;tre un entier !");
$nProfesseurId = $oForm->get(null);

// Nom du professeur
$oForm->read('PROFESSEUR_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ \"PROFESSEUR_NOM\" !");
$oForm->testError0(null, 'blank',     "Il manque le nom du professeur !");
$oForm->testError0(null, 'is_string', "Le nom du professeur doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sProfesseurNom = $oForm->get(null);

// Id du profil
$oForm->read('profil_id', $_POST);
$oForm->testError0(null, 'exist', "Il manque le champ profil_id !");
$oForm->testError0(null, 'blank', "Il manque l'id du profil !");
$oForm->testError0(null, 'is_string', "L'id du profil du nouveau professeur doit être un entier !");
$nIdProfil = $oForm->get(null);

//==============================================================================
// Traitement des donnees
//==============================================================================

// On ne fait plus de test s'il y a eu une erreur.
$oForm->setStopAll($oForm->hasError());

// Vérification de l'existence de toutes les tâches saisies
$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PROFESSEURS
	WHERE PROFESSEUR_ID = {$nProfesseurId}
EOQ;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant du professeur \"{$nProfesseurId}\" n'est pas valide !");

// On ne fait plus de test s'il y a eu une erreur.
$oForm->setStopAll($oForm->hasError());

// Vérification de l'existence de toutes les tâches saisies
$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PROFILS
	WHERE PROFIL_ID = {$nIdProfil}
EOQ;

$oForm->readArray('query2', Database::fetchOneRow($sQuery));
$oForm->testError0('query2.EXIST', 'exist', "L'identifiant du profil \"{$nIdProfil}\" n'est pas valide !");

//==============================================================================
// Actions du formulaire
//==============================================================================

if($oForm->hasError() == true)
{
	// On stocke toutes les erreurs de formulaire.
	Message::addErrorFromFormValidation($oForm->getError());

	// Retourne sur la page appelante
	header("Location: ?page=professeurs&mode=edit");
	return;
}

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

		// Préparation du nom du professeur
		$sQueryProfNom = Database::prepareString($sProfesseurNom);

		// Mise a jour des valeurs de la classe
		$sQuery = <<< ________EOQ
			UPDATE PROFESSEURS
				SET PROFESSEUR_NOM = {$sQueryProfNom},
				PROFESSEUR_PROFIL_ID = {$nIdProfil}
			WHERE PROFESSEUR_ID = {$nProfesseurId}
________EOQ;
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=professeurs");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=professeurs");
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
header("Location: ?page=professeurs&mode=edit&professeur_id={$nProfesseurId}");
return;
