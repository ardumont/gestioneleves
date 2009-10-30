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

// ===== La liste des ecoles =====
$sQuery = "SELECT" .
		  "  ECOLE_ID," .
		  "  ECOLE_NOM, " .
		  "  ECOLE_VILLE, " .
		  "  ECOLE_DEPARTEMENT " .
		  " FROM ECOLES " .
		  " ORDER BY ECOLE_VILLE ASC, ECOLE_NOM ASC";
$aEcoles = Database::fetchArray($sQuery);
// $aEcoles[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des &eacute;coles</h1>

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
			<th>Ecoles</th>
			<th>Villes</th>
			<th>D&eacute;partements</th>
			<th>Supprimer</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aEcoles as $nRowNum => $aEcole): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td>
				<a href="?page=ecoles&amp;mode=edit&amp;ecole_id=<?php echo($aEcole['ECOLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<td><?php echo($aEcole['ECOLE_NOM']); ?></td>
			<td><?php echo($aEcole['ECOLE_VILLE']); ?></td>
			<td><?php echo($aEcole['ECOLE_DEPARTEMENT']); ?></td>
			<td>
				<a href="?page=ecoles&amp;mode=delete&amp;ecole_id=<?php echo($aEcole['ECOLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.gif" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
