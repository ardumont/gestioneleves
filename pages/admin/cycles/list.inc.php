<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('cycle_list');
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

// ===== La liste des cycles =====
$sQuery = <<< EOQ
	SELECT
		CYCLE_ID,
		CYCLE_NOM
	FROM CYCLES
	ORDER BY CYCLE_NOM ASC
EOQ;
$aCycles = Database::fetchArray($sQuery);
// $aCycles[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Liste des cycles</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<div id="help" class="messagebox_info" style="display: none;">
	Par défaut, cette page liste les cycles existants dans l'application.<br />
	<br />
	Vous pouvez modifier un cycle en cliquant sur le nom du cycle.<br />
	Vous pouvez également ajouter un cycle en cliquant sur le + en haut à gauche du tableau.
</div>

<?php if($aCycles != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=cycles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Cycles</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=cycles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="3"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aCycles as $nRowNum => $aCycle): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><a href="?page=cycles&amp;mode=edit&amp;cycle_id=<?php echo($aCycle['CYCLE_ID']); ?>"><?php echo($aCycle['CYCLE_NOM']); ?></a></td>
			<!-- Edition -->
			<td>
				<a href="?page=cycles&amp;mode=edit&amp;cycle_id=<?php echo($aCycle['CYCLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=cycles&amp;mode=delete&amp;cycle_id=<?php echo($aCycle['CYCLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<div class="messagebox_info">
	Aucun cycle n'a été renseigné à ce jour.<br />
	<a href="?page=cycles&amp;mode=add">Ajouter un cycle</a>
</div>
<?php endif; ?>