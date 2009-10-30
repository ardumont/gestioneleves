<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

$objForm = new FormValidation();

// recupere l'id de la periode
$nPeriodeId = $objForm->getValue('periode_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La periode =====
$sQuery = "SELECT" .
		  "  PERIODE_ID," .
		  "  PERIODE_NOM, " .
		  "  PERIODE_DATE_DEBUT, " .
		  "  PERIODE_DATE_FIN " .
		  " FROM PERIODES " .
		  " WHERE PERIODE_ID = {$nPeriodeId} ";
$aPeriode = Database::fetchOneRow($sQuery);
// $aPeriode[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Suppression de la p&eacute;riode</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=periodes&amp;mode=delete_do">
	<table class="resume_info">
		<caption>D&eacute;tail du cycle</caption>
		<tr>
			<td>Cycle</td>
			<td><?php echo($aPeriode['PERIODE_NOM']); ?></td>
		</tr>
		<tr>
			<td>Cycle</td>
			<td><?php echo($aPeriode['PERIODE_DATE_DEBUT']); ?></td>
		</tr>
		<tr>
			<td>Cycle</td>
			<td><?php echo($aPeriode['PERIODE_DATE_FIN']); ?></td>
		</tr>
	</table>
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette p&eacute;riode ?</p>
		<p>
			Ceci supprimera toutes les &eacute;valuations collectives rattach&eacute;es &agrave; cette p&eacute;riode.
		</p>
	</fieldset>
	<p>
		<input type="hidden" name="PERIODE_ID" value="<?php echo($aPeriode['PERIODE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
