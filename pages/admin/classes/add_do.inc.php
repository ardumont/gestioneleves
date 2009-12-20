<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('classe_add');
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

// niveau de la classe
$oForm->read('ID_NIVEAU', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ ID_NIVEAU !");
$oForm->testError0(null, 'blank',     "Il manque le niveau de la nouvelle classe !");
$oForm->testError0(null, 'is_int', "L'id du niveau de la classe doit &ecirc;tre un entier!");
$nIdNiveau = $oForm->get(null);

// professeur de la classe
$oForm->read('ID_PROFESSEUR', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ ID_PROFESSEUR !");
$oForm->testError0(null, 'blank',     "Il manque le professeur de la nouvelle classe !");
$oForm->testError0(null, 'is_int', 	"L'id du professeur doit &ecirc;tre un entier!");
$nIdProfesseur = $oForm->get(null);

// ecole de la classe
$oForm->read('ID_ECOLE', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ ID_ECOLE !");
$oForm->testError0(null, 'blank',     "Il manque l'&eacute;cole de la nouvelle classe !");
$oForm->testError0(null, 'is_int', 	"L'id de l'&eacute;cole de la classe doit &ecirc;tre un entier !");
$nIdEcole = $oForm->get(null);

// nom de la classe
$oForm->read('CLASSE_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ CLASSE_NOM !");
$oForm->testError0(null, 'blank',     "Il manque le nom de la nouvelle classe !");
$oForm->testError0(null, 'is_string', "Le nom de la nouvelle classe doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sClasseNom = $oForm->get(null);

// annee scolaire
$oForm->read('CLASSE_ANNEE_SCOLAIRE', $_POST);
$oForm->testError0(null, 'exist',     "Il manque l'ann&eacute;e scolaire !");
$oForm->testError0(null, 'blank',     "Il manque l'ann&eacute;e scolaire !");
$oForm->testError0(null, 'is_string', "L'ann&eacute;e scolaire doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sClasseAnneeScolaire = $oForm->get(null);

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
			"INSERT INTO CLASSES (CLASSE_NOM, CLASSE_ANNEE_SCOLAIRE, ID_ECOLE)" .
			"VALUES(" .
				Database::prepareString($sClasseNom) . "," .
				Database::prepareString($sClasseAnneeScolaire) . "," .
				$nIdEcole .
			")";
		Database::execute($sQuery);

		// recupere l'id de la classe nouvellement inseree
		$sQuery =
			" SELECT MAX(CLASSE_ID) " .
			" FROM CLASSES";
		$nId = Database::fetchOneValue($sQuery);

		// creation du lien entre cette classe et son niveau
		$sQuery =
			"INSERT INTO NIVEAU_CLASSE (ID_NIVEAU, ID_CLASSE)" .
			"VALUES(".
				$nIdNiveau . "," .
				$nId.
			")";
		Database::execute($sQuery);

		// creation du lien entre cette classe et son niveau
		$sQuery =
			"INSERT INTO PROFESSEUR_CLASSE (ID_PROFESSEUR, ID_CLASSE)" .
			"VALUES(".
				$nIdProfesseur . "," .
				$nId.
			")";
		Database::execute($sQuery);

		// rechargement de la liste des classes
		header("Location: ?page=classes&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=classes&mode=add");
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
header("Location: ?page=classes&mode=add");
return;
