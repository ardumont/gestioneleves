<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// matiere de la competence
$objForm->read('ID_DOMAINE', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ ID_DOMAINE !");
$objForm->testError0(null, 'blank',	"Il manque le domaine de la mati&eacute;re !");
$objForm->testError0(null, 'is_int',"L'id du domaine de la mati&eacute;re doit &ecirc;tre un entier!");
$nIdDomaine = $objForm->get(null);

// nom de la competence
$objForm->read('MATIERE_NOM', $_POST);
$objForm->testError0(null, 'exist',		"Il manque le champ MATIERE_NOM !");
$objForm->testError0(null, 'blank',		"Il manque le nom de la mati&egrave;re !");
$objForm->testError0(null, 'is_string',	"Le nom de la mati&egrave;re doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sMatiereNom = $objForm->get(null);

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
			"INSERT INTO MATIERES (MATIERE_NOM, ID_DOMAINE)" .
			"VALUES(" .
				Database::prepareString($sMatiereNom) . "," . 
				$nIdDomaine . 
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=matieres&mode=add");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=matieres&mode=add");
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
header("Location: ?page=matieres&mode=add");
return;

?>
