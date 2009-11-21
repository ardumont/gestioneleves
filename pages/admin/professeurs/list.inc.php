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
$sQuery = <<< EOQ
	SELECT
		PROFESSEUR_ID,
		PROFESSEUR_NOM
	FROM PROFESSEURS
	ORDER BY PROFESSEUR_NOM ASC
EOQ;
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

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<div id="help" style="display: none;">
	<table class="formulaire">
		<caption>Fonctionnement</caption>
		<tr>
			<td>
				Par défaut, cette page affiche l'ensemble des professeurs qui utilisent l'application.<br />
				Vous pouvez modifier un professeur en cliquant sur le nom du professeur.<br />
				Pour ajouter un professeur, cliquer sur le plus en haut à gauche du tableau.
				<br />&nbsp;
			</td>
		</tr>
	</table>
</div>
<br />
<?php if($aProfesseurs != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=professeurs&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Professeurs</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=professeurs&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="3"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aProfesseurs as $nRowNum => $aProfesseur): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
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
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucun professeur n'a été renseigné à ce jour.<br />
			<a href="?page=professeurs&amp;mode=add">Ajouter un professeur</a>
		</td>
	</tr>
</table>
<?php endif; ?>