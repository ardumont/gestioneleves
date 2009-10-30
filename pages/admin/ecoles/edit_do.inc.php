<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// ecole de la classe
$objForm->read('ECOLE_ID', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ ECOLE_ID !");
$objForm->testError0(null, 'blank',     "Il manque l'&eacute;cole de la nouvelle classe !");
$objForm->testError0(null, 'is_int', 	"L'id de l'&eacute;cole de la classe doit &ecirc;tre un entier !");
$nEcoleId = $objForm->get(null);

// si le champ ECOLE_NOM est specifie alors 
// il s'agit d'un ajout d'un nouvel eleve
$objForm->read('ECOLE_NOM', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ ECOLE_NOM !");
$objForm->testError0(null, 'blank',     "Il manque le nom de la nouvelle &eacute;cole !");
$objForm->testError0(null, 'is_string', "Le nom de l'&eacute;cole doit &ecirc;tre une cha&icirc;ene de caract&egrave;res !");
$sEcoleNom = $objForm->get(null);

// si le champ ECOLE_VILLE est specifie alors 
// il s'agit d'un ajout d'un nouvel eleve
$objForm->read('ECOLE_VILLE', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ ECOLE_VILLE !");
$objForm->testError0(null, 'blank',     "Il manque la ville la nouvelle &eacute;cole !");
$objForm->testError0(null, 'is_string', "Le nom de la ville doit &ecirc;tre une cha&icirc;ene de caract&egrave;res !");
$sEcoleVille = $objForm->get(null);

// si le champ ECOLE_DEPARTEMENT est specifie alors 
// il s'agit d'un ajout d'un nouvel eleve
$objForm->read('ECOLE_DEPARTEMENT', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ ECOLE_DEPARTEMENT !");
$objForm->testError0(null, 'blank',     "Il manque le d&eacute;partement de la nouvelle &eacute;cole !");
$objForm->testError0(null, 'is_int', 	"Le d&eacute;partement de l'&eacute;cole doit &ecirc;tre un entier !");
$sEcoleDep = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($objForm->hasError() == true) break;

		$sQuery =
			"UPDATE ECOLES" .
			" SET ECOLE_NOM = ".Database::prepareString($sEcoleNom) . "," .
			"     ECOLE_VILLE = ".Database::prepareString($sEcoleVille) . "," . 
			"     ECOLE_DEPARTEMENT = ".Database::prepareString($sEcoleDep) . 
			" WHERE ECOLE_ID = {$nEcoleId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=ecoles&mode=edit&ecole_id={$nEcoleId}");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();
		
		// Rechargement
		header("Location: ?page=ecoles");
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
header("Location: ?page=ecoles&mode=edit&ecole_id={$nEcoleId}");
return;

?>
