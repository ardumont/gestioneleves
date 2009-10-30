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

$objFormEleve = new FormValidation();

// recupere l'id de l'eleve du formulaire $_GET
$nEleveId = $objFormEleve->getValue('eleve_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== Informations de l'eleve + id de la classe pour le retour =====
$sQuery = "SELECT " .
		  "  ELEVE_ID," .
		  "  ELEVE_NOM, " .
		  "  CLASSE_ID " .
		  " FROM ELEVES, ELEVE_CLASSE, CLASSES, PROFESSEUR_CLASSE " .
		  " WHERE ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
		  " AND ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " AND CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND ELEVE_ID = {$nEleveId} " .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = " . $_SESSION['PROFESSEUR_ID'] .
		  " AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();
$aEleve = Database::fetchOneRow($sQuery);
// $aEleve[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Suppression de l'&eacute;l&egrave;ve</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=eleves&amp;mode=desactive_do">
	<fieldset>
		<legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir d&eacute;sactiver l'&eacute;l&egrave;ve "<?php echo($aEleve['ELEVE_NOM']); ?>" ?</p>
	</fieldset>
	<p>
		<input type="hidden" name="ELEVE_ID" value="<?php echo($aEleve['ELEVE_ID']) ?>" />
		<input type="hidden" name="CLASSE_ID" value="<?php echo($aEleve['CLASSE_ID']) ?>" />

		<input type="submit" name="action" value="Desactiver" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
