<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// Recuperation des ids de restrictions de recherche
$oForm->read('cle', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ nom !");
$oForm->testError0(null, 'blank',       "Il manque le nom du menu à cacher !");
$oForm->testError0(null, 'convert_string', "La clé du menu à cacher doit être une chaine de caractères !");
$sCleMenu = $oForm->get(null, -1);

$nProfesseurId = $_SESSION['PROFESSEUR_ID'];

// Recuperation des ids de restrictions de recherche
$oForm->read('statut_hidden', $_POST);
$bStatutHidden = $oForm->get(null, false);

//==============================================================================
// Actions du formulaire
//==============================================================================

$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PROFESSEURS
	WHERE PROFESSEUR_ID={$nProfesseurId}
EOQ;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant du professeur \"{$nProfesseurId}\" n'est pas valide !");

// Traitement d'erreur, on s'arrete
if($oForm->hasError() == true || $sCleMenu == -1)
{
	die;
}

//==============================================================================
// Traitement des donnees
//==============================================================================

// Préparation de la requete
$sCleMenuQuery = Database::prepareString($sCleMenu);
$sQuery = <<< EOQ
	DELETE FROM HIDDEN_OBJECTS
	WHERE ID_PROFESSEUR = {$nProfesseurId}
	AND HO_LIBELLE = {$sCleMenuQuery}
EOQ;
$bRes = Database::execute($sQuery);

echo "$bRes $bStatutHidden ";
// Si un commentaire a été saisi
if($bStatutHidden == 'true')// Le menu est caché, on l'ajoute dans la liste des objets à cacher pour le professeur connecté
{
	$sQuery = <<< ____EOQ
		INSERT INTO HIDDEN_OBJECTS(ID_PROFESSEUR, HO_LIBELLE)
		VALUES({$nProfesseurId}, {$sCleMenuQuery})
____EOQ;
	Database::execute($sQuery);
}

?>