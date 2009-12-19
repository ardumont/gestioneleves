<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

// recupere l'annee courante
$nYear = strftime("%Y",time());

$oForm = new FormValidation();

// au retour de la soumission du formulaire de recherche
$nEvalColId = $oForm->getValue('EVAL_COL_ID', $_POST, 'convert_int', null);

// si on ne trouve pas la variable dans le formulaire post
// on peux eventuellement la trouver dans le formulaire get
// en cas de retour de la page d'ajout d'une evaluation individuelle
if($nEvalColId == null)
{
	$nEvalColId = $oForm->getValue('ideval', $_GET, 'convert_int');
}

// Récupère de la session les informations soumises
$aIdEleves = isset($_SESSION['ID_ELEVE']) ? $_SESSION['ID_ELEVE'] : array();
$aIdCompetences = isset($_SESSION['ID_COMPETENCE']) ? $_SESSION['ID_COMPETENCE'] : array();
$nNoteId = isset($_SESSION['ID_NOTE']) ? $_SESSION['ID_NOTE'] : -1;

// Puis détruit la session
$_SESSION['ID_ELEVE'] = null;
$_SESSION['ID_COMPETENCE'] = null;
$_SESSION['ID_NOTE'] = null;

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// Commentaires pré-remplis pour accélérer la saisie
$aCommentairesPreRemplis = array(
	"A revoir.",
	"Bon travail.",
	"Bravo.",
	"Des efforts restent à faire.",
	"Excellent travail.",
	"Peut mieux faire.",
	"Tu dois te concentrer davantage."
);

// ===== La liste des evaluations collectives a ce jour pour le select =====
$sQuery = <<< EOQ
	SELECT
		EVAL_COL_ID,
		CONCAT(CLASSE_NOM, ' - ', PERIODE_NOM, ' - ', EVAL_COL_NOM) AS EVAL_COL_NOM,
		EVAL_COL_DESCRIPTION,
		CLASSE_NOM,
		CLASSE_ANNEE_SCOLAIRE
	FROM EVALUATIONS_COLLECTIVES
		INNER JOIN CLASSES
			ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PERIODES
			ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
	ORDER BY EVAL_COL_DATE ASC
EOQ;
$aEvalCols = Database::fetchArray($sQuery);
// $aEvalCols[][COLONNE] = VALEUR

// si l'id de l'eval n'est pas remplie (typiquement au premier chargement de la page)
// on prend la premiere valeur resultat de la requete precedente
if($nEvalColId == null && $aEvalCols != false)
{
	$nEvalColId = $aEvalCols[0]['EVAL_COL_ID'];
}

