<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

$objForm->read('EVAL_COL_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"EVAL_COL_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de l'&eacute;valuation collective !");
$objForm->testError0(null, 'convert_int',	"L'id de l'&eacute;valuation collective doit &ecirc;tre un entier !");
$nEvalColId = $objForm->get(null);

$objForm->read('PERIODE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"PERIODE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de la p&eacute;riode !");
$objForm->testError0(null, 'convert_int',	"L'id de la p&eacute;riode doit &ecirc;tre un entier !");
$nPeriodeId = $objForm->get(null);

$objForm->read('EVAL_COL_NOM', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ \"EVAL_COL_NOM\" !");
$objForm->testError0(null, 'blank',		"Il manque le nom de l'&eacute;valuation collective !");
$objForm->testError0(null, 'is_string',	"Le nom de l'&eacute;valuation collective doit &ecirc;tre une cha&icirc;ne de caract&egrave;res !");
$sEvalColNom = $objForm->get(null);

// description de l'evaluation collective
$sEvalColDescription = $objForm->getValue('EVAL_COL_DESCRIPTION', $_POST, 'is_string', "");

$objForm->read('EVAL_COL_DATE', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ \"EVAL_COL_DATE\" !");
$objForm->testError0(null, 'blank',		"Il manque la date de l'&eacute;valuation collective !");
$objForm->testError0(null, 'is_string',	"La date de l'&eacute;valuation collective de l'&eacute;l&egrave;ve doit &ecirc;tre une cha&icirc;ne de caract&egrave;res au format dd/MM/YYYY!");
$sEvalColDate = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($objForm->hasError() == true) break;

		$sQuery =
			"UPDATE EVALUATIONS_COLLECTIVES " .
			" SET EVAL_COL_NOM = ".Database::prepareString($sEvalColNom) . "," .
			"     EVAL_COL_DATE = STR_TO_DATE(" . Database::prepareString($sEvalColDate) . ", '%d/%m/%Y')," . 
			"     EVAL_COL_DESCRIPTION = ".Database::prepareString($sEvalColDescription) . "," .
			"     ID_PERIODE = {$nPeriodeId} " .
			" WHERE EVAL_COL_ID = {$nEvalColId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=evaluations_collectives&mode=edit&eval_col_id={$nEvalColId}");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=evaluations_collectives");
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
header("Location: ?page=evaluations_collectives&mode=edit&eval_col_id={$nEvalColId}");
return;
?>
