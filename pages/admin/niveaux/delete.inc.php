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
$nNiveauId = $objForm->getValue('niveau_id', $_GET, 'convert_int');

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

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Suppression du niveau</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=niveaux&amp;mode=delete_do">
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer le niveau '<?php echo($aNiveau['NIVEAU_NOM']) ?>' ?</p>
	</fieldset>
	<p>
		<input type="hidden" name="NIVEAU_ID" value="<?php echo($aNiveau['NIVEAU_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
