<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('professeur_add');
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

// ===== La liste des professeurs pour l'affichage dans le select =====
$sQuery = <<< EOQ
	SELECT
		PROFESSEUR_ID,
		PROFESSEUR_NOM,
		PROFIL_NAME
	FROM PROFESSEURS
		INNER JOIN PROFILS
			ON PROFESSEUR_PROFIL_ID = PROFIL_ID
	ORDER BY PROFESSEUR_NOM ASC
EOQ;
$aProfesseurs = Database::fetchArray($sQuery);
// $aProfesseurs[][Colonne] = Valeur

// ===== La liste des profils =====
$sQuery = <<< EOQ
	SELECT
		PROFIL_ID,
		PROFIL_NAME
	FROM PROFILS
	ORDER BY PROFIL_NAME
EOQ;
$aProfils = Database::fetchArray($sQuery);
// $aProfils[][Nom de colonne] = Valeur

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Ajout d'un professeur</h1>

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
				<td>Profil</td>
				<td>
					<select name="profil_id">
						<option value="-1">-- Sélectionnez un profil --</option>
						<?php foreach($aProfils as $aProfil): ?>
							<option value="<?php echo($aProfil['PROFIL_ID']); ?>"<?php echo ($nProfilId == $aProfil['PROFIL_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aProfil['PROFIL_NAME']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
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
	<caption>Liste des professeurs</caption>
	<thead>
		<tr>
			<th>Professeurs</th>
			<th>Profils</th>
		</tr>
	</thead>
	<tfoot></tfoot>
	<tbody>
		<?php foreach($aProfesseurs as $aProfesseur): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td><?php echo($aProfesseur['PROFESSEUR_NOM']); ?></td>
			<td><?php echo($aProfesseur['PROFIL_NAME']); ?></td>
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
