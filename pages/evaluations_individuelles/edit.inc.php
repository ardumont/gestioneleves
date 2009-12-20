<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eval_ind_edit');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Action du formulaire
//==============================================================================

$oForm = new FormValidation();

$nEvalIndId = $oForm->getValue('eval_ind_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des evaluations individuelles a ce jour =====
$sQuery = "SELECT" .
		  "  EVAL_IND_ID, " .
		  "  EVAL_IND_COMMENTAIRE, " .
		  "  COMPETENCE_ID, " .
		  "  COMPETENCE_NOM, " .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_NOM, " .
		  "  NOTE_ID, " .
		  "  NOTE_NOM, " .
		  "  ELEVE_NOM, " .
		  "  ID_NIVEAU, " .
		  "  CLASSE_NOM " .
		  " FROM EVALUATIONS_INDIVIDUELLES, NOTES, ELEVES, ELEVE_CLASSE, CLASSES, " .
		  " NIVEAU_CLASSE, COMPETENCES, MATIERES, DOMAINES " .
		  " WHERE EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID " .
		  " AND EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID " .
		  " AND ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
		  " AND ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " AND CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE " .
		  " AND EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID " .
		  " AND COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID " .
		  " AND MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND EVALUATIONS_INDIVIDUELLES.EVAL_IND_ID = {$nEvalIndId} ";
$aEvalInd = Database::fetchOneRow($sQuery);
// $aEvalInd[COLONNE] = VALEUR

// ===== La liste des notes =====
$sQuery = "SELECT" .
		  "  NOTE_ID," .
		  "  NOTE_NOM " .
		  " FROM NOTES " .
		  " ORDER BY NOTE_NOTE DESC";
$aNotes = Database::fetchColumnWithKey($sQuery);
// $aNotes[NOTE_ID] = NOTE_NOM

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
		  " AND NIVEAUX.NIVEAU_ID = " .$aEvalInd['ID_NIVEAU'] .
		  " ORDER BY MATIERE_NOM ASC, COMPETENCE_NOM ASC";
$aCompetences = Database::fetchArray($sQuery);
// $aCompetences[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>

<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Edition de l'&eacute;valuation individuelle</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=evaluations_individuelles&amp;mode=edit_do">
	<table class="formulaire">
		<caption>Modifier cette &eacute;valuation individuelle</caption>
		<tr>
			<td>Classe</td>
			<td><?php echo($aEvalInd['CLASSE_NOM']); ?></td>
		</tr>
		<tr>
			<td>El&egrave;ves</td>
			<td><?php echo($aEvalInd['ELEVE_NOM']); ?></td>
		</tr>
		<tr>
			<td>Note</td>
			<td>
				<select name="ID_NOTE">
					<?php foreach($aNotes as $nKey => $sValue): ?>
						<option value="<?php echo($nKey); ?>"<?php echo($nKey == $aEvalInd['NOTE_ID'] ? ' selected="selected"':''); ?>><?php echo($sValue); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Mati&egrave;re - Comp&eacute;tence</td>
			<td>
				<select name="ID_COMPETENCE">
					<?php foreach($aCompetences as $aCompetence): ?>
						<option value="<?php echo($aCompetence['COMPETENCE_ID']); ?>"<?php echo($aCompetence['COMPETENCE_ID'] == $aEvalInd['COMPETENCE_ID'] ? ' selected="selected"':''); ?>"><?php echo($aCompetence['MATIERE_NOM'] . " - " .$aCompetence['COMPETENCE_NOM']); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Commentaire</td>
			<td><textarea cols="50" rows="10" name="EVAL_IND_COMMENTAIRE"><?php echo($aEvalInd['EVAL_IND_COMMENTAIRE']); ?></textarea></td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="EVAL_IND_ID" value="<?php echo($aEvalInd['EVAL_IND_ID']); ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
