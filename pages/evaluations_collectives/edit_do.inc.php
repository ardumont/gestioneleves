<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eval_col_edit');
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

$oForm->read('EVAL_COL_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"EVAL_COL_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de l'&eacute;valuation collective !");
$oForm->testError0(null, 'convert_int',	"L'id de l'&eacute;valuation collective doit &ecirc;tre un entier !");
$nEvalColId = $oForm->get(null);

$oForm->read('PERIODE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"PERIODE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la p&eacute;riode !");
$oForm->testError0(null, 'convert_int',	"L'id de la p&eacute;riode doit &ecirc;tre un entier !");
$nPeriodeId = $oForm->get(null);

$oForm->read('EVAL_COL_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ \"EVAL_COL_NOM\" !");
$oForm->testError0(null, 'blank',		"Il manque le nom de l'&eacute;valuation collective !");
$oForm->testError0(null, 'is_string',	"Le nom de l'&eacute;valuation collective doit &ecirc;tre une cha&icirc;ne de caract&egrave;res !");
$sEvalColNom = $oForm->get(null);

// description de l'evaluation collective
$sEvalColDescription = $oForm->getValue('EVAL_COL_DESCRIPTION', $_POST, 'is_string', "");

$oForm->read('EVAL_COL_DATE', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ \"EVAL_COL_DATE\" !");
$oForm->testError0(null, 'blank',		"Il manque la date de l'&eacute;valuation collective !");
$oForm->testError0(null, 'is_string',	"La date de l'&eacute;valuation collective de l'&eacute;l&egrave;ve doit &ecirc;tre une cha&icirc;ne de caract&egrave;res au format dd/MM/YYYY!");
$sEvalColDate = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

		$sQuery =
			"UPDATE EVALUATIONS_COLLECTIVES " .
			" SET EVAL_COL_NOM = ".Database::prepareString($sEvalColNom) . "," .
			"     EVAL_COL_DATE = STR_TO_DATE(" . Database::prepareString($sEvalColDate) . ", '%d/%m/%Y')," .
			"     EVAL_COL_DESCRIPTION = ".Database::prepareString($sEvalColDescription) . "," .
			"     ID_PERIODE = {$nPeriodeId} " .
			" WHERE EVAL_COL_ID = {$nEvalColId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=evaluations_collectives");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=evaluations_collectives");
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
header("Location: ?page=evaluations_collectives&mode=edit&eval_col_id={$nEvalColId}");
return;
