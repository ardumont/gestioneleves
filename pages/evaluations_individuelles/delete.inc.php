<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eval_ind_delete');
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
$nEvalIndId = $oForm->getValue('eval_ind_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== l'evaluations individuelle a supprimer =====
$sQuery = "SELECT" .
		  "  ELEVE_NOM, " .
		  "  CLASSE_NOM, " .
		  "  NOTE_NOM, " .
		  "  EVAL_IND_ID, " .
		  "  EVAL_IND_COMMENTAIRE, " .
		  "  COMPETENCE_NOM, " .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_NOM " .
		  " FROM EVALUATIONS_INDIVIDUELLES, NOTES, ELEVES, ELEVE_CLASSE, CLASSES, " .
		  " COMPETENCES, MATIERES, DOMAINES, PROFESSEUR_CLASSE " .
		  " WHERE EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID " .
		  " AND EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID " .
		  " AND ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
		  " AND ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " AND CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID " .
		  " AND COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID " .
		  " AND MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND EVALUATIONS_INDIVIDUELLES.EVAL_IND_ID = {$nEvalIndId}";
$aEvalInd = Database::fetchOneRow($sQuery);
// $aEvalInd[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Suppression de l'&eacute;valuation individuelle", $aObjectsToHide);
?>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=evaluations_individuelles&amp;mode=delete_do">
	<table class="list_tree" width="300px">
		<caption>D&eacute;tail de l'&eacute;valuation individuelle</caption>
		<thead>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
			<tr class="level0_row0">
				<td>El&egrave;ve</td>
				<td><?php echo($aEvalInd['ELEVE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Classe</td>
				<td><?php echo($aEvalInd['CLASSE_NOM']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Note</td>
				<td><?php echo($aEvalInd['NOTE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Commentaire</td>
				<td><?php echo($aEvalInd['EVAL_IND_COMMENTAIRE']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Comp&eacute;tence &eacute;valu&eacute;e</td>
				<td><?php echo($aEvalInd['COMPETENCE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<td>Mati&egrave;re</td>
				<td><?php echo($aEvalInd['MATIERE_NOM']); ?></td>
			</tr>
			<tr class="level0_row0">
				<td>Domaine</td>
				<td><?php echo($aEvalInd['DOMAINE_NOM']); ?></td>
			</tr>
		</tbody>
	</table>
	<fieldset>
		<legend>Confirmation</legend>
		<p>Etes-vous s&ucirc;r de vouloir supprimer cette &eacute;valuation individuelle ?</p>
	</fieldset>
	<p>
		<input type="hidden" name="EVAL_IND_ID" value="<?php echo($aEvalInd['EVAL_IND_ID']) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
