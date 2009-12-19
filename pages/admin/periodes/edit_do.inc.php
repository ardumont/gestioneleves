<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// la periode a mettre a jour
$oForm->read('PERIODE_ID', $_POST);
$oForm->testError0(null, 'exist',			"Il manque le champ \"PERIODE_ID\" !");
$oForm->testError0(null, 'blank',			"Il manque l'id de la p&eacute;riode !");
$oForm->testError0(null, 'convert_int',	"L'id de la p&eacute;riode doit &ecirc;tre un entier !");
$nPeriodeId = $oForm->get(null);

// nom de la periode
$oForm->read('PERIODE_NOM', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ PERIODE_NOM !");
$oForm->testError0(null, 'blank',     "Il manque le nom de la p&eacute;riode !");
$oForm->testError0(null, 'is_string', "Le nom de la p&eacute;riode doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sPeriodeNom = $oForm->get(null);

// date de debut
$oForm->read('PERIODE_DATE_DEBUT', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ PERIODE_DATE_DEBUT !");
$oForm->testError0(null, 'blank',     "Il manque la date de d&eacute;but !");
$oForm->testError0(null, 'is_string', "La date de d&eacute;but doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sPeriodeDateDebut = $oForm->get(null);

// date de fin
$oForm->read('PERIODE_DATE_FIN', $_POST);
$oForm->testError0(null, 'exist',     "Il manque le champ PERIODE_DATE_FIN !");
$oForm->testError0(null, 'blank',     "Il manque la date de fin !");
$oForm->testError0(null, 'is_string', "La date de fin doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sPeriodeDateFin = $oForm->get(null);

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ----------
	case 'modifier':
		if($oForm->hasError() == true) break;

		// mise a jour des valeurs du cycle
		$sQuery =
			"UPDATE PERIODES " .
			" SET PERIODE_NOM = " . Database::prepareString($sPeriodeNom) . "," .
			"     PERIODE_DATE_DEBUT = " . Database::prepareString($sPeriodeDateDebut) . "," .
			"     PERIODE_DATE_FIN = " . Database::prepareString($sPeriodeDateFin) .
			" WHERE PERIODE_ID = {$nPeriodeId}";
		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=periodes");
		return;
	break;

	// ----------
	case 'annuler':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=periodes");
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
header("Location: ?page=periodes&mode=edit&periode_id={$nPeriodeId}");
return;
