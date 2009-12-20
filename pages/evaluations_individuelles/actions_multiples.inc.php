<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = (ProfilManager::hasRight('eval_ind_edit') || ProfilManager::hasRight('eval_ind_delete'));
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Préparation des données
//==============================================================================

// Restriction sur l'annee scolaire courante
$sRestrictionAnneeScolaire =
	" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// Page de retour
$sRetour = $oForm->getValue('retour', $_GET, 'is_string', "");
// Action à effectuer (suppression ou edition multiple)
$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

$nEleveId = $oForm->getValue('eleve_id', $_GET, 'convert_int', -1);
$nClasseId = $oForm->getValue('classe_id', $_GET, 'convert_int', -1);
$nEvalCollId = $oForm->getValue('eval_col_id', $_GET, 'convert_int', -1);
$nOffsetDep = $oForm->getValue('offset_depart', $_GET, 'convert_int', -1);
$nCompetenceId = $oForm->getValue('competence_id', $_GET, 'convert_int', -1);

if($nEleveId != -1 || $nClasseId != -1 || $nEvalCollId != -1 || $nCompetenceId != -1 || $nOffsetDep != -1)
{
	// Création des liens de pagination
	$sEndLink = "";
	$sEndLink .= ($nEleveId != -1) ? "&amp;eleve_id={$nEleveId}" : "";
	$sEndLink .= ($nClasseId != -1) ? "&amp;classe_id={$nClasseId}" : "";
	$sEndLink .= ($nEvalCollId != -1) ? "&amp;eval_col_id={$nEvalCollId}" : "";
	$sEndLink .= ($nOffsetDep != -1) ? "&amp;offset_depart={$nOffsetDep}" : "";
	$sEndLink .= ($nCompetenceId != -1) ? "&amp;competence_id={$nCompetenceId}" : "";
}

$aEvalIndsToDel = isset($_POST['evals_inds_id']) ? $_POST['evals_inds_id'] : array();

if($aEvalIndsToDel == false)
{
	$oForm->setError('evals_inds_id', 'liste', "La liste des évaluations individuelles doit être remplit avec au moins une entrée.");
	// Rechargement
	header("Location: ?page=evaluations_individuelles" . ($sRetour != "") ? "&mode={$sRetour}{$sEndLink}" : "{$sEndLink}");
	return;
}

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

// ===== La liste des evaluations collectives à ce jour =====
$sQuery = <<< EOQ
	SELECT
		EVAL_COL_ID,
		CONCAT(CLASSE_NOM, ' - ', PERIODE_NOM, ' - ', DATE_FORMAT(EVAL_COL_DATE, '%d/%m/%Y'), ' - ', EVAL_COL_NOM) AS EVAL_COL_NOM
	FROM EVALUATIONS_COLLECTIVES
		INNER JOIN CLASSES
			ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PERIODES
			ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
	WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	ORDER BY PERIODE_NOM ASC, EVALUATIONS_COLLECTIVES.EVAL_COL_NOM
EOQ;
$aEvalCols = Database::fetchArray($sQuery);
// $aEvalCols[][COLONNE] = VALEUR

// ===== La liste des notes =====
$sQuery = <<< EOQ
	SELECT
		NOTE_ID,
		NOTE_NOM,
		NOTE_LABEL,
		NOTE_NOTE
	FROM NOTES
	ORDER BY NOTE_NOTE DESC
EOQ;
$aNotes = Database::fetchArray($sQuery);
// $aNotes[][COLONNE] = VALEUR

//==============================================================================
// Affichage de la page
//==============================================================================

if($sAction == "Suppression multiple"): ?>
	<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a><img src="<?php echo(URL_ICONS_16X16); ?>/blank.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Evaluations individuelles : Suppression multiple</h1>

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
			<input type="hidden" name="fin_lien_retour" value="<?php echo $sEndLink; ?>" />

			<?php foreach($aEvalInds as $aEvalInd): ?>
			<input type="hidden" name="evals_inds_id[]" value="<?php echo($aEvalInd['EVAL_IND_ID']); ?>" />
			<?php endforeach; ?>

			<input type="submit" name="action" value="Supprimer" />
			<input type="submit" name="action" value="Annuler" />
		</p>
	</form>
<?php elseif($sAction == "Edition multiple"): ?>
	<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a><img src="<?php echo(URL_ICONS_16X16); ?>/blank.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Evaluations individuelles : Edition multiple</h1>

	<?php if(Message::hasError() == true): ?>
	<ul class="form_error">
		<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
		<li><?php echo($sErrorMessage); ?></li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
	<form method="post" action="?page=evaluations_individuelles&amp;mode=edit_multiple_do">
		<fieldset>
			<legend>Edition des évaluations individuelles</legend>
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
		<table>
			<thead></thead>
			<tfoot></tfoot>
			<tbody>
				<tr>
					<td>Modifier la note des évaluations</td>
					<td>
						<select name="id_note">
							<option>-- Sélectionner une note --</option>
							<?php foreach($aNotes as $aNote): ?>
							<option value="<?php echo $aNote['NOTE_ID']; ?>"><?php echo $aNote['NOTE_LABEL']?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Modifier l'évaluation collective des évaluations</td>
					<td>
						<select name="id_eval_col">
							<option>-- Sélectionner une évaluation collective --</option>
							<?php foreach($aEvalCols as $aEvalCol): ?>
							<option value="<?php echo $aEvalCol['EVAL_COL_ID']; ?>"><?php echo $aEvalCol['EVAL_COL_NOM']; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="hidden" name="retour" value="<?php echo $sRetour; ?>" />
						<input type="hidden" name="fin_lien_retour" value="<?php echo $sEndLink; ?>" />

						<?php foreach($aEvalInds as $aEvalInd): ?>
						<input type="hidden" name="evals_inds_id[]" value="<?php echo($aEvalInd['EVAL_IND_ID']); ?>" />
						<?php endforeach; ?>

						<input type="submit" name="action" value="Editer" />
						<input type="submit" name="action" value="Annuler" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
<?php else: ?>
<!-- Rien de prévu à ce jour -->
<?php endif; ?>