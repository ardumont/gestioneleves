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
<h1>Ajout d'une p&eacute;riode</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=periodes&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter une p&eacute;riode</caption>
		<tbody>
			<tr>
				<td>P&eacute;riode</td>
				<td>
					<input type="text" name="PERIODE_NOM" size="10" maxlength="<?php echo PERIODE_NOM; ?>" />
				</td>
			</tr>
			<tr>
				<td>Date de d&eacute;but</td>
				<td>
					<input type="text" name="PERIODE_DATE_DEBUT" size="10" maxlength="<?php echo PERIODE_DATE_DEBUT; ?>" />
				</td>
			</tr>
			<tr>
				<td>Date de fin</td>
				<td>
					<input type="text" name="PERIODE_DATE_FIN" size="10" maxlength="<?php echo PERIODE_DATE_FIN; ?>" />
				</td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<?php if($aPeriodes != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th>P&eacute;riodes</th>
			<th>Dates de d&eacute;but</th>
			<th>Dates de fin</th>
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
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucune période n'a été renseignée à ce jour.
<?php endif; ?>
