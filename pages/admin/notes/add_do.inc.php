<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// nom de la periode
$objForm->read('NOTE_NOM', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ NOTE_NOM !");
$objForm->testError0(null, 'blank',     "Il manque le nom de la note !");
$objForm->testError0(null, 'is_string', "Le nom de la note doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sNoteNom = $objForm->get(null);

// label de la note
$objForm->read('NOTE_LABEL', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ NOTE_LABEL !");
$objForm->testError0(null, 'blank',     "Il manque le label de la note !");
$objForm->testError0(null, 'is_string', "Le label de la note doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sNoteLabel = $objForm->get(null);

// coefficient de la note
$objForm->read('NOTE_NOTE', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ NOTE_NOTE !");
$objForm->testError0(null, 'blank',	"Il manque le coefficient de la note !");
$objForm->testError0(null, 'is_int',"Le coefficient de la note doit &ecirc;tre un entier !");
$sNoteNote = $objForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($objForm->hasError() == true) break;

		// insertion de la nouvelle periode
		$sQuery =
			"INSERT INTO NOTES(NOTE_NOM, NOTE_LABEL, NOTE_NOTE) " .
			"VALUE(" .
				Database::prepareString($sNoteNom) . "," .
				Database::prepareString($sNoteLabel) . "," .
				Database::prepareString($sNoteNote) .
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=notes&mode=add");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=notes");
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
header("Location: ?page=notes&mode=add");
return;
