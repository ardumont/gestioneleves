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

// ===== La liste des cycles =====
$sQuery = "SELECT" .
		  "  CYCLE_ID, " .
		  "  CYCLE_NOM " .
		  " FROM CYCLES " .
		  " ORDER BY CYCLE_NOM ASC";
$aCycles = Database::fetchArray($sQuery);
// $aCycles[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1>Ajout d'un cycle</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=cycles&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter un cycle</caption>
		<tbody>
			<tr>
				<td>Cycle</td>
				<td>
					<input type="text" name="CYCLE_NOM" size="10" maxlength="<?php echo CYCLE_NOM; ?>" />
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
			<th>Cycles</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aCycles as $nRowNum => $aCycle): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td><?php echo($aCycle['CYCLE_NOM']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

