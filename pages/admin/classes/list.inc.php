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
		CLASSE_ID,
		CLASSE_NOM,
		PROFESSEUR_NOM,
	 	CLASSE_ANNEE_SCOLAIRE,
		ECOLE_NOM,
		ECOLE_VILLE,
		ECOLE_DEPARTEMENT
	FROM CLASSES
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN PROFESSEURS
			ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
		INNER JOIN ECOLES
			ON CLASSES.ID_ECOLE = ECOLES.ECOLE_ID
	ORDER BY CLASSE_NOM ASC
EOQ;
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des classes</h1>

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
			Par défaut, cette page liste les classes existantes dans l'application.<br />
			<br />
			Vous pouvez modifier une classe en cliquant sur le nom de la classe.<br />
			Vous pouvez également ajouter une classe en cliquant sur le + en haut à gauche du tableau.<br />
		</td>
	</tr>
</table>
<br />

<?php if($aClasses != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=classes&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Professeurs</th>
			<th>Classes</th>
			<th>Ann&eacute;es scolaires</th>
			<th>Ecoles</th>
			<th>Villes</th>
			<th>D&eacute;partements</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=classes&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<td colspan="8"></td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aClasses as $nRowNum => $aClasse): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><?php echo($aClasse['PROFESSEUR_NOM']); ?></td>
			<td><a href="?page=classes&amp;mode=edit&amp;classe_id=<?php echo($aClasse['CLASSE_ID']); ?>"><?php echo($aClasse['CLASSE_NOM']); ?></a></td>
			<td><?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			<td><?php echo($aClasse['ECOLE_NOM']); ?></td>
			<td><?php echo($aClasse['ECOLE_VILLE']); ?></td>
			<td><?php echo($aClasse['ECOLE_DEPARTEMENT']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=classes&amp;mode=edit&amp;classe_id=<?php echo($aClasse['CLASSE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=classes&amp;mode=delete&amp;classe_id=<?php echo($aClasse['CLASSE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	Aucune classe n'a été renseignée à ce jour.<br />
	<a href="?page=classes&amp;mode=add">Ajouter une classe</a>
<?php endif; ?>