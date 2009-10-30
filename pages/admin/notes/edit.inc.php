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

// recupere l'id de la note
$nNoteId = $objForm->getValue('note_id', $_GET, 'convert_int');

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
		  " WHERE NOTE_ID = {$nNoteId} ";
$aNote = Database::fetchOneRow($sQuery);
// $aNote[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Edition d'une note</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=notes&amp;mode=edit_do">
	<table class="formulaire">
		<caption>D&eacute;tail de la note</caption>
		<tr>
			<td>Note</td>
			<td>
				<input type="text" name="NOTE_NOM" size="10" maxlength="<?php echo NOTE_NOM; ?>" value="<?php echo($aNote['NOTE_NOM']) ?>"  />
			</td>
		</tr>
		<tr>
			<td>Label</td>
			<td>
				<input type="text" name="NOTE_LABEL" size="10" maxlength="<?php echo NOTE_LABEL; ?>" value="<?php echo($aNote['NOTE_LABEL']) ?>"  />
			</td>
		</tr>
		<tr>
			<td>Coefficients</td>
			<td>
				<input type="text" name="NOTE_NOTE" size="10" maxlength="<?php echo NOTE_NOTE; ?>" value="<?php echo($aNote['NOTE_NOTE']) ?>"  />
			</td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="NOTE_ID" value="<?php echo($aNote['NOTE_ID']) ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
