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
$objForm->read('PERIODE_NOM', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ PERIODE_NOM !");
$objForm->testError0(null, 'blank',     "Il manque le nom de la p&eacute;riode !");
$objForm->testError0(null, 'is_string', "Le nom de la p&eacute;riode doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sPeriodeNom = $objForm->get(null);

// date de debut
$objForm->read('PERIODE_DATE_DEBUT', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ PERIODE_DATE_DEBUT !");
$objForm->testError0(null, 'blank',     "Il manque la date de d&eacute;but !");
$objForm->testError0(null, 'is_string', "La date de d&eacute;but doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sPeriodeDateDebut = $objForm->get(null);

// date de fin
$objForm->read('PERIODE_DATE_FIN', $_POST);
$objForm->testError0(null, 'exist',     "Il manque le champ PERIODE_DATE_FIN !");
$objForm->testError0(null, 'blank',     "Il manque la date de fin !");
$objForm->testError0(null, 'is_string', "La date de fin doit &ecirc;tre une cha&icirc;ne de caract&egrave;s !");
$sPeriodeDateFin = $objForm->get(null);

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
			"INSERT INTO PERIODES(PERIODE_NOM, PERIODE_DATE_DEBUT, PERIODE_DATE_FIN) " .
			"VALUE(" .
				Database::prepareString($sPeriodeNom) . "," .
				Database::prepareString($sPeriodeDateDebut) . "," .
				Database::prepareString($sPeriodeDateFin) .
			")";
		Database::execute($sQuery);

		// rechargement de la liste des eleves
		header("Location: ?page=periodes&mode=add");
		return;
	break;
	
	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=periodes&mode=add");
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
header("Location: ?page=periodes&mode=add");
return;

?>
