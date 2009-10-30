<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

// recupere l'annee courante
$nYear = strftime("%Y",time());

$objForm = new FormValidation();

// au retour de la soumission du formulaire de recherche
$nEvalColId = $objForm->getValue('EVAL_COL_ID', $_POST, 'convert_int');

// si on ne trouve pas la variable dans le formulaire post
// on peux eventuellement la trouver dans le formulaire get
// en cas de retour de la page d'ajout d'une evaluation individuelle
if($nEvalColId == null)
{
	$nEvalColId = $objForm->getValue('ideval', $_GET, 'convert_int');
}

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des evaluations collectives a ce jour pour le select =====
$sQuery = "SELECT" .
		  "  EVAL_COL_ID, " .
		  "  EVAL_COL_NOM, " .
		  "  EVAL_COL_DESCRIPTION, " .
		  "  CLASSE_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE " .
		  " FROM EVALUATIONS_COLLECTIVES, CLASSES " .
		  " WHERE EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " ORDER BY EVAL_COL_DATE ASC";
$aEvalCols = Database::fetchArray($sQuery);
// $aEvalCols[][COLONNE] = VALEUR

// si l'id de l'eval n'est pas remplie (typiquement au premier chargement de la page)
// on prend la premiere valeur resultat de la requete precedente
if($nEvalColId == null && $aEvalCols != false)
{
	$nEvalColId = $aEvalCols[0][EVAL_COL_ID];
}

