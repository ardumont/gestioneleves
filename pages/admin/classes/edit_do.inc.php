<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// classe a mettre a jour
$oForm->read('CLASSE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"CLASSE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la classe de l'&eacute;l&egrave;ve !");
$oForm->testError0(null, 'convert_int',	"L'id de la classe de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nClasseId = $oForm->get(null);

// ecole de la classe
$oForm->read('ID_ECOLE', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ \"ID_ECOLE\" !");
$oForm->testError0(null, 'blank',     "Il manque l'&eacute;cole de la nouvelle classe !");
$oForm->testError0(null, 'is_int', 	"L'id de l'&eacute;cole de la classe doit &ecirc;tre un entier !");
$nIdEcole = $oForm->get(null);

// nom de la classe
$oForm->read('CLASSE_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ \"CLASSE_NOM\" !");
$oForm->testError0(null, 'blank',     "Il manque le nom de la nouvelle classe !");
$oForm->testError0(null, 'is_string', "Le nom de la nouvelle classe doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sClasseNom = $oForm->get(null);

// annee scolaire
$oForm->read('CLASSE_ANNEE_SCOLAIRE', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ \"CLASSE_ANNEE_SCOLAIRE\" !");
$oForm->testError0(null, 'blank',     "Il manque l'ann&eacute;e scolaire !");
$oForm->testError0(null, 'is_string', "L'ann&eacute;e scolaire doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sClasseAnneeScolaire = $oForm->get(null);

// niveau de la classe
$oForm->read('ID_NIVEAU', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ \"ID_NIVEAU\" !");
$oForm->testError0(null, 'blank',     "Il manque le niveau de la nouvelle classe !");
$oForm->testError0(null, 'is_int', "L'id du niveau de la classe doit &ecirc;tre un entier!");
$nIdNiveau = $oForm->get(null);

// professeur de la classe
$oForm->read('ID_PROFESSEUR', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ \"ID_PROFESSEUR\" !");
$oForm->testError0(null, 'blank',     "Il manque le professeur de la nouvelle classe !");
$oForm->testError0(null, 'is_int', 	"L'id du professeur doit &ecirc;tre un entier!");
$nIdProfesseur = $oForm->get(null);

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

		// mise a jour des valeurs de la classe
		$sQuery =
			"UPDATE CLASSES" .
			" SET CLASSE_NOM = ".Database::prepareString($sClasseNom) . "," .
			"     CLASSE_ANNEE_SCOLAIRE = " . Database::prepareString($sClasseAnneeScolaire) . "," .
			"     ID_ECOLE = {$nIdEcole}".
			" WHERE CLASSE_ID = {$nClasseId}";
		Database::execute($sQuery);

		////////////////////////////////////////
		// MISE A JOUR VALEURS RELATIONNELLES //
		////////////////////////////////////////

		// changement du professeur qui enseigne la classe $nClasseId
		$sQuery =
			"UPDATE PROFESSEUR_CLASSE " .
			" SET ID_PROFESSEUR = {$nIdProfesseur} ".
			" WHERE ID_CLASSE = {$nClasseId}";
		Database::execute($sQuery);

		// mise a jour du niveau de la classe
		$sQuery =
			"UPDATE NIVEAU_CLASSE " .
			" SET ID_NIVEAU = {$nIdNiveau} ".
			" WHERE ID_CLASSE = {$nClasseId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=classes");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=classes");
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
header("Location: ?page=classes&mode=edit&classe_id={$nClasseId}");
return;
