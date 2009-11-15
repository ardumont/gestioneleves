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

// ===== La liste des professeurs pour l'affichage dans le select =====
$sQuery = <<< EOQ
	SELECT
		PROFESSEUR_ID,
		PROFESSEUR_NOM
	FROM PROFESSEURS
	ORDER BY PROFESSEUR_NOM ASC
EOQ;
$aProfesseurs = Database::fetchArray($sQuery);
// $aProfesseurs[][Colonne] = Valeur

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1>Ajout d'un professeur</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=professeurs&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter un professeur</caption>
		<tbody>
			<tr>
				<td>Nom du professeur</td>
				<td><input type="text" size="10" maxlength="<?php echo PROFESSEUR_NOM; ?>" name="PROFESSEUR_NOM" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
				<td></td>
			</tr>
		</tbody>
	</table>
</form>

<?php if($aProfesseurs != false): ?>
<table class="list_tree">
	<caption>Liste des classes</caption>
	<thead>
		<tr>
			<th>Professeurs</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aProfesseurs as $aProfesseur): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td><?php echo($aProfesseur['PROFESSEUR_NOM']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucun professeur n'a été renseigné à ce jour.
		</td>
	</tr>
</table>
<?php endif; ?>
