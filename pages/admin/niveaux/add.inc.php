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

// ===== La liste des cycles pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  CYCLE_ID," .
		  "  CYCLE_NOM " .
		  " FROM CYCLES " .
		  " ORDER BY CYCLE_NOM ASC";
$aCycles = Database::fetchColumnWithKey($sQuery);
// $aCycles[CYCLE_ID] = CYCLE_NOM

// ===== La liste des domaines pour l'affichage de tous les domaines =====
$sQuery = "SELECT" .
		  "  NIVEAU_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM NIVEAUX, CYCLES " .
		  " WHERE NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, NIVEAU_NOM ASC";
$aNiveaux = Database::fetchArray($sQuery);
// $aNiveaux[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1>Ajout d'un niveau</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=niveaux&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter un niveau</caption>
		<tbody>
			<tr>
				<td>Liste des cycles</td>
				<td>
					<select name="ID_CYCLE">
						<?php foreach($aCycles as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom du niveau</td>
				<td><input type="text" size="100" maxlength="<?php echo(NIVEAU_NOM); ?>" name="NIVEAU_NOM" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<table class="formulaire">
	<thead>
		<tr>
			<th>Cycles</th>
			<th>Niveaux</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aNiveaux as $nRowNum => $aNiveau): ?>
		<tr class="ligne<?php echo($nRowNum%2); ?>">
			<td><?php echo($aNiveau['CYCLE_NOM']); ?></td>
			<td><?php echo($aNiveau['NIVEAU_NOM']); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>