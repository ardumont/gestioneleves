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

$oForm = new FormValidation();

// recupere l'id de la note
$nNoteId = $oForm->getValue('note_id', $_GET, 'convert_int');

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
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Suppression de la note</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=notes&amp;mode=delete_do">
	<table class="list_tree">
		<caption>D&eacute;tail de la note</caption>
		<thead></thead>
		<tfoot></tfoot>
		<thead>
			<tr class="level0_row0">
				<td>Note</td>
				<td><?php echo($aNote['NOTE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Label</td>
				<td><?php echo($aNote['NOTE_LABEL']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Coefficient</td>
				<td><?php echo($aNote['NOTE_NOTE']); ?></td>
			</tr>
		</thead>
	</table>
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette note ?</p>
	</fieldset>
	<p>
		<input type="hidden" name="NOTE_ID" value="<?php echo($aNote['NOTE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
