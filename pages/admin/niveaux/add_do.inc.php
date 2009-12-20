<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('niveau_add');
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

// matiere de la competence
$oForm->read('ID_CYCLE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ ID_CYCLE !");
$oForm->testError0(null, 'blank',	"Il manque l'id du cycle !");
$oForm->testError0(null, 'is_int',"L'id du cycle doit &ecirc;tre un entier!");
$nIdCycle = $oForm->get(null);

// nom du niveau
$oForm->read('NIVEAU_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ NIVEAU_NOM !");
$oForm->testError0(null, 'blank',		"Il manque le nom du niveau !");
$oForm->testError0(null, 'is_string',	"Le nom du niveau doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sNiveauNom = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	case 'ajouter':
		if($oForm->hasError() == true) break;

		// insertion du niveau
		$sQuery =
			"INSERT INTO NIVEAUX(NIVEAU_NOM, ID_CYCLE)" .
			"VALUES(" .
				Database::prepareString($sNiveauNom) . "," .
				$nIdCycle .
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=niveaux&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=niveaux");
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
header("Location: ?page=niveaux&mode=add");
return;
