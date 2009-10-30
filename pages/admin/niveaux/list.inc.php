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

// ===== La liste des niveaux =====
$sQuery = "SELECT" .
		  "  NIVEAU_ID," .
		  "  NIVEAU_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM NIVEAUX, CYCLES " .
		  " WHERE NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC";
$aNiveaux = Database::fetchArray($sQuery);
// $aNiveaux[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des niveaux</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<table class="list_tree">
	<thead>
		<tr>
			<th>Editer</th>
			<th>Niveaux</th>
			<th>Cycles</th>
			<th>Supprimer</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aNiveaux as $nRowNum => $aNiveau): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td>
				<a href="?page=niveaux&amp;mode=edit&amp;niveau_id=<?php echo($aNiveau['NIVEAU_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<td><?php echo($aNiveau['NIVEAU_NOM']); ?></td>
			<td><?php echo($aNiveau['CYCLE_NOM']); ?></td>
			<td>
				<a href="?page=niveaux&amp;mode=delete&amp;niveau_id=<?php echo($aNiveau['NIVEAU_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.gif" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
