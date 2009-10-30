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

// ===== La liste des matieres pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  MATIERE_ID," .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM MATIERES, DOMAINES, CYCLES " .
		  " WHERE MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC";
$aMatieres = Database::fetchArray($sQuery);
// $aMatieres[][COLONNE] = VALEUR

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
<h1>Comp&eacute;tences</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=competences&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter une comp&eacute;tence</caption>
		<tbody>
			<tr>
				<td>Liste des mati&egrave;res</td>
				<td>
					<select name="ID_MATIERE">
						<?php foreach($aMatieres as $aMatiere): ?>
							<option value="<?php echo($aMatiere['MATIERE_ID']); ?>"><?php echo($aMatiere['CYCLE_NOM']." - ".$aMatiere['DOMAINE_NOM'] . " - " . $aMatiere['MATIERE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom</td>
				<td><input type="text" size="100" maxlength="<?php echo(COMPETENCE_NOM); ?>" name="COMPETENCE_NOM" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<table class="list_tree">
	<thead>
		<tr>
			<th>Cycles</th>
			<th>Domaines</th>
			<th>Mati&egrave;res</th>
			<th>Comp&eacute;tences</th>
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
			<td colspan="3"></td>
		</tr>
			<?php foreach($aCycle as $sDomaineNom => $aDomaineNom): ?>
			<!-- Ligne du nom de domaine -->
			<tr class="level0_row<?php echo ($i++)%2; ?>">
				<!-- Nom du cycle -->
				<th></th>
				<!-- Nom du domaine -->
				<th><?php echo($sDomaineNom); ?></th>
				<!-- Le reste -->
				<td colspan="2"></td>
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
					<td></td>
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
						<td><?php echo($sCompetenceNom); ?></td>
					</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>
