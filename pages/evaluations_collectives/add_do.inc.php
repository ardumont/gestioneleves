<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// periode de l'evaluation collective
$objForm->read('ID_PERIODE', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ ID_PERIODE !");
$objForm->testError0(null, 'blank',	"Il manque la p&eacute;riode de l'&eacute;valuation collective !");
$objForm->testError0(null, 'is_int',"L'id de la p&eacute;riode doit &ecirc;tre un entier!");
$nIdPeriode = $objForm->get(null);

// classe de l'evaluation collective
$objForm->read('ID_CLASSE', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ ID_CLASSE !");
$objForm->testError0(null, 'blank',     "Il manque la classe de l'&eacute;valuation collective !");
$objForm->testError0(null, 'is_int', 	"L'id e la classe doit &ecirc;tre un entier !");
$nIdClasse = $objForm->get(null);

// nom de l'evaluation collective
$objForm->read('EVAL_COL_NOM', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ EVAL_COL_NOM !");
$objForm->testError0(null, 'blank',     "Il manque le nom de l'&eacute;valuation collective !");
$objForm->testError0(null, 'is_string', "Le nom de l'&eacute;valuation collective doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sEvalColNom = $objForm->get(null);

// description de l'evaluation collective
$sEvalColDescription = $objForm->getValue('EVAL_COL_DESCRIPTION', $_POST, 'is_string', "");

// description de l'evaluation collective
$objForm->read('EVAL_COL_DATE', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ EVAL_COL_DATE !");
$objForm->testError0(null, 'blank',     "Il manque la date de l'&eacute;valuation collective !");
$objForm->testError0(null, 'is_string', "La date de l'&eacute;valuation collective doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sEvalColDate = $objForm->get(null);

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
			"INSERT INTO EVALUATIONS_COLLECTIVES (" .
			"	EVAL_COL_NOM, EVAL_COL_DESCRIPTION, EVAL_COL_DATE, " .
			"	ID_PERIODE, ID_CLASSE" .
			")" .
			"VALUES(" .
				Database::prepareString($sEvalColNom) . "," .
				Database::prepareString($sEvalColDescription) . "," .
				"STR_TO_DATE(".Database::prepareString($sEvalColDate).", '%d/%m/%Y')," .
				$nIdPeriode . "," .
				$nIdClasse .
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=evaluations_collectives&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=evaluations_collectives&mode=add");
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
header("Location: ?page=evaluations_collectives&mode=add");
return;