if($nEvalColId != null)
{
	// ===== detail de l'evaluation collective =====
	$sQuery = "SELECT" .
			  "  EVAL_COL_ID, " .
			  "  EVAL_COL_NOM, " .
			  "  EVAL_COL_DESCRIPTION, " .
			  "  CLASSE_NOM, " .
			  "  CLASSE_ANNEE_SCOLAIRE, " .
			  "  NIVEAU_NOM," .
			  "  NIVEAU_ID " .
			  " FROM EVALUATIONS_COLLECTIVES, CLASSES, NIVEAU_CLASSE, NIVEAUX " .
			  " WHERE EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID " .
			  " AND CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE ".
			  " AND NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID " .
			  " AND EVALUATIONS_COLLECTIVES.EVAL_COL_ID = {$nEvalColId}";
	$aEvalCollective= Database::fetchOneRow($sQuery);
	// $aEvalCollective[COLONNE] = VALEUR

	// ===== La liste des competences =====
	$sQuery = "SELECT" .
			  "  COMPETENCE_ID, " .
			  "  MATIERE_NOM, " .
			  "  COMPETENCE_NOM " .
			  " FROM NIVEAUX, CYCLES, DOMAINES, MATIERES, COMPETENCES " .
			  " WHERE NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID " .
			  " AND CYCLES.CYCLE_ID = DOMAINES.ID_CYCLE " .
			  " AND DOMAINES.DOMAINE_ID = MATIERES.ID_DOMAINE " .
			  " AND MATIERES.MATIERE_ID = COMPETENCES.ID_MATIERE " .
			  " AND NIVEAUX.NIVEAU_ID = " .$aEvalCollective['NIVEAU_ID'] .
			  " ORDER BY MATIERE_NOM ASC, COMPETENCE_NOM ASC";
	$aCompetences = Database::fetchArray($sQuery);
	// $aCompetences[][COLONNE] = VALEUR

	// ===== La liste des eleves =====
	$sQuery = "SELECT" .
			  "  ELEVE_ID," .
			  "  ELEVE_NOM " .
			  " FROM EVALUATIONS_COLLECTIVES, CLASSES, ELEVE_CLASSE, ELEVES  " .
			  " WHERE EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID " .
			  " AND CLASSES.CLASSE_ID = ELEVE_CLASSE.ID_CLASSE " .
			  " AND ELEVE_CLASSE.ID_ELEVE = ELEVES.ELEVE_ID " .
			  " AND EVALUATIONS_COLLECTIVES.EVAL_COL_ID = {$nEvalColId}" .
			  " ORDER BY ELEVE_NOM ASC";
	$aEleves = Database::fetchColumnWithKey($sQuery);
	// $aEleves[ELEVE_ID] = ELEVE_NOM

	// ===== La liste des notes =====
	$sQuery = "SELECT" .
			  "  NOTE_ID," .
			  "  NOTE_NOM " .
			  " FROM NOTES " .
			  " ORDER BY NOTE_NOTE DESC";
	$aNotes = Database::fetchColumnWithKey($sQuery);
	// $aNotes[NOTE_ID] = NOTE_NOM

	// ===== liste des eval. ind. attachees a cette eval. coll. =====
	$sQuery = "SELECT" .
			  "  ELEVE_NOM, " .
			  "  CLASSE_NOM, " .
			  "  NOTE_NOM, " .
			  "  EVAL_IND_COMMENTAIRE, " .
			  "  COMPETENCE_NOM, " .
			  "  MATIERE_NOM, " .
			  "  DOMAINE_NOM " .
			  " FROM EVALUATIONS_INDIVIDUELLES, NOTES, ELEVES, ELEVE_CLASSE, CLASSES, " .
			  " COMPETENCES, MATIERES, DOMAINES, PROFESSEUR_CLASSE " .
			  " WHERE EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID " .
			  " AND EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID " .
			  " AND ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
			  " AND ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID " .
			  " AND CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
			  " AND EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID " .
			  " AND COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID " .
			  " AND MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
			  " AND EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = {$nEvalColId} " .
			  " ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC";
	$aEvalInds= Database::fetchArray($sQuery);
	// $aEvalInds[][COLONNE] = VALEUR
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1>Ajout d'une &eacute;valuation individuelle</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if(count($aEvalCols) <= 0): ?>
	Aucune &eacute;valuation collective saisie &agrave; ce jour.<br />
	Si vous voulez saisir une &eacute;valuation individuelle, vous devez d'abord saisir au moins une &eacute;valuation collective &agrave; laquelle vous pourrez alors rattacher votre &eacute;valution individuelle.
<?php else: ?>
<form method="post" action="?page=evaluations_individuelles&amp;mode=add">
	<table class="formulaire">
		<caption>Rechercher une &eacute;valuation collective</caption>
		<tbody>
			<tr>
				<td>Evaluation Collective</td>
				<td>
					<select name="EVAL_COL_ID">
						<?php foreach($aEvalCols as $aEvalCol): ?>
							<option value="<?php echo($aEvalCol['EVAL_COL_ID']); ?>"<?php echo($aEvalCol['EVAL_COL_ID'] == $nEvalColId ? ' selected="selected"':''); ?>><?php echo($aEvalCol['CLASSE_NOM'] . " - " . $aEvalCol['EVAL_COL_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="submit" value="Rechercher" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>
<?php endif; ?>

<?php if($nEvalColId != null): ?>
	<form method="post" action="?page=evaluations_individuelles&amp;mode=add_do">
		<table class="resume_info" width="300px">
			<caption>D&eacute;tail de l'&eacute;valuation collective</caption>
			<thead>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
				<tr>
					<td>Evaluation collective</td>
					<td><?php echo($aEvalCollective['EVAL_COL_NOM']); ?></td>
				</tr>
				<tr>
					<td>Description</td>
					<td><?php echo($aEvalCollective['EVAL_COL_DESCRIPTION']); ?></td>
				</tr>
				<tr>
					<td>Classe</td>
					<td><?php echo($aEvalCollective['CLASSE_NOM']); ?></td>
				</tr>
				<tr>
					<td>Ann&eacute;e scolaire</td>
					<td><?php echo($aEvalCollective['CLASSE_ANNEE_SCOLAIRE']); ?></td>
				</tr>
			</tbody>
		</table>
		<br /><br />
		<table class="formulaire">
			<caption>Ajout d'une &eacute;valuation individuelle</caption>
			<tr>
				<td>El&egrave;ve</td>
				<td>
					<select name="ID_ELEVE">
						<?php foreach($aEleves as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Comp&eacute;tence</td>
				<td>
					<select name="ID_COMPETENCE">
						<?php foreach($aCompetences as $aCompetence): ?>
							<option value="<?php echo($aCompetence['COMPETENCE_ID']); ?>"><?php echo($aCompetence['MATIERE_NOM'] . " - " .$aCompetence['COMPETENCE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Note</td>
				<td>
					<select name="ID_NOTE">
						<?php foreach($aNotes as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Commentaire</td>
				<td><textarea cols="50" rows="10" name="EVAL_IND_COMMENTAIRE"></textarea></td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="ID_EVAL_COL" value="<?php echo($aEvalCollective['EVAL_COL_ID']); ?>" />
					<input type="submit" value="Ajouter" name="action" />
				</td>
			</tr>
		</table>
	</form>
	<?php if(count($aEvalInds) <= 0): ?>
		Aucune &eacute;valuation individuelle n'a &eacute;t&eacute; saisie &agrave; ce jour.
	<?php else: ?>
	<table class="list_tree">
		<caption>Liste des &eacute;valuations individuelles</caption>
		<thead>
			<tr>
				<th>El&egrave;ves</th>
				<th>Classes</th>
				<th>Domaines</th>
				<th>Mati&egrave;res</th>
				<th>Comp&eacute;tences</th>
				<th>Notes</th>
				<th>Commentaires</th>
			</tr>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
			<?php foreach($aEvalInds as $nRowNum => $aEvalInd): ?>
			<tr class="level0_row<?php echo($nRowNum%2); ?>">
				<td><?php echo($aEvalInd['ELEVE_NOM']); ?></td>
				<td><?php echo($aEvalInd['CLASSE_NOM']); ?></td>
				<td><?php echo($aEvalInd['DOMAINE_NOM']); ?></td>
				<td><?php echo($aEvalInd['MATIERE_NOM']); ?></td>
				<td><?php echo($aEvalInd['COMPETENCE_NOM']); ?></td>
				<td><?php echo($aEvalInd['NOTE_NOM']); ?></td>
				<td><pre><?php echo($aEvalInd['EVAL_IND_COMMENTAIRE']); ?></pre></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
<?php endif; ?>
