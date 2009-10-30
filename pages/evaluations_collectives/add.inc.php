<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des classes =====
$sQuery = "SELECT" .
		  "  CLASSE_ID," .
		  "  CLASSE_NOM " .
		  " FROM CLASSES, PROFESSEUR_CLASSE, ECOLES " .
		  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND CLASSES.ID_ECOLE = ECOLES.ECOLE_ID " .
		  " AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante() .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = " . $_SESSION['PROFESSEUR_ID'] .
		  " ORDER BY CLASSE_NOM ASC";
$aClasses = Database::fetchColumnWithKey($sQuery);
// $aClasses[CLASSE_ID] = CLASSE_NOM

// ===== La liste des periodes pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  PERIODE_ID, " .
		  "  PERIODE_NOM " .
		  " FROM PERIODES " .
		  " ORDER BY PERIODE_NOM ASC";
$aPeriodes= Database::fetchColumnWithKey($sQuery);
// $aPeriodes[PERIODE_ID] = PERIODE_NOM

// ===== La liste des evaluations collectives a ce jour =====
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
		  " ORDER BY PERIODE_NOM ASC";
$aEvalCols= Database::fetchArray($sQuery);
// $aEvalCols[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1>Ajout d'une &eacute;valuation collective</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if(count($aClasses) <= 0 && count($aPeriodes <= 0) ): ?>
	Vous devez d'abord cr&eacute;er au moins une classe et au moins une p&eacute;riode auxquelles vous pourrez alors rattacher votre &eacute;valuation collective. 
<?php else: ?>
<form method="post" action="?page=evaluations_collectives&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter une &eacute;valuation collective</caption>
		<tbody>
			<tr>
				<td>P&eacute;riode</td>
				<td>
					<select name="ID_PERIODE">
						<?php foreach($aPeriodes as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Classe</td>
				<td>
					<select name="ID_CLASSE">
						<?php foreach($aClasses as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom</td>
				<td><input type="text" size="50" maxlength="<?php echo(EVAL_COL_NOM); ?>" name="EVAL_COL_NOM" /></td>
			</tr>
			<tr>
				<td>Description</td>
				<td><textarea cols="50" rows="10" size="200" maxlength="<?php echo(EVAL_COL_DESCRIPTION); ?>" name="EVAL_COL_DESCRIPTION"></textarea></td>
			</tr>
			<tr>
				<td>Date (format : jj/mm/AAAA)</td>
				<td><input type="text" size="10" maxlength="<?php echo(EVAL_COL_DATE); ?>" name="EVAL_COL_DATE" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>
<?php endif; ?>

<?php if(count($aEvalCols) <= 0): ?>
	Aucune &eacute;valuation collective n'a &eacute;t&eacute; saisie &agrave; ce jour.
<?php else: ?>
<table>
	<caption>Liste des &eacute;valuations</caption>
	<thead>
		<tr>
			<th>P&eacute;riodes</th>
			<th>Classes</th>
			<th>Ann&eacute;es scolaires</th>
			<th>Evaluations collectives</th>
			<th>Descriptions</th>
			<th>Dates</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aEvalCols as $nRowNum => $aEvalCol): ?>
		<tr class="ligne<?php echo($nRowNum%2); ?>">
			<td><?php echo($aEvalCol['PERIODE_NOM']); ?></td>
			<td><?php echo($aEvalCol['CLASSE_NOM']); ?></td>
			<td><?php echo($aEvalCol['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			<td><?php echo($aEvalCol['EVAL_COL_NOM']); ?></td>
			<td><pre><?php echo($aEvalCol['EVAL_COL_DESCRIPTION']); ?></pre></td>
			<td><?php echo($aEvalCol['EVAL_COL_DATE']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>