<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('livret_list');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

// Restriction sur l'annee scolaire courante
$sRestrictionAnneeScolaire =
	" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// Recuperation des ids de restrictions de recherche
$oForm->read('eleve_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ eleve_id !");
$oForm->testError0(null, 'blank',       "Il manque l'id de l'élève !");
$oForm->testError0(null, 'convert_int', "L'identifiant de l'élève doit être un entier !");
$nEleveId = $oForm->get(null, -1);

$oForm->read('periode_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ periode_id !");
$oForm->testError0(null, 'blank',       "Il manque l'id de la période !");
$oForm->testError0(null, 'convert_int', "L'identifiant de la période doit être un entier !");
$nPeriodeId = $oForm->get(null, -1);

$oForm->read('classe_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ classe_id !");
$oForm->testError0(null, 'blank',       "Il manque l'id de la classe !");
$oForm->testError0(null, 'convert_int', "L'identifiant de la classe doit être un entier !");
$nClasseId = $oForm->get(null, -1);

$oForm->read('commentaire_hidden', $_POST);
$sCommentaireHidden = $oForm->get(null, -1);

$oForm->read('commentaire_saisie', $_POST);
$sCommentaire = $oForm->get(null, -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

// ===== Vérification des valeurs =====

$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM ELEVES
	WHERE ELEVE_ID = {$nEleveId}
EOQ;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant de l'élève \"{$nEleveId}\" n'est pas valide !");

$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PERIODES
	WHERE PERIODE_ID = {$nPeriodeId}
EOQ;

$oForm->readArray('query2', Database::fetchOneRow($sQuery));
$oForm->testError0('query2.EXIST', 'exist', "L'identifiant de la période \"{$nPeriodeId}\" n'est pas valide !");

$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM CLASSES
	WHERE CLASSE_ID = {$nClasseId}
EOQ;

$oForm->readArray('query3', Database::fetchOneRow($sQuery));
$oForm->testError0('query3.EXIST', 'exist', "L'identifiant de la classe \"{$nClasseId}\" n'est pas valide !");

// Traitement d'erreur, on s'arrete
if($oForm->hasError() == true)
{
	die;
}

//==============================================================================
// Traitement des donnees
//==============================================================================

// Si un commentaire a été saisi
if($sCommentaire != -1 && strcmp($sCommentaire, $sCommentaireHidden) != 0)
{
	// Préparation des chaines pour la requete
	$sCommentaireQuery = Database::prepareString($sCommentaire);

	$sQuery = <<< ____EOQ
		SELECT COMMENTAIRE_ID
		FROM COMMENTAIRES
		WHERE ID_ELEVE = {$nEleveId}
		AND ID_PERIODE = {$nPeriodeId}
		AND ID_CLASSE =  {$nClasseId}
____EOQ;
	$nIdCommentaire = Database::fetchOneValue($sQuery);

	if($nIdCommentaire == false)
	{
		$sQuery = <<< ________EOQ
			INSERT INTO COMMENTAIRES(ID_ELEVE, ID_PERIODE, ID_CLASSE, COMMENTAIRE_VALEUR)
			VALUES({$nEleveId}, {$nPeriodeId}, {$nClasseId}, {$sCommentaireQuery})
________EOQ;
	} else {
		$sQuery = <<< ________EOQ
			UPDATE COMMENTAIRES
			SET COMMENTAIRE_VALEUR = {$sCommentaireQuery}
			WHERE COMMENTAIRE_ID = {$nIdCommentaire}
________EOQ;
	}
	$bRes = Database::execute($sQuery);
}

?>