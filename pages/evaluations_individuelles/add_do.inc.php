<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// id de l'evaluation collective a laquelle l'evaluation individuelle est rattachee
$objForm->read('ID_EVAL_COL', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ ID_EVAL_COL !");
$objForm->testError0(null, 'blank',	"Il manque l'id de l'&eacute;valuation collective !");
$objForm->testError0(null, 'is_int',"L'id de l'&eacute;valuation collective doit &ecirc;tre un entier!");
$nIdEvalCol = $objForm->get(null);

// eleve
$objForm->read('ID_ELEVE', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ ID_ELEVE !");
$objForm->testError0(null, 'blank',	"Il manque l'&eacute;l&egrave;ve !");
$objForm->testError0(null, 'is_int',"L'id de l'&eacute;l&egrave;ve doit &ecirc;tre un entier!");
$nIdEleve = $objForm->get(null);

// note
$objForm->read('ID_NOTE', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ ID_NOTE !");
$objForm->testError0(null, 'blank',	"Il manque la note de l'&eacute;valuation individuelle !");
$objForm->testError0(null, 'is_int',"L'id de la note doit &ecirc;tre un entier!");
$nIdNote = $objForm->get(null);

// competence
$objForm->read('ID_COMPETENCE', $_POST);
$objForm->testError0(null, 'exist', "Il manque le champ ID_COMPETENCE !");
$objForm->testError0(null, 'blank', "Il manque la comp&eacute;tence de l'&eacute;valuation individuelle !");
$objForm->testError0(null, 'is_int',"L'id de la comp&eacute;tence doit &ecirc;tre un entier !");
$nIdCompetence = $objForm->get(null);

// appreciation de l'eleve
$sEvalIndCommentaire = $objForm->getValue('EVAL_IND_COMMENTAIRE', $_POST, 'is_string', "");

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($objForm->hasError() == true) break;
		// insertion de l'eleve dans la table		
		$sQuery =
			"INSERT INTO EVALUATIONS_INDIVIDUELLES (" .
			"	EVAL_IND_COMMENTAIRE, " .
			"   ID_EVAL_COL, ID_ELEVE, ID_NOTE, ID_COMPETENCE " .
			")" .
			"VALUES(" .
				Database::prepareString($sEvalIndCommentaire) . "," . 
				"{$nIdEvalCol}, {$nIdEleve}, {$nIdNote}, {$nIdCompetence}" . 
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=evaluations_individuelles&mode=add&ideval={$nIdEvalCol}");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=evaluations_individuelles&mode=add");
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
header("Location: ?page=evaluations_individuelles&mode=add");
return;

?>
