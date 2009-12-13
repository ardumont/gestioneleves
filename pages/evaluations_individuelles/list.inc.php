<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

$nOffset = 30;

//restriction sur l'annee scolaire courante
$sRestrictionAnneeScolaire =
	" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// Pagination
$nOffsetDep = $oForm->getValue('offset_depart', $_GET, 'convert_int', 0);
$nOffsetFin = $nOffsetDep + $nOffset;

// Recuperation des ids de restrictions de recherche
$nEleveId = $oForm->getValue('eleve_id', $_GET, 'convert_int', -1);
$nClasseId = $oForm->getValue('classe_id', $_GET, 'convert_int', -1);
$nEvalCollId = $oForm->getValue('eval_col_id', $_GET, 'convert_int', -1);
$nCompetenceId = $oForm->getValue('competence_id', $_GET, 'convert_int', -1);

if($nEleveId == -1)
{
	// Recuperation des ids de restrictions de recherche
	$nEleveId = $oForm->getValue('ELEVE_ID', $_POST, 'convert_int', -1);
}

if($nClasseId == -1)
{
	$nClasseId = $oForm->getValue('CLASSE_ID', $_POST, 'convert_int', -1);
}

if($nEvalCollId == -1)
{
	$nEvalCollId = $oForm->getValue('EVAL_COL_ID', $_POST, 'convert_int', -1);
}

if($nCompetenceId == -1)
{
	$nCompetenceId = $oForm->getValue('COMPETENCE_ID', $_POST, 'convert_int', -1);
}

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des evaluations collectives à ce jour =====
$sQuery = <<< EOQ
	SELECT
		EVAL_COL_ID,
		CONCAT(PERIODE_NOM, ' - ', EVAL_COL_NOM) AS EVAL_COL_NOM,
		EVAL_COL_DESCRIPTION,
		DATE_FORMAT(EVAL_COL_DATE, '%d/%m/%Y') AS EVAL_COL_DATE,
		PERIODE_NOM,
		CLASSE_NOM,
		CLASSE_ANNEE_SCOLAIRE
	FROM EVALUATIONS_COLLECTIVES
		INNER JOIN CLASSES
			ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PERIODES
			ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
	WHERE 1=1
	{$sRestrictionAnneeScolaire}
	ORDER BY PERIODE_NOM ASC, EVALUATIONS_COLLECTIVES.EVAL_COL_NOM
EOQ;
$aEvalCols = Database::fetchArray($sQuery);
// $aEvalCols[][COLONNE] = VALEUR

// ===== La liste des eleves du professeur pour l'annee courante =====
$sQuery = <<< EOQ
	SELECT DISTINCT
		ELEVE_ID,
		CONCAT(CLASSE_ANNEE_SCOLAIRE, ' - ', CLASSE_NOM, ' - ', ELEVE_NOM) AS ELEVE_NOM
	FROM ELEVES
		INNER JOIN ELEVE_CLASSE
			ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
		INNER JOIN CLASSES
			ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
	WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
EOQ;
$aEleves = Database::fetchArray($sQuery);
// $aEleves[][COLONNE] = VALEUR

// ===== La liste des classes pour l'annee scolaire du professeur logge =====
$sQuery = <<< EOQ
	SELECT
		CLASSE_ID,
		CLASSE_NOM,
		CLASSE_ANNEE_SCOLAIRE
	FROM CLASSES
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN PROFESSEURS
			ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
	WHERE PROFESSEURS.PROFESSEUR_ID = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC
EOQ;
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

$sQuery = <<< EOQ
	SELECT
		DISTINCT COMPETENCE_ID,
		COMPETENCE_NOM
	FROM EVALUATIONS_INDIVIDUELLES
		INNER JOIN COMPETENCES
			ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
		INNER JOIN EVALUATIONS_COLLECTIVES
			ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
		INNER JOIN CLASSES
			ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
	WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	ORDER BY COMPETENCE_NOM
EOQ;
$aCompetences = Database::fetchArray($sQuery);
// $aCompetences[][COLONNE] = VALEUR

// criteres de recherche
$sQueryEvalCollId = ($nEvalCollId != -1) ? " AND EVALUATIONS_COLLECTIVES.EVAL_COL_ID = {$nEvalCollId} " : "";
$sQueryClasseId = ($nClasseId != -1) ? " AND CLASSES.CLASSE_ID = {$nClasseId} " : "";
$sQueryElevesId = ($nEleveId != -1) ? " AND ELEVES.ELEVE_ID = {$nEleveId} " : "";
$sQueryCompetenceId  = ($nCompetenceId != -1) ? " AND COMPETENCES.COMPETENCE_ID = {$nCompetenceId} " : "";

