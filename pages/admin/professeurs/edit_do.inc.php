<?php
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

//==============================================================================
// Actions du formulaire
//==============================================================================

if($oForm->hasError() == true)
{
	// On stocke toutes les erreurs de formulaire.
	Message::addErrorFromFormValidation($oForm->getError());

	// Retourne sur la page appelante
	header("Location: ?page=professeurs");
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

		/////////////////////////////////////
		// MISE A JOUR VALEURS PRINCIPALES //
		/////////////////////////////////////

		$sQueryProfNom = Database::prepareString($sProfesseurNom);
		// mise a jour des valeurs de la classe
		$sQuery = <<< ________EOQ
			UPDATE PROFESSEURS
				SET PROFESSEUR_NOM = {$sQueryProfNom}
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
