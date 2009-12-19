<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

//$bHasRight = ProfilManager::hasRight('profil_delete');
//if($bHasRight == false)
//{
//	// Redirection
//	header("Location: ?page=no_rights");
//	return;
//}

//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

$oForm->read('profil_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque l'identifiant du profil !");
$oForm->testError0(null, 'blank',       "Il manque l'identifiant du profil !");
$oForm->testError0(null, 'convert_int', "L'identifiant du profil doit être un entier !");
$nProfilId = $oForm->get(null);

// ===== Vérification des valeurs =====

// S'il y a eu une erreur sur l'id, ça ne sert à rien d'aller plus loin.
if($oForm->hasError('profil_id') == true)
{
	Message::addErrorFromFormValidation($oForm->getError());

	// Rechargement
	header("Location: ?page=profils");
	return;
}

$sQuery = <<< _EOQ_
	SELECT
		1 EXIST,
		COUNT(USER_PROFIL_ID) USER_COUNT
	FROM PROFILS
		LEFT OUTER JOIN USERS ON USER_PROFIL_ID = PROFIL_ID
	WHERE PROFIL_ID = {$nProfilId}
	GROUP BY PROFIL_ID
_EOQ_;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant du profil \"{$nProfilId}\" n'est pas valide !");

$oForm->testError1('profil_id',    		'min_value_equal', 10, "Ce profil est un profil système. Il ne peut pas être supprimé !");
$oForm->testError1('query1.USER_COUNT', 'equal', 			0, "Des utilisateurs utilisent ce profil. Suppression impossible !");

//==============================================================================
// Actions du formulaire
//==============================================================================

switch(strtoupper($sAction))
{
	case 'SUPPRIMER':
		if($oForm->hasError() == true) break;

		// La suppression dans la table PROFILS_REL_RIGHTS se fait via
		// le "ON DELETE CASCADE" de la contrainte FK_REL_PROFILS
		$sQuery = <<< _EOQ_
			DELETE FROM PROFILS
			WHERE PROFIL_ID = {$nProfilId}
_EOQ_;

		Database::execute($sQuery);

		// Rechargement
		header("Location: ?page=profils");
		return;
	break;

	// ----------
	case 'ANNULER':
		$oForm->clearError();

		// Rechargement
		header("Location: ?page=profils");
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
Message::addErrorFromFormValidation($objForm->getError());

// Rechargement
header("Location: ?page=profils&mode=delete&profil_id={$nProfilId}");
return;
?>