if($nEleveId != -1 || $nClasseId != -1 || $nEvalCollId != -1 || $nCompetenceId != -1)
{
	// ===== La liste des evaluations individuelles a ce jour =====
	$sQuery = <<< ____EOQ
		SELECT
			COUNT(*)
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
		WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
		{$sRestrictionAnneeScolaire}
		{$sQueryElevesId}
		{$sQueryEvalCollId}
		{$sQueryClasseId}
		{$sQueryCompetenceId}
		ORDER BY ELEVE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
____EOQ;
	$nRowCount = Database::fetchOneValue($sQuery);

	// ===== La liste des evaluations individuelles a ce jour =====
	$sQuery = <<< ____EOQ
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
		WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
		{$sRestrictionAnneeScolaire}
		{$sQueryElevesId}
		{$sQueryEvalCollId}
		{$sQueryClasseId}
		{$sQueryCompetenceId}
		ORDER BY ELEVE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
		LIMIT {$nOffsetDep}, {$nOffsetFin}
____EOQ;
	$aEvalInds = Database::fetchArray($sQuery);
	// $aEvalInds[][COLONNE] = VALEUR
} else {
	$aEvalInds = array();
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

if($nEleveId != -1 || $nClasseId != -1 || $nEvalCollId != -1 || $nCompetenceId != -1)
{
	// Création des liens de pagination
	$sEndLink = "";
	$sEndLink .= ($nEleveId != -1) ? "&amp;eleve_id={$nEleveId}" : "";
	$sEndLink .= ($nClasseId != -1) ? "&amp;classe_id={$nClasseId}" : "";
	$sEndLink .= ($nEvalCollId != -1) ? "&amp;eval_col_id={$nEvalCollId}" : "";
	$sEndLink .= ($nCompetenceId != -1) ? "&amp;competence_id={$nCompetenceId}" : "";

	$sLinkPrec = "?page=evaluations_individuelles&amp;offset_depart=" . ($nOffsetDep - $nOffset) . "{$sEndLink}";
	$sLinkSucc = "?page=evaluations_individuelles&amp;offset_depart={$nOffsetFin}{$sEndLink}";

	// Calcule le nombre de liens à afficher pour se déplacer rapidement dans la pagination
	$nNbLinks =  (int) ($nRowCount / $nOffset);
	// Calcule notre place dans ce nombre de liens
	$nEmplacementLien = ($nOffsetDep % $nRowCount) / $nOffset;

	// Création des liens de pagination
	foreach(range(0, $nNbLinks) as $i)
	{
		$aLinks[] = "?page=evaluations_individuelles&amp;offset_depart=" . ($nOffset * $i) . "{$sEndLink}";
	}
}
//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des évaluations individuelles de l'année courante</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<div id="help" style="display: none;">
	<table class="formulaire">
		<caption>Fonctionnement</caption>
		<tr>
			<td>
				Par défaut, cette page affiche toutes les évaluations individuelles saisies à ce jour par le professeur connecté.<br />
				Vous pouvez toutefois ne filtrer que par classe ou par élève.<br />
				Pour cela, sélectionner une classe ou un élève puis cliquer sur le bouton <i>Rechercher</i>.<br />
				Attention, toutefois, si l'élève n'appartient pas à la classe, aucun résultat ne s'affichera.
				<br />
				Par ailleurs, si l'affichage dépasse les <?php echo $nOffset; ?> lignes, des liens "précédent" et "suivant" apparaîssent pour afficher les <?php $nOffset; ?> éléments précédents ou suivants.
				<br />&nbsp;
			</td>
		</tr>
	</table>
</div>
<form method="post" action="?page=evaluations_individuelles" name="formulaire_eval_ind" id="formulaire_eval_ind">
	<table class="formulaire">
		<caption>Critères de recherche</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Evaluations collectives</td>
				<td>
					<select name="EVAL_COL_ID" onchange="document.getElementById('formulaire_eval_ind').submit();">
						<option value="-1">-- Sélectionner une évaluation collective --</option>
						<?php foreach($aEvalCols as $aEvalCol): ?>
							<option value="<?php echo($aEvalCol['EVAL_COL_ID']); ?>"<?php echo($aEvalCol['EVAL_COL_ID'] == $nEvalCollId ? ' selected="selected"' :''); ?>><?php echo($aEvalCol['EVAL_COL_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Classes</td>
				<td>
					<select name="CLASSE_ID" onchange="document.getElementById('formulaire_eval_ind').submit();">
						<option value="-1">-- Sélectionner une classe --</option>
						<?php foreach($aClasses as $aClasse): ?>
							<option value="<?php echo($aClasse['CLASSE_ID']); ?>"<?php echo($aClasse['CLASSE_ID'] == $nClasseId ? ' selected="selected"' :''); ?>><?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']. " - " . $aClasse['CLASSE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Elèves</td>
				<td>
					<select name="ELEVE_ID" onchange="document.getElementById('formulaire_eval_ind').submit();">
						<option value="-1">-- Sélectionner un élève --</option>
						<?php foreach($aEleves as $aEleve): ?>
							<option value="<?php echo($aEleve['ELEVE_ID']); ?>"<?php echo($aEleve['ELEVE_ID'] == $nEleveId ? ' selected="selected"' :''); ?>><?php echo($aEleve['ELEVE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Compétences</td>
				<td>
					<select name="COMPETENCE_ID" onchange="document.getElementById('formulaire_eval_ind').submit();">
						<option value="-1">-- Sélectionner une compétence --</option>
						<?php foreach($aCompetences as $aCompetence): ?>
							<option value="<?php echo($aCompetence['COMPETENCE_ID']); ?>"<?php echo($aCompetence['COMPETENCE_ID'] == $nCompetenceId ? ' selected="selected"' :''); ?>><?php echo($aCompetence['COMPETENCE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="action" value="Rechercher" /></td>
			</tr>
		</tbody>
	</table>
</form>
<?php if(count($aEvalInds) <= 0): ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucun critère de recherche n'a été saisi ou aucune évaluation individuelle
			n'a été saisie à ce jour pour ces critères.<br />
			<a href="?page=evaluations_individuelles&amp;mode=add">Ajouter une évaluation individuelle</a>
		</td>
	</tr>
</table>
<?php else: ?>
<a href="javascript:void(0);" onclick="$('.evals_inds_id').attr('checked', 'checked');">Sélectionner tout</a>&nbsp;
<a href="javascript:void(0);" onclick="$('.evals_inds_id').removeAttr('checked');">Désélectionner tout</a>
<form method="post" action="?page=evaluations_individuelles&amp;mode=delete_multiple">
	<table class="list_tree">
		<caption>
			<strong>
				<?php echo ($nOffsetDep > 0) ? '<a href="' . $sLinkPrec . '">précédent</a>&nbsp;': ''; ?>
				Liste des évaluations individuelles (<?php echo "{$nOffsetDep} - {$nOffsetFin}"; ?>)&nbsp;
				<?php echo ($nOffsetFin < $nRowCount) ? '<a href="' . $sLinkSucc . '">suivant</a>' : ''; ?>
				&nbsp;
				<?php foreach($aLinks as $i => $sLink): ?>
					<?php if(($i * $nOffset) == $nOffsetDep): ?>
						<?php echo ($i+1); ?>
					<?php else: ?>
						<a href="<?php echo ($sLink); ?>"><?php echo ($i+1); ?></a>&nbsp;
					<?php endif; ?>
				<?php endforeach; ?>
			</strong>
		</caption>
		<thead>
			<tr>
				<th><a href="?page=evaluations_individuelles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
				<th>Elèves</th>
				<th>Classes</th>
				<th>Evaluations<br />collectives</th>
				<th>Matières</th>
				<th>Compétences</th>
				<th>Notes</th>
				<th></th>
				<th colspan="3">Actions</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><a href="?page=evaluations_individuelles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
				<th colspan="10">
					<input type="submit" name="suppression_multiple" value="Suppression multiple" />
				</th>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach($aEvalInds as $nRowNum => $aEvalInd): ?>
			<tr class="level0_row<?php echo($nRowNum%2); ?>">
				<td></td>
				<th><?php echo($aEvalInd['ELEVE_NOM']); ?></th>
				<td><?php echo($aEvalInd['CLASSE_NOM']); ?></td>
				<td><?php echo($aEvalInd['EVAL_COL_NOM']); ?></td>
				<td><?php echo($aEvalInd['MATIERE_NOM']); ?></td>
				<td><?php echo($aEvalInd['COMPETENCE_NOM']); ?></td>
				<td title="<?php echo($aEvalInd['NOTE_NOM'] . (($aEvalInd['EVAL_IND_COMMENTAIRE'] != null) ? " - '" . $aEvalInd['EVAL_IND_COMMENTAIRE'] . "'" : "")); ?>"><?php echo($aEvalInd['NOTE_LABEL']); ?></td>
				<td></td>
				<!-- Edition -->
				<td>
					<a href="?page=evaluations_individuelles&amp;mode=edit&amp;eval_ind_id=<?php echo($aEvalInd['EVAL_IND_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
				</td>
				<!-- Suppression -->
				<td>
					<a href="?page=evaluations_individuelles&amp;mode=delete&amp;eval_ind_id=<?php echo($aEvalInd['EVAL_IND_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
				</td>
				<!-- Suppression multiple -->
				<td>
					<input type="checkbox" name="evals_inds_id[]" class="evals_inds_id" value="<?php echo($aEvalInd['EVAL_IND_ID']); ?>" alt="Suppression multiple" title="Suppression multiple"  />
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>
<?php endif; ?>