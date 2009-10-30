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

// ===== La note =====
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
<h1>Suppression de la note</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=notes&amp;mode=delete_do">
	<table class="resume_info">
		<caption>D&eacute;tail du cycle</caption>
		<tr>
			<td>Note</td>
			<td><?php echo($aNote['NOTE_NOM']); ?></td>
		</tr>
		<tr>
			<td>Label</td>
			<td><?php echo($aNote['NOTE_LABEL']); ?></td>
		</tr>
		<tr>
			<td>Coefficient</td>
			<td><?php echo($aNote['NOTE_NOTE']); ?></td>
		</tr>
	</table>
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette notes ?</p>
	</fieldset>
	<p>
		<input type="hidden" name="NOTE_ID" value="<?php echo($aNote['NOTE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
