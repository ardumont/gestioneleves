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

// recupere l'id du domaine du formulaire $_GET
$nDomaineId = $oForm->getValue('domaine_id', $_GET, 'convert_int');

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
		  "  CYCLE_NOM " .
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
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Suppression du domaine</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=domaines&amp;mode=delete_do">
	<table class="list_tree" width="200px">
		<caption>D&eacute;tail du domaine</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr class="level0_row0">
				<td>Cycle</td>
				<td><?php echo($aDomaine['CYCLE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Domaine</td>
				<td><?php echo($aDomaine['DOMAINE_NOM']); ?></td>
			</tr>
		</tbody>
	</table>
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer ce domaine ?</p>
		<p>
			Ceci supprimera toutes les mati&egrave;res qui sont rattach&eacute;es &agrave; ce domaine.<br />
			Ceci supprimera &eacute;galement en cons&eacute;quence toutes comp&eacute;tences rattach&eacute;es aux mati&egrave;res concern&eacute;es.
		</p>
	</fieldset>
	<p>
		<input type="hidden" name="DOMAINE_ID" value="<?php echo($aDomaine['DOMAINE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
