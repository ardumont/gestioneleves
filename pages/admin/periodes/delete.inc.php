<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('periode_delete');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

$oForm = new FormValidation();

// recupere l'id de la periode
$nPeriodeId = $oForm->getValue('periode_id', $_GET, 'convert_int');

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

echo h1("Suppression d'une période", $aObjectsToHide);


if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=periodes&amp;mode=delete_do">
	<table class="list_tree">
		<caption>D&eacute;tail de la p&eacute;riode</caption>
		<thead></thead>
		<tfoot></tfoot>
		<thead>
			<tr class="level0_row0">
				<td>Cycle</td>
				<td><?php echo($aPeriode['PERIODE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Cycle</td>
				<td><?php echo($aPeriode['PERIODE_DATE_DEBUT']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Cycle</td>
				<td><?php echo($aPeriode['PERIODE_DATE_FIN']); ?></td>
			</tr>
		</thead>
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
