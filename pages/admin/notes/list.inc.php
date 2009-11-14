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
<h1>Liste des niveaux</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if($aNotes != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=notes&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Notes</th>
			<th>Labels</th>
			<th>Coefficients</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=notes&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="5"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aNotes as $nRowNum => $aNote): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><?php echo($aNote['NOTE_NOM']); ?></td>
			<td><?php echo($aNote['NOTE_LABEL']); ?></td>
			<td><?php echo($aNote['NOTE_NOTE']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=notes&amp;mode=edit&amp;note_id=<?php echo($aNote['NOTE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=notes&amp;mode=delete&amp;note_id=<?php echo($aNote['NOTE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucune note n'a été renseignée à ce jour.<br />
	<a href="?page=notes&amp;mode=add">Ajouter une note</a>
<?php endif; ?>