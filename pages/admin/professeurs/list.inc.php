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
		  "  PROFESSEUR_ID, " .
		  "  PROFESSEUR_NOM " .
		  " FROM PROFESSEURS " .
		  " ORDER BY PROFESSEUR_NOM ASC";
$aProfesseurs = Database::fetchArray($sQuery);
// $aProfesseurs[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des professeurs</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if($aProfesseurs != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th>Professeurs</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aProfesseurs as $nRowNum => $aProfesseur): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td>
				<a href="?page=professeurs&amp;mode=edit&amp;professeur_id=<?php echo($aProfesseur['PROFESSEUR_ID']); ?>"><?php echo($aProfesseur['PROFESSEUR_NOM']); ?></a></td>
			<!-- Edition -->
			<td>
				<a href="?page=professeurs&amp;mode=edit&amp;professeur_id=<?php echo($aProfesseur['PROFESSEUR_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=professeurs&amp;mode=delete&amp;professeur_id=<?php echo($aProfesseur['PROFESSEUR_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucun professeur n'a été renseigné à ce jour.
<?php endif; ?>