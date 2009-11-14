<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// action du formulaire
$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// nom du fichier a importer
//$sNomFichier = $oForm->getValue('nom_fichier', $_FILES['nom_fichier'], 'is_string', "");
$sNomFichier = $_FILES['nom_fichier']['tmp_name'];

//==============================================================================
// Actions du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'importer':
		if($oForm->hasError() == true) break;

		// importe les cycles/niveaux/domaines/matieres/competences
		$bRes = import_cndmcs($sNomFichier);
		// Rechargement
		header("Location: ?page=imports&mode=imports_csv&res=" . ($bRes ? "ok" : "ko"));
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=imports&mode=imports_csv");
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
header("Location: ?page=imports&mode=imports_csv");
return;
