<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('matiere_add');
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
$oForm->read('ID_DOMAINE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ ID_DOMAINE !");
$oForm->testError0(null, 'blank',	"Il manque le domaine de la mati&eacute;re !");
$oForm->testError0(null, 'is_int',"L'id du domaine de la mati&eacute;re doit &ecirc;tre un entier!");
$nIdDomaine = $oForm->get(null);

// nom de la competence
$oForm->read('MATIERE_NOM', $_POST);
$oForm->testError0(null, 'exist',		"Il manque le champ MATIERE_NOM !");
$oForm->testError0(null, 'blank',		"Il manque le nom de la mati&egrave;re !");
$oForm->testError0(null, 'is_string',	"Le nom de la mati&egrave;re doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sMatiereNom = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($oForm->hasError() == true) break;

		// Insertion de la matiere dans la table
		$sQuery =
			"INSERT INTO MATIERES (MATIERE_NOM, ID_DOMAINE)" .
			"VALUES(" .
				Database::prepareString($sMatiereNom) . "," .
				$nIdDomaine .
			")";
		Database::execute($sQuery);

		// Rechargement de la liste des eleves
		header("Location: ?page=matieres&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=matieres&mode=add");
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
header("Location: ?page=matieres&mode=add");
return;
