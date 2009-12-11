<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================
//
//$bHasRight = ProfilManager::hasRight('project_delete');
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

$sRetour = $oForm->getValue('retour', $_GET, 'is_string', "");

$aEvalIndsToDel = $_POST['evals_inds_id'];

//==============================================================================
// Actions du formulaire
//==============================================================================

$sQueryIdToDel = implode(",", $aEvalIndsToDel);

// Récupération des tâches à supprimer
// Récupère des informations complémentaires sur la tâche à supprimer
$sQuery = <<< EOQ
	SELECT
		ELEVE_NOM,
		CLASSE_NOM,
		NOTE_NOM,
		NOTE_LABEL,
		EVAL_IND_ID,
		EVAL_IND_COMMENTAIRE,
		CONCAT(PERIODE_NOM, ' - ', EVAL_COL_NOM) AS EVAL_COL_NOM,
		COMPETENCE_NOM,
		MATIERE_NOM,
		DOMAINE_NOM
	FROM EVALUATIONS_INDIVIDUELLES
		INNER JOIN NOTES
			ON EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID
		INNER JOIN ELEVES
			ON EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID
		INNER JOIN ELEVE_CLASSE
			ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
		INNER JOIN CLASSES
			ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN COMPETENCES
			ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
		INNER JOIN MATIERES
			ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
		INNER JOIN DOMAINES
			ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN EVALUATIONS_COLLECTIVES
			ON EVALUATIONS_COLLECTIVES.EVAL_COL_ID = EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL
		INNER JOIN PERIODES
			ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
	WHERE EVAL_IND_ID IN ({$sQueryIdToDel})
EOQ;
$aEvalInds = Database::fetchArray($sQuery);

// Si pas de suppression, on retourne sur la page de vue détaillée d'un projet
if(count($aEvalInds) == 0)
{
	// Rechargement
	header("Location: ?page=evaluations_individuelles" . ($sRetour != "") ? "&mode={$sRetour}" : "");
	return;
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
?>
<h1><img src="<?php echo(URL_ICONS_16X16); ?>/projet.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Evaluations individuelles : Suppression multiple</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=evaluations_individuelles&amp;mode=delete_multiple_do">
	<fieldset>
		<legend>Confirmation</legend>
		Etes-vous sûr de vouloir supprimer les évaluations individuelles suivantes ?
		<br />
		<table class="list_tree">
			<thead>
				<tr>
					<th>El&egrave;ves</th>
					<th>Classes</th>
					<th>Domaines</th>
					<th>Mati&egrave;res</th>
					<th>Comp&eacute;tences</th>
					<th>Notes</th>
				</tr>
			</thead>
			<tfoot></tfoot>
			<tbody>
				<?php foreach($aEvalInds as $nRowNum => $aEvalInd): ?>
				<tr class="level0_row<?php echo($nRowNum%2); ?>">
					<td><?php echo($aEvalInd['ELEVE_NOM']); ?></td>
					<td><?php echo($aEvalInd['CLASSE_NOM']); ?></td>
					<td><?php echo($aEvalInd['DOMAINE_NOM']); ?></td>
					<td><?php echo($aEvalInd['MATIERE_NOM']); ?></td>
					<td><?php echo($aEvalInd['COMPETENCE_NOM']); ?></td>
					<td title="<?php echo($aEvalInd['EVAL_IND_COMMENTAIRE']); ?>"><?php echo($aEvalInd['NOTE_NOM']); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</fieldset>
	<p>
		<input type="hidden" name="retour" value="<?php echo $sRetour; ?>" />
		<?php foreach($aEvalInds as $aEvalInd): ?>
		<input type="hidden" name="evals_inds_id[]" value="<?php echo($aEvalInd['EVAL_IND_ID']); ?>" />
		<?php endforeach; ?>

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
