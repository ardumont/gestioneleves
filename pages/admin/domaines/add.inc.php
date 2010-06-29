<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('domaine_add');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

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

// ===== La liste des cycles pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  CYCLE_ID," .
		  "  CYCLE_NOM " .
		  " FROM CYCLES " .
		  " ORDER BY CYCLE_NOM ASC";
$aCycles = Database::fetchColumnWithKey($sQuery);
// $aCycles[CYCLE_ID] = CYCLE_NOM

// ===== La liste des domaines =====
$sQuery = "SELECT" .
		  "  CYCLE_NOM, " .
		  "  DOMAINE_ID, " .
		  "  DOMAINE_NOM " .
		  " FROM DOMAINES, CYCLES " .
		  " WHERE DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC";
$aDomaines = Database::fetchArrayWithKey($sQuery, 'CYCLE_NOM', false);
// $aDomaines[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Ajout d'un domaine", $aObjectsToHide);

if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=domaines&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter un domaine</caption>
		<tbody>
			<tr>
				<td>Liste des cycles</td>
				<td>
					<select name="ID_CYCLE">
						<?php foreach($aCycles as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom du domaine</td>
				<td><input type="text" size="100" maxlength="<?php echo(DOMAINE_NOM); ?>" name="DOMAINE_NOM" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<?php if($aDomaines != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th>Cycles</th>
			<th>Domaines</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php $nRowNum = 0; ?>
		<?php foreach($aDomaines as $sCycleNom => $aCycleNom): ?>
		<!-- Ligne du cycle -->
		<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
			<!-- Nom du cycle -->
			<th><?php echo($sCycleNom); ?></th>
			<!-- Le reste -->
			<td></td>
		</tr>
			<?php foreach($aCycleNom as $aDomaine): ?>
			<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
				<!-- Nom du cycle -->
				<td></td>
				<!-- Nom de domaine -->
				<td><?php echo($aDomaine['DOMAINE_NOM']); ?></td>
			</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucun domaine n'a été renseigné à ce jour.
		</td>
	</tr>
</table>
<?php endif; ?>
