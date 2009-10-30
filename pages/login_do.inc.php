<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objFormAuth = new FormValidation();

$sAction = $objFormAuth->getValue('action', $_POST, 'is_string', "");

$objFormAuth->read('professeur_id', $_POST);
$objFormAuth->testError0(null, 'exist',       "Il manque le champ \"professeur_id\" !");
$objFormAuth->testError0(null, 'blank',       "Il manque l'identifiant du professeur !");
$objFormAuth->testError0(null, 'convert_int', "L'identifiant du professeur doit etre un entier !");
$nProfesseurId  = $objFormAuth->get(null, -1);

$objFormAuth->read('professeur_password', $_POST);
$objFormAuth->testError0(null, 'exist',     "Il manque le champ \"professeur_password\" !");
$objFormAuth->testError0(null, 'blank',     "Il manque le mot de passe du professeur !");
$objFormAuth->testError0(null, 'is_string', "Le mot de passe du professeur doit être une chaîne de caractères !");
$sUserPassword = $objFormAuth->get(null);

//==============================================================================
// ===== Vérification des valeurs =====
//==============================================================================

if($objFormAuth->hasError() == false)
{
	$sQuery = "SELECT 1 " .
			  " FROM PROFESSEURS" .
			  " WHERE PROFESSEUR_ID = {$nProfesseurId} " .
			  "   AND PROFESSEUR_PWD = " . Database::prepareString(md5($sUserPassword));
	$nOk = Database::fetchOneValue($sQuery);

	if($nOk === false)
	{
		Message::addError("L'identifiant et/ou le mot de passe de l'utilisateur ne sont pas correct(s) !");		
	}

	if(Message::hasError() == true)
	{
		// Rechargement
		header("Location: ".SITE_URL."/");
		return;
	}
}

//==============================================================================
// Actions du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	case 'valider':
		if($objFormAuth->hasError() == true) break;

		$sQuery =
			" SELECT PROFESSEUR_NOM " .
			" FROM PROFESSEURS " .
			" WHERE PROFESSEUR_ID = {$nProfesseurId}";
		$sProfesseurNom = Database::fetchOneValue($sQuery);

		// stocke les donnees en session
		$_SESSION['PROFESSEUR_ID'] = $nProfesseurId;
		$_SESSION['PROFESSEUR_NOM'] = $sProfesseurNom;
		 
		// Rechargement
		header("Location: ".SITE_URL."/");
		return;
		break;

	// ----------
	default:
		$objFormAuth->clearError();
		Message::addError("L'action \"{$sAction}\" est inconnue !");
		break;
}

// On stocke toutes les erreurs de formulaire.
Message::addErrorFromFormValidation($objFormAuth->getError());

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
Message::addErrorFromFormValidation($objFormAuth->getError());

// Rechargement
header("Location: ".SITE_URL."/");
return;
?>