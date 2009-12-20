<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eleve_edit');
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

$oForm->read('ELEVE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"ELEVE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de l'&eacute;l&egrave !");
$oForm->testError0(null, 'convert_int',	"L'id de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nEleveId = $oForm->get(null);

$oForm->read('ELEVE_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ \"ELEVE_NOM\" !");
$oForm->testError0(null, 'blank',		"Il manque le nom de l'&eacute;l&egrave;ve !");
$oForm->testError0(null, 'is_string',	"Le nom de l'&eacute;l&egrave;ve doit &ecirc;tre une cha&icirc;ne de caract&egrave;res !");
$sEleveNom = $oForm->get(null);

$oForm->read('ELEVE_DATE_NAISSANCE', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ \"ELEVE_DATE_NAISSANCE\" !");
$oForm->testError0(null, 'blank',		"Il manque la date de naissance de l'&eacute;l&egrave;ve !");
$oForm->testError0(null, 'is_string',	"La date de naissance de l'&eacute;l&egrave;ve doit &ecirc;tre une cha&icirc;ne de caract&egrave;res au format dd/MM/YYYY!");
$sEleveDateNaissance = $oForm->get(null);

$oForm->read('ELEVE_ACTIF', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"ELEVE_ACTIF\" !");
$oForm->testError0(null, 'blank',			"On ne sait pas si l'&eacute;l&egrave;ve est actif !");
$oForm->testError0(null, 'convert_bool',	"Actif ou inactif. Un boolean !");
$bEleveActif = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

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
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=eleves");
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
header("Location: ?page=eleves&mode=edit&eleve_id={$nEleveId}");
return;
