<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('niveau_edit');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Action du formulaire
//==============================================================================

$oForm = new FormValidation();

// recupere l'id du domaine du formulaire $_GET
$nNiveauId = $oForm->getValue('niveau_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== Le domaine =====
$sQuery = "SELECT" .
		  "  NIVEAU_ID, " .
		  "  NIVEAU_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM NIVEAUX, CYCLES " .
		  " WHERE NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " AND NIVEAUX.NIVEAU_ID = {$nNiveauId} ";
$aNiveau = Database::fetchOneRow($sQuery);
// $aNiveau[COLONNE] = VALEUR

// ===== La liste des cycles pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  CYCLE_ID," .
		  "  CYCLE_NOM " .
		  " FROM CYCLES " .
		  " ORDER BY CYCLE_NOM ASC";
$aCycles = Database::fetchColumnWithKey($sQuery);
// $aCycles[CYCLE_ID] = CYCLE_NOM

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Edition d'un niveau</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=niveaux&amp;mode=edit_do">
	<table class="formulaire">
		<caption>D&eacute;tail du niveau</caption>
			<tr>
				<td>Liste des cycles</td>
				<td>
					<select name="ID_CYCLE">
						<?php foreach($aCycles as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"<?php echo($aNiveau['CYCLE_ID'] == $nKey ? ' selected="selected"':''); ?>><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom du domaine</td>
				<td><input type="text" name="NIVEAU_NOM" size="100" maxlength="<?php echo(NIVEAU_NOM); ?>" value="<?php echo($aNiveau['NIVEAU_NOM']); ?>" /></td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="NIVEAU_ID" value="<?php echo($aNiveau['NIVEAU_ID']) ?>" />
					<input type="submit" name="action" value="Modifier" />
				</td>
				<td>
					<input type="submit" name="action" value="Annuler" />
				</td>
			</tr>
	</table>
</form>
