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
$objForm->testError0(null, 'exist',			"Il manque le champ \"ELEVE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de l'&eacute;l&egrave !");
$objForm->testError0(null, 'convert_int',	"L'id de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nEleveId = $objForm->get(null);

$objForm->read('ELEVE_NOM', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ \"ELEVE_NOM\" !");
$objForm->testError0(null, 'blank',		"Il manque le nom de l'&eacute;l&egrave;ve !");
$objForm->testError0(null, 'is_string',	"Le nom de l'&eacute;l&egrave;ve doit &ecirc;tre une cha&icirc;ne de caract&egrave;res !");
$sEleveNom = $objForm->get(null);

$objForm->read('ELEVE_DATE_NAISSANCE', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ \"ELEVE_DATE_NAISSANCE\" !");
$objForm->testError0(null, 'blank',		"Il manque la date de naissance de l'&eacute;l&egrave;ve !");
$objForm->testError0(null, 'is_string',	"La date de naissance de l'&eacute;l&egrave;ve doit &ecirc;tre une cha&icirc;ne de caract&egrave;res au format dd/MM/YYYY!");
$sEleveDateNaissance = $objForm->get(null);

$objForm->read('ELEVE_ACTIF', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"ELEVE_ACTIF\" !");
$objForm->testError0(null, 'blank',			"On ne sait pas si l'&eacute;l&egrave;ve est actif !");
$objForm->testError0(null, 'convert_bool',	"Actif ou inactif. Un boolean !");
$bEleveActif = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($objForm->hasError() == true) break;

		$sQuery =
			"UPDATE ELEVES" .
			" SET ELEVE_NOM = ".Database::prepareString($sEleveNom) . "," .
			"     ELEVE_DATE_NAISSANCE = STR_TO_DATE(" . Database::prepareString($sEleveDateNaissance) . ", '%d/%m/%Y')," .
			"     ELEVE_ACTIF = {$bEleveActif}".
			" WHERE ELEVE_ID = {$nEleveId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=eleves");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=eleves");
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
header("Location: ?page=eleves&mode=edit&eleve_id={$nEleveId}");
return;
