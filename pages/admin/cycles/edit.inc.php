<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('cycle_edit');
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

// recupere l'id du cycle du formulaire $_GET
$nCycleId = $oForm->getValue('cycle_id', $_GET, 'convert_int');

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

echo h1("Edition d'un cycle", $aObjectsToHide);

if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=cycles&amp;mode=edit_do">
	<table class="formulaire">
		<caption>D&eacute;tail du cycle</caption>
		<tr>
			<td>Cycle</td>
			<td>
				<input type="text" name="CYCLE_NOM" size="10" maxlength="<?php echo CYCLE_NOM; ?>" value="<?php echo($aCycle['CYCLE_NOM']) ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="CYCLE_ID" value="<?php echo($aCycle['CYCLE_ID']) ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