if($nEvalColId != null)
{
	// ===== detail de l'evaluation collective =====
	$sQuery = <<< ____EOQ
		SELECT
			EVAL_COL_ID,
			EVAL_COL_NOM,
			EVAL_COL_DESCRIPTION,
			CLASSE_NOM,
			CLASSE_ANNEE_SCOLAIRE,
			NIVEAU_NOM,
			NIVEAU_ID
		FROM EVALUATIONS_COLLECTIVES
			INNER JOIN CLASSES
				ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
			INNER JOIN NIVEAU_CLASSE
				ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
			INNER JOIN NIVEAUX
				ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
		WHERE EVALUATIONS_COLLECTIVES.EVAL_COL_ID = {$nEvalColId}
____EOQ;
	$aEvalCollective = Database::fetchOneRow($sQuery);
	// $aEvalCollective[COLONNE] = VALEUR

	// ===== La liste des competences =====
	$sQuery = <<< ____EOQ
		SELECT
			COMPETENCE_ID,
			MATIERE_NOM,
			COMPETENCE_NOM
		FROM NIVEAUX
			INNER JOIN CYCLES
				ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
			INNER JOIN DOMAINES
				ON CYCLES.CYCLE_ID = DOMAINES.ID_CYCLE
			INNER JOIN MATIERES
				ON DOMAINES.DOMAINE_ID = MATIERES.ID_DOMAINE
			INNER JOIN COMPETENCES
				ON MATIERES.MATIERE_ID = COMPETENCES.ID_MATIERE
		WHERE NIVEAUX.NIVEAU_ID = {$aEvalCollective['NIVEAU_ID']}
		ORDER BY MATIERE_NOM ASC, COMPETENCE_NOM ASC
____EOQ;
	$aCompetences = Database::fetchArray($sQuery);
	// $aCompetences[][COLONNE] = VALEUR

	// ===== La liste des eleves =====
	$sQuery = <<< ____EOQ
		SELECT
			ELEVE_ID,
			ELEVE_NOM
		FROM EVALUATIONS_COLLECTIVES
			INNER JOIN CLASSES
				ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
			INNER JOIN ELEVE_CLASSE
				ON CLASSES.CLASSE_ID = ELEVE_CLASSE.ID_CLASSE
			INNER JOIN ELEVES
				ON ELEVE_CLASSE.ID_ELEVE = ELEVES.ELEVE_ID
		WHERE EVALUATIONS_COLLECTIVES.EVAL_COL_ID = {$nEvalColId}
		ORDER BY ELEVE_NOM ASC
____EOQ;
	$aEleves = Database::fetchColumnWithKey($sQuery);
	// $aEleves[ELEVE_ID] = ELEVE_NOM

	// ===== La liste des notes =====
	$sQuery = <<< ____EOQ
		SELECT
			NOTE_ID,
			NOTE_NOM
		FROM NOTES
		ORDER BY NOTE_NOTE DESC
____EOQ;
	$aNotes = Database::fetchColumnWithKey($sQuery);
	// $aNotes[NOTE_ID] = NOTE_NOM

	if($aIdEleves != false && $aIdCompetences != false)
	{
		$sQueryEleves = implode(",", $aIdEleves);
		$sQueryCompetences = implode(",", $aIdCompetences);

		$sQueryEleves = "AND ELEVES.ELEVE_ID IN ({$sQueryEleves})";
		$sQueryCompetences = "AND COMPETENCES.COMPETENCE_ID IN ({$sQueryCompetences})";

		// ===== liste des eval. ind. attachees a cette eval. coll. =====
		$sQuery = <<< ________EOQ
			SELECT
				EVAL_IND_ID,
				ELEVE_NOM,
				CLASSE_NOM,
				NOTE_NOM,
				NOTE_LABEL,
				EVAL_IND_COMMENTAIRE,
				COMPETENCE_NOM,
				MATIERE_NOM,
				DOMAINE_NOM
			FROM EVALUATIONS_INDIVIDUELLES
				INNER JOIN NOTES
					ON EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID
				INNER JOIN ELEVES
					ON EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID
				INNER JOIN EVALUATIONS_COLLECTIVES
					ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
				INNER JOIN CLASSES
					ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN COMPETENCES
					ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
				INNER JOIN MATIERES
					ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
				INNER JOIN DOMAINES
					ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
				INNER JOIN PROFESSEUR_CLASSE
					ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
			WHERE EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = {$nEvalColId}
			{$sQueryEleves}
			{$sQueryCompetences}
			ORDER BY ELEVE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aEvalInds = Database::fetchArray($sQuery);
		// $aEvalInds[][COLONNE] = VALEUR
	} else {
		$aEvalInds = false;
	}
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Ajout d'une &eacute;valuation individuelle</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<br />
<?php endif; ?>

<?php if(count($aEvalCols) <= 0): ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucune &eacute;valuation collective saisie &agrave; ce jour.<br />
			Si vous voulez saisir une &eacute;valuation individuelle, vous devez d'abord
			<a href="?page=evaluations_collectives&amp;mode=add">saisir au moins une &eacute;valuation collective</a>
			&agrave; laquelle vous pourrez alors rattacher votre &eacute;valuation individuelle.
		</td>
	</tr>
</table>
<?php else: ?>
<form method="post" action="?page=evaluations_individuelles&amp;mode=add" name="recherche_eval_coll" id="recherche_eval_coll">
	<table class="formulaire">
		<caption>Rechercher une &eacute;valuation collective</caption>
		<tbody>
			<tr>
				<td>Evaluation Collective</td>
				<td>
					<select name="EVAL_COL_ID" onchange="document.getElementById('recherche_eval_coll').submit();">
						<?php foreach($aEvalCols as $aEvalCol): ?>
							<option value="<?php echo($aEvalCol['EVAL_COL_ID']); ?>"<?php echo($aEvalCol['EVAL_COL_ID'] == $nEvalColId ? ' selected="selected"':''); ?>><?php echo($aEvalCol['EVAL_COL_NOM']); ?></option>
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
		<table class="formulaire">
			<caption>Ajout d'une &eacute;valuation individuelle</caption>
			<tr>
				<td>El&egrave;ves</td>
				<td>
					<select multiple="multiple" size="5" name="ID_ELEVE[]">
						<?php foreach($aEleves as $nKey => $sValue): ?>
							<?php $bInArray = in_array($nKey, $aIdEleves); ?>
							<option value="<?php echo($nKey); ?>"<?php echo $bInArray ? ' selected="selected"': ''; ?>><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Comp&eacute;tences</td>
				<td>
					<select multiple="multiple" size="5" name="ID_COMPETENCE[]">
						<?php foreach($aCompetences as $aCompetence): ?>
							<?php $bInArray = in_array($aCompetence['COMPETENCE_ID'], $aIdCompetences); ?>
							<option value="<?php echo($aCompetence['COMPETENCE_ID']); ?>"<?php echo $bInArray ? ' selected="selected"': ''; ?>><?php echo($aCompetence['MATIERE_NOM'] . " - " .$aCompetence['COMPETENCE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Note</td>
				<td>
					<select name="ID_NOTE">
						<?php foreach($aNotes as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"<?php echo ($nKey == $nNoteId) ? ' selected="selected"': ''; ?>><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Commentaire pré-rempli</td>
				<td>
					<select name="eval_ind_commentaire_pre_rempli" onchange="if(this.value != -1) document.getElementById('eval_ind_comm').disabled=1";">
						<option value="">-- Sélectionner un commentaire pré-rempli --</option>
						<?php foreach($aCommentairesPreRemplis as $sCommentairePreRempli): ?>
							<option value="<?php echo($sCommentairePreRempli); ?>"><?php echo($sCommentairePreRempli); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Commentaire</td>
				<td><textarea cols="50" rows="10" name="eval_ind_commentaire"></textarea></td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="ID_EVAL_COL" value="<?php echo($aEvalCollective['EVAL_COL_ID']); ?>" />
					<input type="submit" value="Ajouter" name="action" />
				</td>
			</tr>
		</table>
	</form>

	<?php if($aEvalInds != false): ?>
	<a href="javascript:void(0);" onclick="$('.evals_inds_id').attr('checked', 'checked');">Sélectionner tout</a>&nbsp;
	<a href="javascript:void(0);" onclick="$('.evals_inds_id').removeAttr('checked');">Désélectionner tout</a>
	<form method="post" action="?page=evaluations_individuelles&amp;mode=actions_multiples&amp;retour=add">
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
					<th colspan="3">Actions</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="9">
						<input type="submit" name="suppression_multiple" value="Suppression multiple" />
					</th>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach($aEvalInds as $nRowNum => $aEvalInd): ?>
				<tr class="level0_row<?php echo($nRowNum%2); ?>">
					<td><?php echo($aEvalInd['ELEVE_NOM']); ?></td>
					<td><?php echo($aEvalInd['CLASSE_NOM']); ?></td>
					<td><?php echo($aEvalInd['DOMAINE_NOM']); ?></td>
					<td><?php echo($aEvalInd['MATIERE_NOM']); ?></td>
					<td><?php echo($aEvalInd['COMPETENCE_NOM']); ?></td>
					<td title="<?php echo($aEvalInd['NOTE_NOM'] . (($aEvalInd['EVAL_IND_COMMENTAIRE'] != null) ? " - '" . $aEvalInd['EVAL_IND_COMMENTAIRE'] . "'" : "")); ?>"><?php echo($aEvalInd['NOTE_LABEL']); ?></td>
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
						<input type="checkbox" class="evals_inds_id" name="evals_inds_id[]" value="<?php echo($aEvalInd['EVAL_IND_ID']); ?>" alt="Suppression multiple" title="Suppression multiple"  />
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</form>
	<?php endif; ?>
<?php endif; ?>
