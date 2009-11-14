<?php
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

// recupere l'id de l'eleve du formulaire $_GET
$nEvalColId = $objForm->getValue('eval_col_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== l'evaluation collective a supprimer =====
$sQuery = "SELECT" .
		  "  EVAL_COL_ID, " .
		  "  EVAL_COL_NOM, " .
		  "  EVAL_COL_DESCRIPTION, " .
		  "  DATE_FORMAT(EVAL_COL_DATE, '%d/%m/%Y') AS EVAL_COL_DATE, " .
		  "  PERIODE_NOM, " .
		  "  CLASSE_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE " .
		  " FROM EVALUATIONS_COLLECTIVES, CLASSES, PERIODES " .
		  " WHERE EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " AND EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID " .
		  " AND EVALUATIONS_COLLECTIVES.EVAL_COL_ID = {$nEvalColId}";
$aEvalCol = Database::fetchOneRow($sQuery);
// $aEvalCol[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Suppression de l'&eacute;valuation collective</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=evaluations_collectives&amp;mode=delete_do">
	<table class="list_tree" width="300px">
		<caption>D&eacute;tail de l'&eacute;valuation collective</caption>
		<thead>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
			<tr class="level0_row0">
				<td>Evaluation collective</td>
				<td><?php echo($aEvalCol['EVAL_COL_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>P&eacute;riode</td>
				<td><?php echo($aEvalCol['PERIODE_NOM']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Date</td>
				<td><?php echo($aEvalCol['EVAL_COL_DATE']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Description</td>
				<td><?php echo($aEvalCol['EVAL_COL_DESCRIPTION']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Classe</td>
				<td><?php echo($aEvalCol['CLASSE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Ann&eacute;e scolaire</td>
				<td><?php echo($aEvalCol['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			</tr>
		</tbody>
	</table>
	<fieldset>
		<legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette &eacute;valuation collective ?</p>
	</fieldset>
	<p>
		<input type="hidden" name="EVAL_COL_ID" value="<?php echo($aEvalCol['EVAL_COL_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
