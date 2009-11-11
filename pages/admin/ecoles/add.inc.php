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

//==============================================================================
// Preparation de l'affichage
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
// Affichage de la page
//==============================================================================

?>
<h1>Ajout d'une &eacute;cole</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=ecoles&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter une &eacute;cole</caption>
		<tbody>
			<tr>
				<td>Nom</td>
				<td><input type="text" size="50" maxlength="<?php echo(ECOLE_NOM); ?>" name="ECOLE_NOM" /></td>
			</tr>
			<tr>
				<td>Ville</td>
				<td><input type="text" size="50" maxlength="<?php echo(ECOLE_VILLE); ?>" name="ECOLE_VILLE" /></td>
			</tr>
			<tr>
				<td>D&eacute;partement</td>
				<td><input type="text" size="5" maxlength="<?php echo(ECOLE_DEPARTEMENT); ?>" name="ECOLE_DEPARTEMENT" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<?php if($aEcoles != false): ?>
<table class="list_tree">
	<caption>Liste des &eacute;coles</caption>
	<thead>
		<tr>
			<th>Ecoles</th>
			<th>Villes</th>
			<th>D&eacute;partements</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aEcoles as $nRowNum => $aEcole): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td><?php echo($aEcole['ECOLE_NOM']); ?></td>
			<td><?php echo($aEcole['ECOLE_VILLE']); ?></td>
			<td><?php echo($aEcole['ECOLE_DEPARTEMENT']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucune école n'a été renseignée à ce jour.
<?php endif; ?>
