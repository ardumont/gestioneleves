<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('periode_edit');
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

echo h1("Edition d'une période", $aObjectsToHide);

if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=periodes&amp;mode=edit_do">
	<table class="formulaire">
		<caption>D&eacute;tail de la p&eacute;riode</caption>
		<tr>
			<td>P&eacute;riode</td>
			<td>
				<input type="text" name="PERIODE_NOM" size="10" maxlength="<?php echo PERIODE_NOM; ?>" value="<?php echo($aPeriode['PERIODE_NOM']) ?>"  />
			</td>
		</tr>
		<tr>
			<td>Date de d&eacute;but</td>
			<td>
				<input type="text" name="PERIODE_DATE_DEBUT" size="10" maxlength="<?php echo PERIODE_DATE_DEBUT; ?>" value="<?php echo($aPeriode['PERIODE_DATE_DEBUT']) ?>"  />
			</td>
		</tr>
		<tr>
			<td>Date de fin</td>
			<td>
				<input type="text" name="PERIODE_DATE_FIN" size="10" maxlength="<?php echo PERIODE_DATE_FIN; ?>" value="<?php echo($aPeriode['PERIODE_DATE_FIN']) ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="PERIODE_ID" value="<?php echo($aPeriode['PERIODE_ID']) ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
