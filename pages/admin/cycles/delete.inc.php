<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

$objForm = new FormValidation();

// recupere l'id du cycle du formulaire $_GET
$nCycleId = $objForm->getValue('cycle_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== Le cycle =====
$sQuery = "SELECT" .
		  "  CYCLE_ID, " .
		  "  CYCLE_NOM " .
		  " FROM CYCLES " .
		  " WHERE CYCLE_ID = {$nCycleId} ";
$aCycle = Database::fetchOneRow($sQuery);
// $aCycle[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Suppression du cycle</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=cycles&amp;mode=delete_do">
	<table class="resume_info">
		<caption>D&eacute;tail du cycle</caption>
		<tr>
			<td>Cycle</td>
			<td><?php echo($aCycle['CYCLE_NOM']); ?></td>
		</tr>
	</table>
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer ce cycle ?</p>
		<p>
			Ceci supprimera tous les niveaux rattach&eacute;s &agrave; ce cycle ainsi que tous les domaines.<br />
		</p>
	</fieldset>
	<p>
		<input type="hidden" name="CYCLE_ID" value="<?php echo($aCycle['CYCLE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
