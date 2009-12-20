<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('note_edit');
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

// la note a supprimer
$oForm->read('NOTE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"NOTE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la note !");
$oForm->testError0(null, 'convert_int',	"L'id de la note doit &ecirc;tre un entier !");
$nNoteId = $oForm->get(null);

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

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

		// mise a jour de la note
		$sQuery =
			"UPDATE NOTES " .
			" SET NOTE_NOM = " . Database::prepareString($sNoteNom) . "," .
			"     NOTE_LABEL = " . Database::prepareString($sNoteLabel) .
			" WHERE NOTE_ID = {$nNoteId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=notes");
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
header("Location: ?page=notes&mode=edit&note_id={$nNoteId}");
return;
