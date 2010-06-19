<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('periode_list');
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

// ===== La liste des periodes =====
$sQuery = <<< EOQ
	SELECT
		PERIODE_ID,
		PERIODE_NOM,
		PERIODE_DATE_DEBUT,
		PERIODE_DATE_FIN
	FROM PERIODES
	ORDER BY PERIODE_NOM ASC
EOQ;
$aPeriodes = Database::fetchArray($sQuery);
// $aPeriodes[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Liste des p&eacute;riodes</h1>

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
	Par défaut, cette page affiche l'ensemble des périodes existantes dans l'application.<br />
	<br />
	Vous pouvez modifier une période en cliquant sur son nom.<br />
	Vous pouvez également ajouter une période en cliquant sur le + en haut à gauche du tableau.
</div>

<?php if($aPeriodes != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=periodes&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>P&eacute;riode</th>
			<th>Dates de d&eacute;but</th>
			<th>Dates de fin</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=periodes&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="5"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aPeriodes as $nRowNum => $aPeriode): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><a href="?page=periodes&amp;mode=edit&amp;periode_id=<?php echo($aPeriode['PERIODE_ID']); ?>"><?php echo($aPeriode['PERIODE_NOM']); ?></a></td>
			<td><?php echo($aPeriode['PERIODE_DATE_DEBUT']); ?></td>
			<td><?php echo($aPeriode['PERIODE_DATE_FIN']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=periodes&amp;mode=edit&amp;periode_id=<?php echo($aPeriode['PERIODE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=periodes&amp;mode=delete&amp;periode_id=<?php echo($aPeriode['PERIODE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucune période n'a été renseignée à ce jour.<br />
			<a href="?page=periodes&amp;mode=add">Ajouter une période</a>
		</td>
	</tr>
</table>
<?php endif; ?>