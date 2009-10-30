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
<h1>Liste des cycles</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<table>
	<thead>
		<tr>
			<th>Editer</th>
			<th>Cycles</th>
			<th>Supprimer</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aCycles as $nRowNum => $aCycle): ?>
		<tr class="ligne<?php echo($nRowNum%2); ?>">
			<td>
				<a href="?page=cycles&amp;mode=edit&amp;cycle_id=<?php echo($aCycle['CYCLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<td><?php echo($aCycle['CYCLE_NOM']); ?></td>
			<td>
				<a href="?page=cycles&amp;mode=delete&amp;cycle_id=<?php echo($aCycle['CYCLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.gif" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
