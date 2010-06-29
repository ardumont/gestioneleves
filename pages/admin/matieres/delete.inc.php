<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('matiere_delete');
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

// recupere l'id de la matiere du formulaire $_GET
$nMatiereId = $oForm->getValue('matiere_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La matiere =====
$sQuery = "SELECT" .
		  "  MATIERE_ID," .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_ID, " .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM" .
		  " FROM MATIERES, DOMAINES, CYCLES " .
		  " WHERE MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " AND MATIERES.MATIERE_ID = {$nMatiereId} ";
$aMatiere = Database::fetchOneRow($sQuery);
// $aMatiere[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Suppression d'une matière", $aObjectsToHide);

if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=matieres&amp;mode=delete_do">
	<table class="list_tree">
		<caption>D&eacute;tail de la mati&egrave;re</caption>
		<thead></thead>
		<tfoot></tfoot>
		<thead>
			<tr class="level0_row0">
				<td>Domaine</td>
				<td><?php echo($aMatiere['DOMAINE_NOM']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Nom de la mati&egrave;re</td>
				<td><?php echo($aMatiere['MATIERE_NOM']); ?></td>
			</tr>
		</thead>
	</table>
	<fieldset><legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette mati&egrave;re ?</p>
		<p>
			Ceci supprimera toutes les comp&eacute;tences rattach&eacute;es &agrave; cette mati&egrave;re.
		</p>
	</fieldset>
	<p>
		<input type="hidden" name="MATIERE_ID" value="<?php echo($aMatiere['MATIERE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
