<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

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
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC";
$aCompetences = Database::fetchArray($sQuery);
// $aCompetences[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des comp&eacute;tences</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<table class="list_tree">
	<thead>
		<tr>
			<th>Editer</th>
			<th>Cycles</th>
			<th>Domaines</th>
			<th>Mati&egrave;res</th>
			<th>Comp&eacute;tences</th>
			<th>Supprimer</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aCompetences as $nRowNum => $aCompetence): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td>
				<a href="?page=competences&amp;mode=edit&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<td><?php echo($aCompetence['CYCLE_NOM']); ?></td>
			<td><?php echo($aCompetence['DOMAINE_NOM']); ?></td>
			<td><?php echo($aCompetence['MATIERE_NOM']); ?></td>
			<td>
				<a href="?page=competences&amp;mode=edit&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>">
					<?php echo($aCompetence['COMPETENCE_NOM']); ?></td>
				</a>
			<td>
				<a href="?page=competences&amp;mode=delete&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.gif" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
