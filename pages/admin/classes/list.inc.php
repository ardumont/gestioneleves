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

// ===== La liste des classes =====
$sQuery = "SELECT" .
		  "  CLASSE_ID," .
		  "  CLASSE_NOM, " .
		  "  PROFESSEUR_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE, " .
		  "  ECOLE_NOM, " .
		  "  ECOLE_VILLE, " .
		  "  ECOLE_DEPARTEMENT " .
		  " FROM CLASSES, PROFESSEUR_CLASSE, PROFESSEURS, ECOLES " .
		  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID " .
		  " AND CLASSES.ID_ECOLE = ECOLES.ECOLE_ID " .
		  " ORDER BY CLASSE_NOM ASC";
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des classes</h1>

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
			<th>Professeurs</th>
			<th>Classes</th>
			<th>Ann&eacute;es scolaires</th>
			<th>Ecoles</th>
			<th>Villes</th>
			<th>D&eacute;partements</th>
			<th>Supprimer</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aClasses as $nRowNum => $aClasse): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td>
				<a href="?page=classes&amp;mode=edit&amp;classe_id=<?php echo($aClasse['CLASSE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<td><?php echo($aClasse['PROFESSEUR_NOM']); ?></td>
			<td><?php echo($aClasse['CLASSE_NOM']); ?></td>
			<td><?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			<td><?php echo($aClasse['ECOLE_NOM']); ?></td>
			<td><?php echo($aClasse['ECOLE_VILLE']); ?></td>
			<td><?php echo($aClasse['ECOLE_DEPARTEMENT']); ?></td>
			<td>
				<a href="?page=classes&amp;mode=delete&amp;classe_id=<?php echo($aClasse['CLASSE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.gif" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
