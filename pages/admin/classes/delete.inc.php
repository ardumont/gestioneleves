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
$nClasseId = $objForm->getValue('classe_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des classes =====
$sQuery = "SELECT" .
		  "  CLASSE_ID," .
		  "  CLASSE_NOM, " .
		  "  PROFESSEUR_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE, " .
		  "  ECOLE_NOM, " .
		  "  ECOLE_VILLE, " .
		  "  ECOLE_DEPARTEMENT " .
		  " FROM CLASSES, PROFESSEUR_CLASSE, PROFESSEURS, ECOLES " .
		  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID " .
		  " AND CLASSES.ID_ECOLE = ECOLES.ECOLE_ID " .
		  " AND CLASSE_ID = {$nClasseId}" .
		  " ORDER BY CLASSE_NOM ASC";
$aClasse = Database::fetchOneRow($sQuery);
// $aClasse[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Suppression de la classe</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=classes&amp;mode=delete_do">
	<table class="resume_info">
		<caption>D&eacute;tail de la classe</caption>
		<tr>
			<td>Professeur</td>
			<td><?php echo($aClasse['PROFESSEUR_NOM']); ?></td>
		</tr>
		<tr>
			<td>Nom de la classe</td>
			<td><?php echo($aClasse['CLASSE_NOM']); ?></td>
		</tr>
		<tr>
			<td>Ann&eacute;e scolaire</td>
			<td><?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']); ?></td>
		</tr>
		<tr>
			<td>Ecole</td>
			<td><?php echo($aClasse['ECOLE_NOM']); ?></td>
		</tr>
		<tr>
			<td>Ville</td>
			<td><?php echo($aClasse['ECOLE_VILLE']); ?></td>
		</tr>
		<tr>
			<td>D&eacute;partement</td>
			<td><?php echo($aClasse['ECOLE_DEPARTEMENT']); ?></td>
		</tr>
	</table>
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette classe ?</p>
		<p>
			Ceci supprimera tous les liens des &eacute;l&egrave;ve de cette classe et la classe.<br />
		</p>		
	</fieldset>
	<p>
		<input type="hidden" name="CLASSE_ID" value="<?php echo($aClasse['CLASSE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
