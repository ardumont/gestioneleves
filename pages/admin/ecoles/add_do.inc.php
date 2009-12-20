<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('ecole_add');
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

// si le champ ECOLE_NOM est specifie alors
// il s'agit d'un ajout d'un nouvel eleve
$oForm->read('ECOLE_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ ECOLE_NOM !");
$oForm->testError0(null, 'blank',     "Il manque le nom de la nouvelle &eacute;cole !");
$oForm->testError0(null, 'is_string', "Le nom de l'&eacute;cole doit &ecirc;tre une cha&icirc;ene de caract&egrave;res !");
$sEcoleNom = $oForm->get(null);

// si le champ ECOLE_VILLE est specifie alors
// il s'agit d'un ajout d'un nouvel eleve
$oForm->read('ECOLE_VILLE', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ ECOLE_VILLE !");
$oForm->testError0(null, 'blank',     "Il manque la ville de la nouvelle &eacute;cole !");
$oForm->testError0(null, 'is_string', "Le nom de la ville doit &ecirc;tre une cha&icirc;ene de caract&egrave;res !");
$sEcoleVille = $oForm->get(null);

// si le champ ECOLE_DEPARTEMENT est specifie alors
// il s'agit d'un ajout d'un nouvel eleve
$oForm->read('ECOLE_DEPARTEMENT', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ ECOLE_DEPARTEMENT !");
$oForm->testError0(null, 'blank',     "Il manque le d&eacute;partement de la nouvelle &eacute;cole !");
$oForm->testError0(null, 'is_int', 	"Le d&eacute;partement de l'&eacute;cole doit &ecirc;tre un entier !");
$sEcoleDep = $oForm->get(null);

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
			"INSERT INTO ECOLES (ECOLE_NOM, ECOLE_VILLE, ECOLE_DEPARTEMENT)" .
			"VALUES(".
				Database::prepareString($sEcoleNom) . "," .
				Database::prepareString($sEcoleVille) . "," .
				Database::prepareString($sEcoleDep).
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=ecoles");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=ecoles");
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
header("Location: ?page=ecoles&mode=add");
return;
