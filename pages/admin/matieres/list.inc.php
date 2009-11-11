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

// ===== La liste des matieres =====
$sQuery = "SELECT" .
		  "  CYCLE_NOM, " .
		  "  DOMAINE_NOM, " .
		  "  MATIERE_ID, " .
		  "  MATIERE_NOM " .
		  " FROM MATIERES, DOMAINES, CYCLES " .
		  " WHERE MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC";
$aMatieres = Database::fetchArrayWithMultiKey($sQuery, array('CYCLE_NOM', 'DOMAINE_NOM', 'MATIERE_NOM'));
// $aMatieres[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des mati&egrave;res</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if($aMatieres != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th>Cycles</th>
			<th>Domaines</th>
			<th>Mati&egrave;res</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php $nRowNum = 0; ?>
		<?php foreach($aMatieres as $sCycle => $aCycle): ?>
		<!-- Ligne du cycle -->
		<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
			<!-- Le cycle -->
			<td><?php echo($sCycle); ?></td>
			<!-- Le reste -->
			<td colspan="4"></td>
		</tr>
			<?php foreach($aCycle as $sDomaine => $aDomaine): ?>
			<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
				<!-- Le cycle -->
				<td></td>
				<!-- Le nom du domaine -->
				<td><?php echo($sDomaine); ?></td>
				<!-- Le reste -->
				<td colspan="3"></td>
			</tr>
				<?php foreach($aDomaine as $sMatiere => $aMatiere): ?>
				<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
					<!-- Le cycle -->
					<td></td>
					<!-- Le nom du domaine -->
					<td></td>
					<!-- La matière -->
					<td>
						<a href="?page=matieres&amp;mode=edit&amp;matiere_id=<?php echo($aMatiere['MATIERE_ID']); ?>"> <?php echo $sMatiere; ?></a>
					</td>
					<!-- Edition -->
					<td>
						<a href="?page=matieres&amp;mode=edit&amp;matiere_id=<?php echo($aMatiere['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
					</td>
					<!-- Suppression -->
					<td>
						<a href="?page=matieres&amp;mode=delete&amp;matiere_id=<?php echo($aMatiere['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
					</td>
				</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucune matière n'a été renseignée à ce jour.
<?php endif; ?>