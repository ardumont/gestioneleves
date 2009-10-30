<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Action du formulaire
//==============================================================================

$objForm = new FormValidation();

// recupere l'id du domaine du formulaire $_GET
$nDomaineId = $objForm->getValue('domaine_id', $_GET, 'convert_int');

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

// ===== Le domaine =====
$sQuery = "SELECT" .
		  "  DOMAINE_ID, " .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_ID " .
		  " FROM DOMAINES, CYCLES " .
		  " WHERE DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " AND DOMAINES.DOMAINE_ID = {$nDomaineId} ";
$aDomaine = Database::fetchOneRow($sQuery);
// $aDomaine[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Edition d'un domaine</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=domaines&amp;mode=edit_do">
	<table class="formulaire">
		<caption>D&eacute;tail du domaine</caption>
			<tr>
				<td>Liste des cycles</td>
				<td>
					<select name="ID_CYCLE">
						<?php foreach($aCycles as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"<?php echo($aDomaine['CYCLE_ID'] == $nKey ? ' selected="selected"':''); ?>><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom du domaine</td>
				<td><input type="text" name="DOMAINE_NOM" size="100" maxlength="<?php echo(DOMAINE_NOM); ?>" value="<?php echo($aDomaine['DOMAINE_NOM']); ?>" /></td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="DOMAINE_ID" value="<?php echo($aDomaine['DOMAINE_ID']) ?>" />
					<input type="submit" name="action" value="Modifier" />
				</td>
				<td>
					<input type="submit" name="action" value="Annuler" />
				</td>
			</tr>
	</table>
</form>
