<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eval_col_add');
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

// periode de l'evaluation collective
$oForm->read('ID_PERIODE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ ID_PERIODE !");
$oForm->testError0(null, 'blank',	"Il manque la p&eacute;riode de l'&eacute;valuation collective !");
$oForm->testError0(null, 'is_int',"L'id de la p&eacute;riode doit &ecirc;tre un entier!");
$nIdPeriode = $oForm->get(null);

// classe de l'evaluation collective
$oForm->read('ID_CLASSE', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ ID_CLASSE !");
$oForm->testError0(null, 'blank',     "Il manque la classe de l'&eacute;valuation collective !");
$oForm->testError0(null, 'is_int', 	"L'id e la classe doit &ecirc;tre un entier !");
$nIdClasse = $oForm->get(null);

// nom de l'evaluation collective
$oForm->read('EVAL_COL_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ EVAL_COL_NOM !");
$oForm->testError0(null, 'blank',     "Il manque le nom de l'&eacute;valuation collective !");
$oForm->testError0(null, 'is_string', "Le nom de l'&eacute;valuation collective doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sEvalColNom = $oForm->get(null);

// description de l'evaluation collective
$sEvalColDescription = $oForm->getValue('EVAL_COL_DESCRIPTION', $_POST, 'is_string', "");

$oForm->read('EVAL_COL_DATE', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ EVAL_COL_DATE !");
$oForm->testError0(null, 'blank',     "Il manque la date de l'&eacute;valuation collective !");
$oForm->testError0(null, 'is_string', "La date de l'&eacute;valuation collective doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sEvalColDate = $oForm->get(null);

// description de l'evaluation collective
$aIdCompetences = isset($_POST['ID_COMPETENCE']) && $_POST['ID_COMPETENCE'] != false ? $_POST['ID_COMPETENCE'] : array();

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($oForm->hasError() == true) break;
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

        // recuperer le dernier id de l'evaluation
        $nIdEvalCol = Database::lastInsertId();

        if ($aIdCompetences != null) {
            foreach($aIdCompetences as $key => $nIdComp) {
                $sQuery =
                    "INSERT INTO EVAL_COMPETENCES (" .
                    "	ID_EVAL_COL, ID_COMPETENCE" .
                    ")" .
                    "VALUES(" .
                    Database::prepareString($nIdEvalCol) . "," .
                    Database::prepareString($nIdComp) .
                    ")";
                Database::execute($sQuery);
            }
        }

		// rechargement de la liste des eleves
		header("Location: ?page=evaluations_collectives&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=evaluations_collectives&mode=add");
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
header("Location: ?page=evaluations_collectives&mode=add");
return;
