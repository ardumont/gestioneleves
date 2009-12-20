<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('import_xml_cycle');
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

// action du formulaire
$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

if ($_FILES['nom_fichier']['error'])
{
	switch($_FILES['nom_fichier']['error'])
	{
		case 1: // UPLOAD_ERR_INI_SIZE
			echo "Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !";
			break;
		case 2: // UPLOAD_ERR_FORM_SIZE
			echo "Le fichier dépasse la limite autorisée dans le formulaire HTML !";
			break;
		case 3: // UPLOAD_ERR_PARTIAL
			echo "L'envoi du fichier a été interrompu pendant le transfert !";
			break;
		case 4: // UPLOAD_ERR_NO_FILE
			echo "Le fichier que vous avez envoyé a une taille nulle !";
			break;
	}
	$sNomFichier = "";
	// Trouver une meilleure gestion de l'erreur
	die;
} else {
	// $_FILES['nom_fichier']['error'] vaut 0 soit UPLOAD_ERR_OK
	// ce qui signifie qu'il n'y a eu aucune erreur
	// nom du fichier a importer
	//$sNomFichier = $oForm->getValue('nom_fichier', $_FILES['nom_fichier'], 'is_string', "");
	$sNomFichier = $_FILES['nom_fichier']['tmp_name'];
}

//==============================================================================
// Actions du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'importer':
		if($oForm->hasError() == true) break;

		if($sNomFichier != "")
		{
			// importe les cycles/niveaux/domaines/matieres/competences
			$bRes = import_xml_cycle($sNomFichier);
		} else {
			$bRes = false;
		}
		// Rechargement
		header("Location: ?page=imports&mode=imports_xml&res=" . ($bRes ? "ok" : "ko"));
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=imports&mode=imports_xml");
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
header("Location: ?page=imports&mode=imports_xml");
return;
