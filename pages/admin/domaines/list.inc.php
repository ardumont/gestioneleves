<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Modification de la date =====
$oForm = new FormValidation();

// soumission via post, typiquement une fois le bouton rechercher appuye.
$nCycleId = $oForm->getValue('cycle_id', $_POST, 'convert_int', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des cycles =====
$sQuery = <<< EOQ
	SELECT
		CYCLE_NOM,
		CYCLE_ID
	FROM CYCLES
	ORDER BY CYCLE_NOM ASC
EOQ;
$aCycles = Database::fetchArray($sQuery);
// $aCycles[][COLONNE] = VALEUR

$sQueryCycleId = "";
if($nCycleId != -1)
{
	$sQueryCycleId = " AND CYCLE_ID = {$nCycleId}";
}

// ===== La liste des domaines =====
$sQuery = <<< EOQ
	SELECT
		CYCLE_NOM,
		DOMAINE_ID,
		DOMAINE_NOM
	FROM DOMAINES, CYCLES
	WHERE DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
	{$sQueryCycleId}
	ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC
EOQ;
$aDomaines = Database::fetchArrayWithKey($sQuery, 'CYCLE_NOM', false);
// $aDomaines[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des domaines</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if($aDomaines != false): ?>
	<form method="post" action="?page=domaines" name="formulaire_domaine" id="formulaire_domaine">
		<table class="formulaire">
			<caption>Crit&eacute;res de recherche</caption>
			<tfoot>
			</tfoot>
			<tbody>
				<tr>
					<td>Cycle</td>
					<td>
						<select name="cycle_id" onchange="document.getElementById('formulaire_domaine').submit();">
							<option value="-1">-- Sélectionnez un cycle --</option>
							<?php foreach($aCycles as $aCycle): ?>
								<option value="<?php echo($aCycle['CYCLE_ID']); ?>"<?php echo ($nCycleId == $aCycle['CYCLE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aCycle['CYCLE_NOM']); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><input type="submit" name="action" value="Rechercher" /></td>
				</tr>
			</tbody>
		</table>
	</form>
	<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=domaines&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Cycles</th>
			<th>Domaines</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=domaines&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="4"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php $nRowNum = 0; ?>
		<?php foreach($aDomaines as $sCycleNom => $aCycleNom): ?>
		<!-- Ligne du cycle -->
		<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
			<td></td>
			<!-- Nom du cycle -->
			<th><?php echo($sCycleNom); ?></th>
			<!-- Le reste -->
			<td colspan="3"></td>
		</tr>
			<?php foreach($aCycleNom as $aDomaine): ?>
			<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
				<td></td>
				<!-- Nom du cycle -->
				<td></td>
				<!-- Nom de domaine -->
				<td><?php echo($aDomaine['DOMAINE_NOM']); ?></td>
				<!-- Edition -->
				<td>
					<a href="?page=domaines&amp;mode=edit&amp;domaine_id=<?php echo($aDomaine['DOMAINE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
				</td>
				<!-- Suppression -->
				<td>
					<a href="?page=domaines&amp;mode=delete&amp;domaine_id=<?php echo($aDomaine['DOMAINE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	<?php if($nCycleId != -1): ?>
	Aucun domaine n'a été renseigné pour le cycle sélectionné.<br />
	<?php else: ?>
	Aucun domaine n'a été renseigné à ce jour.<br />
	<a href="?page=domaines&amp;mode=add">Ajouter un domaine</a>
	<?php endif; ?>
<?php endif; ?>