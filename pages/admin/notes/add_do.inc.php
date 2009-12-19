<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// nom de la periode
$oForm->read('NOTE_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ NOTE_NOM !");
$oForm->testError0(null, 'blank',     "Il manque le nom de la note !");
$oForm->testError0(null, 'is_string', "Le nom de la note doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sNoteNom = $oForm->get(null);

// label de la note
$oForm->read('NOTE_LABEL', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ NOTE_LABEL !");
$oForm->testError0(null, 'blank',     "Il manque le label de la note !");
$oForm->testError0(null, 'is_string', "Le label de la note doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sNoteLabel = $oForm->get(null);

// coefficient de la note
$oForm->read('NOTE_NOTE', $_POST);
$oForm->testError0(null, 'exist',	"Il manque le champ NOTE_NOTE !");
$oForm->testError0(null, 'blank',	"Il manque le coefficient de la note !");
$oForm->testError0(null, 'is_int',"Le coefficient de la note doit &ecirc;tre un entier !");
$sNoteNote = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($oForm->hasError() == true) break;

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
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=notes");
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
header("Location: ?page=notes&mode=add");
return;
