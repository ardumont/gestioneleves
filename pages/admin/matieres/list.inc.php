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

// soumission via post, typiquement une fois le bouton rechercher appuye.
$nDomaineId = $oForm->getValue('domaine_id', $_POST, 'convert_int', -1);

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

// ===== La liste des domaines =====
$sQuery = <<< EOQ
	SELECT
		DOMAINE_ID,
		DOMAINE_NOM
	FROM DOMAINES
	ORDER BY DOMAINE_NOM ASC
EOQ;
$aDomaines = Database::fetchArray($sQuery);
// $aDomaines[][COLONNE] = VALEUR

$sQueryCycleId = "";
if($nCycleId != -1)
{
	$sQueryCycleId = " AND CYCLE_ID = {$nCycleId}";
}

$sQueryDomaineId = "";
if($nDomaineId != -1)
{
	$sQueryDomaineId = " AND DOMAINE_ID = {$nDomaineId}";
}

// ===== La liste des matieres =====
$sQuery = <<< EOQ
	SELECT
		CYCLE_NOM,
		DOMAINE_NOM,
		MATIERE_ID,
		MATIERE_NOM
	FROM MATIERES, DOMAINES, CYCLES
	WHERE MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
	AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
	{$sQueryDomaineId}
	{$sQueryCycleId}
	ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC
EOQ;
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
	<form method="post" action="?page=matieres">
		<table class="formulaire">
			<caption>Crit&eacute;res de recherche</caption>
			<tfoot>
			</tfoot>
			<tbody>
				<tr>
					<td>Cycle</td>
					<td>
						<select name="cycle_id">
							<option value="-1">-- Sélectionnez un cycle --</option>
							<?php foreach($aCycles as $aCycle): ?>
								<option value="<?php echo($aCycle['CYCLE_ID']); ?>"<?php echo ($nCycleId == $aCycle['CYCLE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aCycle['CYCLE_NOM']); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Domaines</td>
					<td>
						<select name="domaine_id">
							<option value="-1">-- Sélectionnez un domaine --</option>
							<?php foreach($aDomaines as $aDomaine): ?>
								<option value="<?php echo($aDomaine['DOMAINE_ID']); ?>"<?php echo ($nDomaineId == $aDomaine['DOMAINE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aDomaine['DOMAINE_NOM']); ?></option>
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
				<th><a href="?page=matieres&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
				<th>Cycles</th>
				<th>Domaines</th>
				<th>Mati&egrave;res</th>
				<th colspan="2">Actions</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><a href="?page=matieres&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
				<th colspan="5"></th>
			</tr>
		</tfoot>
		<tbody>
			<?php $nRowNum = 0; ?>
			<?php foreach($aMatieres as $sCycle => $aCycle): ?>
			<!-- Ligne du cycle -->
			<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
				<th></th>
				<!-- Le cycle -->
				<th><?php echo($sCycle); ?></th>
				<!-- Le reste -->
				<th colspan="4"></th>
			</tr>
				<?php foreach($aCycle as $sDomaine => $aDomaine): ?>
				<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
					<th></th>
					<!-- Le cycle -->
					<th></th>
					<!-- Le nom du domaine -->
					<th><?php echo($sDomaine); ?></th>
					<!-- Le reste -->
					<th colspan="3"></th>
				</tr>
					<?php foreach($aDomaine as $sMatiere => $aMatiere): ?>
					<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
						<td></td>
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
	<?php if($nCycleId != -1 || $nMatiereId != -1): ?>
		Aucune matière n'a été renseignée pour ces critères de recherche.<br />
	<?php else:?>
		Aucune matière n'a été renseignée à ce jour.<br />
		<a href="?page=matieres&amp;mode=add">Ajouter une matière</a>
	<?php endif; ?>
<?php endif; ?>