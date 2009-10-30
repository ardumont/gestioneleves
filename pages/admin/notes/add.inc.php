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

// ===== La liste des notes =====
$sQuery = "SELECT" .
		  "  NOTE_ID," .
		  "  NOTE_NOM, " .
		  "  NOTE_LABEL, " .
		  "  NOTE_NOTE " .
		  " FROM NOTES " .
		  " ORDER BY NOTE_NOTE DESC";
$aNotes = Database::fetchArray($sQuery);
// $aNotes[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1>Ajout d'une note</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=notes&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter une note</caption>
		<tbody>
			<tr>
				<td>Note</td>
				<td>
					<input type="text" name="NOTE_NOM" size="10" maxlength="<?php echo NOTE_NOM; ?>" />
				</td>
			</tr>
			<tr>
				<td>Label</td>
				<td>
					<input type="text" name="NOTE_LABEL" size="10" maxlength="<?php echo NOTE_LABEL; ?>" />
				</td>
			</tr>
			<tr>
				<td>Coefficients</td>
				<td>
					<input type="text" name="NOTE_NOTE" size="10" maxlength="<?php echo NOTE_NOTE; ?>" />
				</td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<table class="list_tree">
	<thead>
		<tr>
			<th>Notes</th>
			<th>Labels</th>
			<th>Coefficients</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aNotes as $nRowNum => $aNote): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td><?php echo($aNote['NOTE_NOM']); ?></td>
			<td><?php echo($aNote['NOTE_LABEL']); ?></td>
			<td><?php echo($aNote['NOTE_NOTE']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

