<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// classe a mettre a jour
$objForm->read('CLASSE_ID', $_POST);
$objForm->testError0(null, 'exist',			"Il manque le champ \"CLASSE_ID\" !");
$objForm->testError0(null, 'blank',			"Il manque l'id de la classe de l'&eacute;l&egrave;ve !");
$objForm->testError0(null, 'convert_int',	"L'id de la classe de l'&eacute;l&egrave;ve doit &ecirc;tre un entier !");
$nClasseId = $objForm->get(null);

// ecole de la classe
$objForm->read('ID_ECOLE', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ \"ID_ECOLE\" !");
$objForm->testError0(null, 'blank',     "Il manque l'&eacute;cole de la nouvelle classe !");
$objForm->testError0(null, 'is_int', 	"L'id de l'&eacute;cole de la classe doit &ecirc;tre un entier !");
$nIdEcole = $objForm->get(null);

// nom de la classe
$objForm->read('CLASSE_NOM', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ \"CLASSE_NOM\" !");
$objForm->testError0(null, 'blank',     "Il manque le nom de la nouvelle classe !");
$objForm->testError0(null, 'is_string', "Le nom de la nouvelle classe doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sClasseNom = $objForm->get(null);

// annee scolaire
$objForm->read('CLASSE_ANNEE_SCOLAIRE', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ \"CLASSE_ANNEE_SCOLAIRE\" !");
$objForm->testError0(null, 'blank',     "Il manque l'ann&eacute;e scolaire !");
$objForm->testError0(null, 'is_string', "L'ann&eacute;e scolaire doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sClasseAnneeScolaire = $objForm->get(null);

// niveau de la classe
$objForm->read('ID_NIVEAU', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ \"ID_NIVEAU\" !");
$objForm->testError0(null, 'blank',     "Il manque le niveau de la nouvelle classe !");
$objForm->testError0(null, 'is_int', "L'id du niveau de la classe doit &ecirc;tre un entier!");
$nIdNiveau = $objForm->get(null);

// professeur de la classe
$objForm->read('ID_PROFESSEUR', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ \"ID_PROFESSEUR\" !");
$objForm->testError0(null, 'blank',     "Il manque le professeur de la nouvelle classe !");
$objForm->testError0(null, 'is_int', 	"L'id du professeur doit &ecirc;tre un entier!");
$nIdProfesseur = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($objForm->hasError() == true) break;

		/////////////////////////////////////
		// MISE A JOUR VALEURS PRINCIPALES //
		/////////////////////////////////////

		// mise a jour des valeurs de la classe
		$sQuery =
			"UPDATE CLASSES" .
			" SET CLASSE_NOM = ".Database::prepareString($sClasseNom) . "," .
			"     CLASSE_ANNEE_SCOLAIRE = " . Database::prepareString($sClasseAnneeScolaire) . "," . 
			"     ID_ECOLE = {$nIdEcole}".
			" WHERE CLASSE_ID = {$nClasseId}";
		Database::execute($sQuery);

		////////////////////////////////////////
		// MISE A JOUR VALEURS RELATIONNELLES //
		////////////////////////////////////////

		// changement du professeur qui enseigne la classe $nClasseId
		$sQuery =
			"UPDATE PROFESSEUR_CLASSE " .
			" SET ID_PROFESSEUR = {$nIdProfesseur} ".
			" WHERE ID_CLASSE = {$nClasseId}";
		Database::execute($sQuery);

		// mise a jour du niveau de la classe
		$sQuery =
			"UPDATE NIVEAU_CLASSE " .
			" SET ID_NIVEAU = {$nIdNiveau} ".
			" WHERE ID_CLASSE = {$nClasseId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=classes&mode=edit&classe_id={$nClasseId}");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();
		
		// Rechargement
		header("Location: ?page=classes");
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
header("Location: ?page=classes&mode=edit&classe_id={$nClasseId}");
return;

?>
