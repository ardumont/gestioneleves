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
$sQuery = <<< EOQ
	SELECT
		ECOLE_ID,
		ECOLE_NOM, 
		ECOLE_VILLE, 
		ECOLE_DEPARTEMENT 
	FROM ECOLES 
	ORDER BY ECOLE_VILLE ASC, ECOLE_NOM ASC
EOQ;
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

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<br />
<table class="formulaire">
	<caption>Fonctionnement</caption>
	<tr>
		<td>
Par défaut, cette page liste les écoles existantes dans l'application.<br />
<br />
Vous pouvez modifier une classe en cliquant sur le nom de l'école.<br />
Vous pouvez également ajouter une école en cliquant sur le + en haut à gauche du tableau.<br />
		</td>
	</tr>
</table>
<br />

<?php if($aEcoles != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=ecoles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Ecoles</th>
			<th>Villes</th>
			<th>D&eacute;partements</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=ecoles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="5"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aEcoles as $nRowNum => $aEcole): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><a href="?page=ecoles&amp;mode=edit&amp;ecole_id=<?php echo($aEcole['ECOLE_ID']); ?>"><?php echo($aEcole['ECOLE_NOM']); ?></a></td>
			<td><?php echo($aEcole['ECOLE_VILLE']); ?></td>
			<td><?php echo($aEcole['ECOLE_DEPARTEMENT']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=ecoles&amp;mode=edit&amp;ecole_id=<?php echo($aEcole['ECOLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=ecoles&amp;mode=delete&amp;ecole_id=<?php echo($aEcole['ECOLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucune école n'a été renseignée à ce jour.<br />
	<a href="?page=ecoles&amp;mode=add">Ajouter une école</a>
<?php endif; ?>