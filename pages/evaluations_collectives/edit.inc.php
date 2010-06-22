<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eval_col_edit');
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

$objForm = new FormValidation();

$nEvalColId = $objForm->getValue('eval_col_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des evaluations collectives a ce jour =====
$sQuery = "SELECT" .
		  "  EVAL_COL_ID, " .
		  "  EVAL_COL_NOM, " .
		  "  EVAL_COL_DESCRIPTION, " .
		  "  DATE_FORMAT(EVAL_COL_DATE, '%d/%m/%Y') AS EVAL_COL_DATE, " .
		  "  PERIODE_ID, " .
		  "  CLASSE_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE " .
		  " FROM EVALUATIONS_COLLECTIVES, CLASSES, PERIODES " .
		  " WHERE EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " AND EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID " .
		  " AND EVALUATIONS_COLLECTIVES.EVAL_COL_ID = {$nEvalColId}" .
		  " ORDER BY PERIODE_NOM ASC";
$aEvalCol= Database::fetchOneRow($sQuery);
// $aEvalCol[COLONNE] = VALEUR

// ===== La liste des eleves du professeur pour l'annee courante =====
$sQuery = "SELECT " .
		  "  PERIODE_ID," .
		  "  PERIODE_NOM " .
		  " FROM PERIODES " .
		  " ORDER BY PERIODE_NOM ASC";
$aPeriodes = Database::fetchArray($sQuery);
// $aPeriodes[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Edition de l'&eacute;valuation collective", $aObjectsToHide);
?>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=evaluations_collectives&amp;mode=edit_do">
	<table class="formulaire">
		<caption>Modifier cette &eacute;valuation collective</caption>
		<tr>
			<td>Classe</td>
			<td><?php echo($aEvalCol['CLASSE_NOM']); ?></td>
		</tr>
		<tr>
			<td>Ann&eacute;e scolaire</td>
			<td><?php echo($aEvalCol['CLASSE_ANNEE_SCOLAIRE']); ?></td>
		</tr>
		<tr>
			<td>Nom de l'&eacute;valuation collective</td>
			<td><input type="text" name="EVAL_COL_NOM" size="<?php echo(EVAL_COL_NOM); ?>" maxlength="<?php echo(EVAL_COL_NOM); ?>" value="<?php echo($aEvalCol['EVAL_COL_NOM']); ?>" /></td>
		</tr>
		<tr>
			<td>Description</td>
			<td>
				<textarea name="EVAL_COL_DESCRIPTION" rows="10" cols="50" /><?php echo($aEvalCol['EVAL_COL_DESCRIPTION']); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>Liste des p&eacute;riodes</td>
			<td>
				<select name="PERIODE_ID">
					<option value="0">-- S&eacute;lectionner une p&eacute;riode --</option>
					<?php foreach($aPeriodes as $aPeriode): ?>
						<option value="<?php echo($aPeriode['PERIODE_ID']); ?>"<?php echo($aPeriode['PERIODE_ID'] == $aEvalCol['PERIODE_ID'] ? ' selected="selected"' :''); ?>><?php echo($aPeriode['PERIODE_NOM']); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Date</td>
			<td>
				<input type="text" name="EVAL_COL_DATE" size="<?php echo(EVAL_COL_DATE); ?>" name="EVAL_COL_DATE" id="EVAL_COL_DATE" maxlength="<?php echo(EVAL_COL_DATE); ?>" value="<?php echo($aEvalCol['EVAL_COL_DATE']); ?>" />
				<button id="f_trigger_b1" type="reset">...</button>
				<script type="text/javascript">
				    Calendar.setup({
				        inputField     :    "EVAL_COL_DATE",	// id of the input field
				        ifFormat       :    "%d/%m/%Y",      	// format of the input field
				        showsTime      :    false,           	// will display a time selector
				        button         :    "f_trigger_b1",  	// trigger for the calendar (button ID)
				        singleClick    :    true,           	// single-click mode
				        step           :    1                	// show all years in drop-down boxes (instead of every other year as default)
				    });
				</script>
			</td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="EVAL_COL_ID" value="<?php echo($aEvalCol['EVAL_COL_ID']); ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
