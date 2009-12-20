<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('ecole_edit');
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

// recupere l'id de l'eleve du formulaire $_GET
$nEcoleId = $oForm->getValue('ecole_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== L'ecole =====
$sQuery = "SELECT" .
		  "  ECOLE_ID," .
		  "  ECOLE_NOM, " .
		  "  ECOLE_VILLE, " .
		  "  ECOLE_DEPARTEMENT " .
		  " FROM ECOLES ";
$aEcole = Database::fetchOneRow($sQuery);
// $aEcole[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Edition d'une &eacute;cole</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=ecoles&amp;mode=edit_do">
	<table class="formulaire" border="0">
		<caption>D&eacute;tail de la classe</caption>
		<tr>
			<td>Ecole</td>
			<td><input type="text" name="ECOLE_NOM" size="<?php echo(ECOLE_NOM); ?>" maxlength="<?php echo(ECOLE_NOM); ?>" value="<?php echo($aEcole['ECOLE_NOM']); ?>" /></td>
		</tr>
		<tr>
			<td>Ville</td>
			<td><input type="text" name="ECOLE_VILLE" size="<?php echo(ECOLE_VILLE); ?>" maxlength="<?php echo(ECOLE_VILLE); ?>" value="<?php echo($aEcole['ECOLE_VILLE']); ?>" /></td>
		</tr>
		<tr>
			<td>D&eacute;partement</td>
			<td><input type="text" name="ECOLE_DEPARTEMENT" size="<?php echo(ECOLE_DEPARTEMENT); ?>" maxlength="<?php echo(ECOLE_DEPARTEMENT); ?>" value="<?php echo($aEcole['ECOLE_DEPARTEMENT']); ?>" /></td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="ECOLE_ID" value="<?php echo($aEcole['ECOLE_ID']) ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
