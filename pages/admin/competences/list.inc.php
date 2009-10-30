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
$aCompetences = Database::fetchArrayWithMultiKey($sQuery, array('CYCLE_NOM', 'DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM'));
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
			<th>Cycles</th>
			<th>Domaines</th>
			<th>Mati&egrave;res</th>
			<th>Comp&eacute;tences</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php $i = 0; ?>
		<?php foreach($aCompetences as $sCycle => $aCycle): ?>
		<!-- Ligne du cycle -->
		<tr class="level0_row<?php echo ($i++)%2; ?>">
			<!-- Nom du cycle -->
			<th><?php echo($sCycle); ?></th>
			<!-- Le reste -->
			<td colspan="5"></td>
		</tr>
			<?php foreach($aCycle as $sDomaineNom => $aDomaineNom): ?>
			<!-- Ligne du nom de domaine -->
			<tr class="level0_row<?php echo ($i++)%2; ?>">
				<!-- Nom du cycle -->
				<th></th>
				<!-- Nom du domaine -->
				<th><?php echo($sDomaineNom); ?></th>
				<!-- Le reste -->
				<td colspan="4"></td>
			</tr>
				<?php foreach($aDomaineNom as $sMatiereNom => $aMatiereNom): ?>
				<!-- Ligne de la matiere -->
				<tr class="level0_row<?php echo ($i++)%2; ?>">
					<!-- Nom du cycle -->
					<th></th>
					<!-- Nom du domaine -->
					<th></th>
					<!-- Nom de la matiere -->
					<th><?php echo($sMatiereNom); ?></th>
					<!-- Le reste -->
					<td colspan="3"></td>
				</tr>
					<?php foreach($aMatiereNom as $sCompetenceNom => $aCompetence): ?>
					<!-- Ligne de la competence -->
					<tr class="level0_row<?php echo ($i++)%2; ?>">
						<!-- Nom du cycle -->
						<th></th>
						<!-- Nom du domaine -->
						<th></th>
						<!-- Nom de la matiere -->
						<th></th>
						<!-- Nom de la compétence -->
						<td>
							<a href="?page=competences&amp;mode=edit&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>"><?php echo($sCompetenceNom); ?></a>
						</td>
						<!-- Edition -->
						<td>
							<a href="?page=competences&amp;mode=edit&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
						</td>
						<!-- Suppression -->
						<td>
							<a href="?page=competences&amp;mode=delete&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
						</td>
					</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>
