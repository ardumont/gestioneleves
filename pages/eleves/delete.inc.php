<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eleve_delete');
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
$nEleveId = $oForm->getValue('eleve_id', $_GET, 'convert_int');
// recupere l'id de l'eleve du formulaire $_GET
$nClasseId = $oForm->getValue('classe_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== Informations de l'eleve + id de la classe pour le retour =====
$sQuery = "SELECT " .
		  "  ELEVE_ID," .
		  "  ELEVE_NOM, " .
		  "  CLASSE_ID, " .
		  "  CLASSE_NOM " .
		  " FROM ELEVES, ELEVE_CLASSE, CLASSES, PROFESSEUR_CLASSE " .
		  " WHERE ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
		  " AND ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " AND CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND ELEVE_ID = {$nEleveId} " .
		  " AND CLASSE_ID = {$nClasseId} ";
$aEleve = Database::fetchOneRow($sQuery);
// $aEleve[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Suppression de l'&eacute;l&egrave;ve", $aObjectsToHide);
?>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=eleves&amp;mode=delete_do">
	<fieldset>
		<legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer l'&eacute;l&egrave;ve "<?php echo($aEleve['ELEVE_NOM']); ?>" ?</p>
		<p>
			Ceci supprimera l'&eacute;l&egrave;ve "<?php echo($aEleve['ELEVE_NOM']); ?>" de la classe "<?php echo($aEleve['CLASSE_NOM']); ?>".<br />
			Si l'&eacute;l&egrave;ve n'est plus rattach&eacute; &agrave; d'autres classes, il sera alors d&eacute;finitivement supprim&eacute;.
		</p>
	</fieldset>
	<p>
		<input type="hidden" name="ELEVE_ID" value="<?php echo($aEleve['ELEVE_ID']) ?>" />
		<input type="hidden" name="CLASSE_ID" value="<?php echo($aEleve['CLASSE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
