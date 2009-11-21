<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//restriction sur l'annee scolaire courante
$sRestrictionAnneeScolaire =
	" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// recuperation des ids de restrictions de recherche
$nEleveId = $oForm->getValue('ELEVE_ID', $_POST, 'convert_int', -1);
$nClasseId = $oForm->getValue('CLASSE_ID', $_POST, 'convert_int', -1);
$nEvalCollId = $oForm->getValue('EVAL_COL_ID', $_POST, 'convert_int', -1);

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
		EVAL_COL_NOM,
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
	ORDER BY PERIODE_NOM ASC
EOQ;
$aEvalCols = Database::fetchArray($sQuery);
// $aEvalCols[][COLONNE] = VALEUR

// ===== La liste des eleves du professeur pour l'annee courante =====
$sQuery = <<< EOQ
	SELECT DISTINCT
		ELEVE_ID,
		ELEVE_NOM,
		CLASSE_ANNEE_SCOLAIRE,
		CLASSE_NOM
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

// criteres de recherche
$sQueryElevesId = ($nEleveId != -1) ? " AND ELEVES.ELEVE_ID = {$nEleveId} " : "";
$sQueryClasseId = ($nClasseId != -1) ? " AND CLASSES.CLASSE_ID = {$nClasseId} " : "";
$sQueryEvalCollId = ($nEvalCollId != -1) ? " AND EVALUATIONS_COLLECTIVES.EVAL_COL_ID = {$nEvalCollId} " : "";

// ===== La liste des evaluations individuelles a ce jour =====
$sQuery = <<< EOQ
	SELECT
		ELEVE_NOM,
		CLASSE_NOM,
		NOTE_NOM,
		EVAL_IND_ID,
		EVAL_IND_COMMENTAIRE,
		EVAL_COL_NOM,
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
	WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	{$sQueryElevesId}
	{$sQueryEvalCollId}
	{$sQueryClasseId}
	ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
EOQ;
$aEvalInds= Database::fetchArray($sQuery);
// $aEvalInds[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des évaluations individuelles</h1>

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
				<td>Liste des évaluations collectives de l'année courante</td>
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
				<td>Liste des classes de l'année courante</td>
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
				<td>Liste des élèves de l'année courante</td>
				<td>
					<select name="ELEVE_ID" onchange="document.getElementById('formulaire_eval_ind').submit();">
						<option value="-1">-- Sélectionner un élève --</option>
						<?php foreach($aEleves as $aEleve): ?>
							<option value="<?php echo($aEleve['ELEVE_ID']); ?>"<?php echo($aEleve['ELEVE_ID'] == $nEleveId ? ' selected="selected"' :''); ?>><?php echo($aEleve['CLASSE_ANNEE_SCOLAIRE']. " - " . $aEleve['CLASSE_NOM'] . " - " . $aEleve['ELEVE_NOM']); ?></option>
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
			Aucune évaluation individuelle n'a été saisie à ce jour pour ces critères.<br />
			<a href="?page=evaluations_individuelles&amp;mode=add">Ajouter une évaluation individuelle</a>
		</td>
	</tr>
</table>
<?php else: ?>
	<table class="list_tree">
		<caption>Liste des évaluations individuelles</caption>
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
				<th colspan="2">Actions</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><a href="?page=evaluations_individuelles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
				<th colspan="9"></th>
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
				<td><?php echo($aEvalInd['NOTE_NOM']); ?></td>
				<td></td>
				<!-- Edition -->
				<td>
					<a href="?page=evaluations_individuelles&amp;mode=edit&amp;eval_ind_id=<?php echo($aEvalInd['EVAL_IND_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
				</td>
				<!-- Suppression -->
				<td>
					<a href="?page=evaluations_individuelles&amp;mode=delete&amp;eval_ind_id=<?php echo($aEvalInd['EVAL_IND_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
				</td>
			</tr>
			<?php if($aEvalInd['EVAL_IND_COMMENTAIRE'] != null): ?>
			<tr class="level0_row<?php echo($nRowNum%2); ?>">
				<td colspan="2"></td>
				<th>Commentaires</th>
				<td colspan="8"><pre style="font-size: 1.2em;"><?php echo($aEvalInd['EVAL_IND_COMMENTAIRE']); ?></pre></td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>