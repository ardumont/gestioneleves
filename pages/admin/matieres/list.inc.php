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
		  "  MATIERE_ID, " .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM MATIERES, DOMAINES, CYCLES " .
		  " WHERE MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC";
$aMatieres = Database::fetchArray($sQuery);
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

<table>
	<thead>
		<tr>
			<th>Editer</th>
			<th>Cycles</th>
			<th>Domaines</th>
			<th>Mati&egrave;res</th>
			<th>Supprimer</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aMatieres as $nRowNum => $aMatiere): ?>
		<tr class="ligne<?php echo($nRowNum%2); ?>">
			<td>
				<a href="?page=matieres&amp;mode=edit&amp;matiere_id=<?php echo($aMatiere['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<td><?php echo($aMatiere['CYCLE_NOM']); ?></td>
			<td><?php echo($aMatiere['DOMAINE_NOM']); ?></td>
			<td><?php echo($aMatiere['MATIERE_NOM']); ?></td>
			<td>
				<a href="?page=matieres&amp;mode=delete&amp;matiere_id=<?php echo($aMatiere['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.gif" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
