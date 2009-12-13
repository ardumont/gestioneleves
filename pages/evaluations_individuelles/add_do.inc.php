<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// Id de l'evaluation collective a laquelle l'evaluation individuelle est rattachee
$oForm->read('ID_EVAL_COL', $_POST);
$oForm->testError0(null, 'exist', "Il manque le champ ID_EVAL_COL !");
$oForm->testError0(null, 'blank', "Il manque l'id de l'évaluation collective !");
$oForm->testError0(null, 'is_int', "L'id de l'évaluation collective doit être un entier!");
$nIdEvalCol = $oForm->get(null);

// Note
$oForm->read('ID_NOTE', $_POST);
$oForm->testError0(null, 'exist', "Il manque le champ ID_NOTE !");
$oForm->testError0(null, 'blank', "Il manque la note de l'évaluation individuelle !");
$oForm->testError0(null, 'is_int', "L'id de la note doit être un entier!");
$nIdNote = $oForm->get(null);

// Appreciation de l'eleve
$sEvalIndCommentaire = $oForm->getValue('EVAL_IND_COMMENTAIRE', $_POST, 'is_string', "");

// Eleves
$aIdEleves = isset($_POST['ID_ELEVE']) && $_POST['ID_ELEVE'] != false ? $_POST['ID_ELEVE'] : array();

// Competences
$aIdCompetences = isset($_POST['ID_COMPETENCE']) && $_POST['ID_COMPETENCE'] != false ? $_POST['ID_COMPETENCE'] : array();

// Stocke en session les tableaux soumis pour faciliter l'affichage de la page add
$_SESSION['ID_ELEVE'] = $aIdEleves;
$_SESSION['ID_COMPETENCE'] = $aIdCompetences;
$_SESSION['ID_NOTE'] = $nIdNote;
$_SESSION['EVAL_IND_COMMENTAIRE'] = $sEvalIndCommentaire;

if($aIdEleves == false)
{
	$oForm->setError('ID_ELEVE', 'liste', "La liste des élèves doit être remplit avec au moins une entrée.");
}

if($aIdCompetences == false)
{
	$oForm->setError('ID_COMPETENCE', 'liste', "La liste des compétences doit être remplit avec au moins une entrée.");
}

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($oForm->hasError() == true) break;

		// Parcours la liste d'élèves soumis
		foreach($aIdEleves as $nIdEleve)
		{
			// Parcours des compétences évaluées dans l'évaluation individuelle
			foreach($aIdCompetences as $nIdCompetence)
			{
				// Récupération des informations pour la requete
				// Préparation de la requête
				$sQueryComm = Database::prepareString($sEvalIndCommentaire);
				// insertion de l'eleve dans la table
				$sQuery = <<< ________________EOQ
					INSERT INTO EVALUATIONS_INDIVIDUELLES (
						EVAL_IND_COMMENTAIRE, 
						ID_EVAL_COL, 
						ID_ELEVE, 
						ID_NOTE, 
						ID_COMPETENCE)
					VALUES({$sQueryComm}, {$nIdEvalCol}, {$nIdEleve}, {$nIdNote}, {$nIdCompetence})
________________EOQ;
				Database::execute($sQuery);
			}
		}

		// rechargement de la liste des eleves
		header("Location: ?page=evaluations_individuelles&mode=add&ideval={$nIdEvalCol}");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=evaluations_individuelles&mode=add");
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
header("Location: ?page=evaluations_individuelles&mode=add");
return;
