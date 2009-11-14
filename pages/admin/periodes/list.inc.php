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

// ===== La liste des periodes =====
$sQuery = "SELECT" .
		  "  PERIODE_ID," .
		  "  PERIODE_NOM, " .
		  "  PERIODE_DATE_DEBUT, " .
		  "  PERIODE_DATE_FIN " .
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
?>
<h1>Liste des p&eacute;riodes</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if($aPeriodes != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th>P&eacute;riode</th>
			<th>Dates de d&eacute;but</th>
			<th>Dates de fin</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aPeriodes as $nRowNum => $aPeriode): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td><?php echo($aPeriode['PERIODE_NOM']); ?></td>
			<td><?php echo($aPeriode['PERIODE_DATE_DEBUT']); ?></td>
			<td><?php echo($aPeriode['PERIODE_DATE_FIN']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=periodes&amp;mode=edit&amp;periode_id=<?php echo($aPeriode['PERIODE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=periodes&amp;mode=delete&amp;periode_id=<?php echo($aPeriode['PERIODE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucune période n'a été renseignée à ce jour.<br />
	<a href="?page=periodes&amp;mode=add">Ajouter une période</a>
<?php endif; ?>