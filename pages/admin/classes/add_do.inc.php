<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// niveau de la classe
$objForm->read('ID_NIVEAU', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ ID_NIVEAU !");
$objForm->testError0(null, 'blank',     "Il manque le niveau de la nouvelle classe !");
$objForm->testError0(null, 'is_int', "L'id du niveau de la classe doit &ecirc;tre un entier!");
$nIdNiveau = $objForm->get(null);

// professeur de la classe
$objForm->read('ID_PROFESSEUR', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ ID_PROFESSEUR !");
$objForm->testError0(null, 'blank',     "Il manque le professeur de la nouvelle classe !");
$objForm->testError0(null, 'is_int', 	"L'id du professeur doit &ecirc;tre un entier!");
$nIdProfesseur = $objForm->get(null);

// ecole de la classe
$objForm->read('ID_ECOLE', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ ID_ECOLE !");
$objForm->testError0(null, 'blank',     "Il manque l'&eacute;cole de la nouvelle classe !");
$objForm->testError0(null, 'is_int', 	"L'id de l'&eacute;cole de la classe doit &ecirc;tre un entier !");
$nIdEcole = $objForm->get(null);

// nom de la classe
$objForm->read('CLASSE_NOM', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ CLASSE_NOM !");
$objForm->testError0(null, 'blank',     "Il manque le nom de la nouvelle classe !");
$objForm->testError0(null, 'is_string', "Le nom de la nouvelle classe doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sClasseNom = $objForm->get(null);

// annee scolaire
$objForm->read('CLASSE_ANNEE_SCOLAIRE', $_POST);
$objForm->testError0(null, 'exist',     "Il manque l'ann&eacute;e scolaire !");
$objForm->testError0(null, 'blank',     "Il manque l'ann&eacute;e scolaire !");
$objForm->testError0(null, 'is_string', "L'ann&eacute;e scolaire doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sClasseAnneeScolaire = $objForm->get(null);

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

		// rechargement de la liste des eleves
		header("Location: ?page=classes&mode=add");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=classes&mode=add");
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
header("Location: ?page=classes&mode=add");
return;

?>
