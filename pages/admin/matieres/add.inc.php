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

// ===== La liste des domaines pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  DOMAINE_ID," .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM DOMAINES, CYCLES " .
		  " WHERE DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC";
$aDomaines = Database::fetchArray($sQuery);
// $aDomaines[][COLONNE] = VALEUR

// ===== La liste des matieres pour l'affichage de toutes les matieres =====
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
<h1>Mati&egrave;res</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=matieres&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter une mati&egrave;re</caption>
		<tbody>
			<tr>
				<td>Liste des domaines</td>
				<td>
					<select name="ID_DOMAINE">
						<?php foreach($aDomaines as $aDomaine): ?>
							<option value="<?php echo($aDomaine['DOMAINE_ID']); ?>"><?php echo($aDomaine['CYCLE_NOM']." - ".$aDomaine['DOMAINE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom de la mati&egrave;re</td>
				<td><input type="text" size="100" maxlength="<?php echo(MATIERE_NOM); ?>" name="MATIERE_NOM" /></td>
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
			<td colspan="3"></td>
		</tr>
			<?php foreach($aCycle as $sDomaine => $aDomaine): ?>
			<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
				<!-- Le cycle -->
				<td></td>
				<!-- Le nom du domaine -->
				<td><?php echo($sDomaine); ?></td>
				<!-- Le reste -->
				<td></td>
			</tr>
				<?php foreach($aDomaine as $sMatiere => $aMatiere): ?>
				<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
					<!-- Le cycle -->
					<td></td>
					<!-- Le nom du domaine -->
					<td></td>
					<!-- La matière -->
					<td><?php echo $sMatiere; ?></td>
				</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>