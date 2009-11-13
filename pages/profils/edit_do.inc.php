<?php
//==============================================================================
// Préparation des données
//==============================================================================

// On récupère l'id de l'utilisateur rangé en session
$nUserId = $_SESSION['PROFESSEUR_ID'];

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

$oForm->read('user_pwd', $_POST);
$oForm->testValue0(null, 'exist',     "Il manque le champ user_pwd !");
$oForm->testValue0(null, 'blank',     "Il manque le mot de passe !");
$oForm->testError0(null, 'is_string', "Le mot de passe doit être une chaîne de caractères !");
$sPwd = $oForm->get(null);

$oForm->read('user_pwd_conf', $_POST);
$oForm->testError0(null, 'exist',     	"Il manque le champ user_pwd_conf !");
$oForm->testError0(null, 'blank',     	"Il manque la confirmation du mot de passe !");
$oForm->testError0(null, 'is_string', 	"La confirmation du mot de passe doit être une chaîne de caractères !");
$oForm->testError1(null, 'equal', $sPwd, 	"Les deux mots de passe renseignés ne sont pas les mêmes !");
$sPwdConf = $oForm->get(null);

$oForm->read('professeur_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ professeur_id !");
$oForm->testError0(null, 'blank',       "Il manque l'id du professeur !");
$oForm->testError0(null, 'convert_int', "L'identifiant du professeur doit être un entier !");
$nProfesseurId = $oForm->get(null);

// ===== Vérification des valeurs =====

$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PROFESSEURS
	WHERE PROFESSEUR_ID = {$nProfesseurId}
EOQ;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant du professeur \"{$nProfesseurId}\" n'est pas valide !");

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtoupper($sAction))
{
	// ----------
	case 'VALIDER':
		if($oForm->hasError() == true) break;

		// ===== Mise à jour de la BDD =====

		// Création de la requête de mise à jour du mot de passe s'il n'est pas vide

		if($sPwd != null)
		{
			// Initialisation des variables SQL
			$sSqlPwd = Database::prepareString(md5($sPwd));

			$sQuery = <<< ____________EOQ
				UPDATE PROFESSEURS
				SET PROFESSEUR_PWD = {$sSqlPwd}
				WHERE PROFESSEUR_ID = {$nProfesseurId}
____________EOQ;

			Database::execute($sQuery);
		}

		// Rechargement
		header("Location: ?page=profils&mode=edit");
		return;
	break;

	// ----------
	case 'ANNULER':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=home");
		return;
	break;

	// ----------
	default:
		$oForm->clearError();

		Message::addError("L'action \"{$sAction}\" est inconnue !");
}

//==============================================================================
// Traitement des données
//==============================================================================

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

// On stocke toutes les erreurs de formulaire.
Message::addErrorFromFormValidation($oForm->getError());

// Rechargement
header("Location: ?page=profils&mode=edit");
return;
