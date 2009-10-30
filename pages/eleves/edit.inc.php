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

$nEleveId = $objForm->getValue('eleve_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== Informations sur l'eleve =====
$sQuery = "SELECT " .
		  "  ELEVE_ID," .
		  "  ELEVE_NOM, " .
		  "  DATE_FORMAT(ELEVE_DATE_NAISSANCE, '%d/%m/%Y') AS ELEVE_DATE_NAISSANCE, " .
		  "  ELEVE_ACTIF " .
		  " FROM ELEVES " .
		  " WHERE ELEVE_ID = {$nEleveId}";
$aEleve = Database::fetchOneRow($sQuery);
// $aEleve[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>

<h1>Edition de l'&eacute;l&egrave;ve <?php echo($aEleve[ELEVE_NOM]); ?></h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=eleves&amp;mode=edit_do">
	<table class="formulaire">
		<caption>Modifier cet &eacute;l&egrave;ve</caption>
		<tr>
			<th>Nom de l'&eacute;l&egrave;ve</th>
			<td><input type="text" size="<?php echo(ELEVE_NOM); ?>" maxlength="<?php echo(ELEVE_NOM); ?>" name="ELEVE_NOM" value="<?php echo($aEleve['ELEVE_NOM']); ?>" /></td>
		</tr>
		<tr>
			<th>Date de naissance (dd/MM/YYYY)</th>
			<td><input type="text" size="<?php echo(ELEVE_DATE_NAISSANCE); ?>" maxlength="<?php echo(ELEVE_DATE_NAISSANCE); ?>" name="ELEVE_DATE_NAISSANCE" value="<?php echo($aEleve['ELEVE_DATE_NAISSANCE']); ?>" /></td>
		</tr>
		<tr>
			<th>Actif</th>
			<td nowrap="nowrap">
				<input type="radio" name="ELEVE_ACTIF" value="0" <?php echo(($aEleve['ELEVE_ACTIF'] == 0) ? 'checked="checked"' : ''); ?>/>
				Non
				<br />
				<input type="radio" name="ELEVE_ACTIF" value="1" <?php echo(($aEleve['ELEVE_ACTIF'] == 1) ? 'checked="checked"' : ''); ?>/>
				Oui
			</td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="ELEVE_ID" value="<?php echo($aEleve['ELEVE_ID']); ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
