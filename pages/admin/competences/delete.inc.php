<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('competence_delete');
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
$nCompetenceId = $oForm->getValue('competence_id', $_GET, 'convert_int');

// recupere l'id de l'eleve du formulaire $_GET
$nMatiereId = $oForm->getValue('matiere_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des competences =====
$sQuery = "SELECT" .
		  "  COMPETENCE_ID, " .
		  "  COMPETENCE_NOM, " .
		  "  MATIERE_ID, " .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM COMPETENCES, MATIERES, DOMAINES, CYCLES " .
		  " WHERE COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID " .
		  " AND MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " AND COMPETENCE_ID = {$nCompetenceId} " .
		  " AND MATIERE_ID = {$nMatiereId} ";
$aCompetence = Database::fetchOneRow($sQuery);
// $aCompetence[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Suppression de la compétence", $aObjectsToHide);

if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=competences&amp;mode=delete_do">
	<table class="list_tree">
		<caption>D&eacute;tail de la comp&eacute;tence</caption>
		<thead>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
			<tr class="level0_row0">
				<td>Cycle</td>
				<td><?php echo($aCompetence['CYCLE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Domaine</td>
				<td><?php echo($aCompetence['DOMAINE_NOM']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Mati&egrave;re</td>
				<td><?php echo($aCompetence['MATIERE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Comp&eacute;tence</td>
				<td><?php echo($aCompetence['COMPETENCE_NOM']); ?></td>
			</tr>
		</tbody>
	</table>
	<fieldset>
		<legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette comp&eacute;tence ?</p>
	</fieldset>
	<p>
		<input type="hidden" name="COMPETENCE_ID" value="<?php echo($aCompetence['COMPETENCE_ID']) ?>" />
		<input type="hidden" name="MATIERE_ID" value="<?php echo($aCompetence['MATIERE_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
