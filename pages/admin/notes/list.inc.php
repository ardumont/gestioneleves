<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('note_list');
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

// ===== La liste des notes =====
$sQuery = <<< EOQ
	SELECT
		NOTE_ID,
		NOTE_NOM,
		NOTE_LABEL,
		NOTE_NOTE
	FROM NOTES
	ORDER BY NOTE_NOTE DESC
EOQ;
$aNotes = Database::fetchArray($sQuery);
// $aNotes[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Liste des notes</h1>

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
	Par défaut, cette page affiche l'ensemble des notes existantes dans l'application.<br />
	<br />
	Vous ne pouvez pas modifier les notes, ni en ajouter ou en supprimer.<br />
	En effet, des modules de l'application en dépendent (le calcul des moyennes, l'affichage des livrets, etc...).<br />&nbsp;
</div>
<br /><br />

<?php if($aNotes != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th></th>
			<th>Notes</th>
			<th>Labels</th>
			<th>Coefficients</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th></th>
			<th colspan="3"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aNotes as $nRowNum => $aNote): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><?php echo($aNote['NOTE_NOM']); ?></td>
			<td><?php echo($aNote['NOTE_LABEL']); ?></td>
			<td><?php echo($aNote['NOTE_NOTE']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucune note n'a été renseignée à ce jour.<br />
			<a href="?page=notes&amp;mode=add">Ajouter une note</a>
		</td>
	</tr>
</table>
<?php endif; ?>