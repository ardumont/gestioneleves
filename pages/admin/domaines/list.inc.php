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

// ===== La liste des domaines =====
$sQuery = "SELECT" .
		  "  CYCLE_NOM, " .
		  "  DOMAINE_ID, " .
		  "  DOMAINE_NOM " .
		  " FROM DOMAINES, CYCLES " .
		  " WHERE DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC";
$aDomaines = Database::fetchArrayWithKey($sQuery, 'CYCLE_NOM', false);
// $aDomaines[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des domaines</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if($aDomaines != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=domaines&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Cycles</th>
			<th>Domaines</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=domaines&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="4"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php $nRowNum = 0; ?>
		<?php foreach($aDomaines as $sCycleNom => $aCycleNom): ?>
		<!-- Ligne du cycle -->
		<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
			<td></td>
			<!-- Nom du cycle -->
			<th><?php echo($sCycleNom); ?></th>
			<!-- Le reste -->
			<td colspan="3"></td>
		</tr>
			<?php foreach($aCycleNom as $aDomaine): ?>
			<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
				<td></td>
				<!-- Nom du cycle -->
				<td></td>
				<!-- Nom de domaine -->
				<td><?php echo($aDomaine['DOMAINE_NOM']); ?></td>
				<!-- Edition -->
				<td>
					<a href="?page=domaines&amp;mode=edit&amp;domaine_id=<?php echo($aDomaine['DOMAINE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
				</td>
				<!-- Suppression -->
				<td>
					<a href="?page=domaines&amp;mode=delete&amp;domaine_id=<?php echo($aDomaine['DOMAINE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucun domaine n'a été renseigné à ce jour.<br />
	<a href="?page=domaines&amp;mode=add">Ajouter un domaine</a>
<?php endif; ?>