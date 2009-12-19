<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// matiere de la competence
$oForm->read('ID_CYCLE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ ID_CYCLE !");
$oForm->testError0(null, 'blank',	"Il manque le cycle du domaine !");
$oForm->testError0(null, 'is_int',"L'id du cycle doit &ecirc;tre un entier!");
$nIdCycle = $oForm->get(null);

// nom de la competence
$oForm->read('DOMAINE_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ DOMAINE_NOM !");
$oForm->testError0(null, 'blank',		"Il manque le nom du domaine !");
$oForm->testError0(null, 'is_string',	"Le nom du domaine &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sDomaineNom = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($oForm->hasError() == true) break;
		// insertion du domaine dans la table
		$sQuery =
			"INSERT INTO DOMAINES (DOMAINE_NOM, ID_CYCLE)" .
			"VALUES(" .
				Database::prepareString($sDomaineNom) . "," .
				$nIdCycle .
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=domaines&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=domaines&mode=add");
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
header("Location: ?page=domaines&mode=add");
return;
