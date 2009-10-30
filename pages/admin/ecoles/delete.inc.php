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

// recupere l'id de l'eleve du formulaire $_GET
$nEcoleId = $objForm->getValue('ecole_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== L'ecole =====
$sQuery = "SELECT" .
		  "  ECOLE_ID," .
		  "  ECOLE_NOM, " .
		  "  ECOLE_VILLE, " .
		  "  ECOLE_DEPARTEMENT " .
		  " FROM ECOLES " .
		  " WHERE ECOLE_ID = {$nEcoleId}";
$aEcole = Database::fetchOneRow($sQuery);
// $aEcole[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Suppression de l'&eacute;cole</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=ecoles&amp;mode=delete_do">
	<table class="resume_info">
		<caption>D&eacute;tail de l'&eacute;cole</caption>
		<tr>
			<td>Ecole</td>
			<td><?php echo($aEcole['ECOLE_NOM']); ?></td>
		</tr>
		<tr>
			<td>Ville</td>
			<td><?php echo($aEcole['ECOLE_VILLE']); ?></td>
		</tr>
		<tr>
			<td>D&eacute;partement</td>
			<td><?php echo($aEcole['ECOLE_DEPARTEMENT']); ?></td>
		</tr>
	</table>
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette &eacute;cole ?</p>
		<p>
			Ceci supprimera toutes les classes li&eacute;es &agrave; cette &eacute;cole.<br />
		</p>		
	</fieldset>
	<p>
		<input type="hidden" name="ECOLE_ID" value="<?php echo($aEcole['ECOLE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
