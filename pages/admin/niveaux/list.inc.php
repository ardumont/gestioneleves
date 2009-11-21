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

// ===== La liste des niveaux =====
$sQuery = <<< EOQ
	SELECT
		NIVEAU_ID,
		NIVEAU_NOM,
		CYCLE_NOM
	FROM NIVEAUX
		INNER JOIN CYCLES
			ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
	ORDER BY CYCLE_NOM ASC
EOQ;
$aNiveaux = Database::fetchArrayWithKey($sQuery, 'CYCLE_NOM', false);
// $aNiveaux[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des niveaux</h1>

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
				Par défaut, cette page affiche l'ensemble des niveaux existants dans l'application.<br />
				<br />
				Vous pouvez modifier un niveau en cliquant sur son nom.<br />
				Vous pouvez également ajouter un niveau en cliquant sur le + en haut à gauche du tableau.
				<br />&nbsp;
			</td>
		</tr>
	</table>
</div>
<br />

<?php if($aNiveaux != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=niveaux&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Cycles</th>
			<th>Niveaux</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=niveaux&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="4"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php $nRowNum = 0; ?>
		<?php foreach($aNiveaux as $sCycleNom => $aNiveaux): ?>
			<!-- ligne du cycle -->
			<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
				<!-- ligne vide pour l'ajout -->
				<th></th>
				<!-- Nom du cycle -->
				<th><?php echo($sCycleNom); ?></th>
				<!-- Le reste -->
				<th colspan="3"></th>
			</tr>
			<?php foreach($aNiveaux as $aNiveau): ?>
				<!-- Ligne du niveau -->
				<tr class="level0_row<?php echo(($nRowNum++)%2); ?>">
					<!-- ligne vide pour l'ajout -->
					<td></td>
					<!-- Nom du cycle -->
					<td></td>
					<!-- Niveau -->
					<td><a href="?page=niveaux&amp;mode=edit&amp;niveau_id=<?php echo($aNiveau['NIVEAU_ID']); ?>"><?php echo($aNiveau['NIVEAU_NOM']); ?></a></td>
					<!-- Edition du niveau -->
					<td>
						<a href="?page=niveaux&amp;mode=edit&amp;niveau_id=<?php echo($aNiveau['NIVEAU_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
					</td>
					<!-- Suppression du niveau-->
					<td>
						<a href="?page=niveaux&amp;mode=delete&amp;niveau_id=<?php echo($aNiveau['NIVEAU_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
					</td>
				</tr>
			<?php endforeach;?>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucun niveau n'a été renseigné à ce jour.<br />
			<a href="?page=niveaux&amp;mode=add">Ajouter un niveau</a>
		</td>
	</tr>
</table>
<?php endif; ?>