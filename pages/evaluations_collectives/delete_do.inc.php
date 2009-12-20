<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eval_col_delete');
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

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'supprimer':
		if($oForm->hasError() == true) break;

		$sQuery =
			"DELETE FROM EVALUATIONS_COLLECTIVES " .
			" WHERE EVAL_COL_ID = {$nEvalColId} ";
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
header("Location: ?page=evaluations_collectives");
return;
