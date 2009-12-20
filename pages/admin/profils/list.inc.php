<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('profil_list');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

// ===== La liste des profils =====
$sQuery = <<< EOQ
	SELECT
		PROFIL_ID,
		PROFIL_NAME,
		PROFIL_COMMENT,
		COUNT(PROFESSEUR_PROFIL_ID) USER_COUNT
	FROM PROFILS
		LEFT OUTER JOIN PROFESSEURS
			ON PROFESSEUR_PROFIL_ID = PROFIL_ID
	GROUP BY PROFIL_ID
	ORDER BY PROFIL_NAME
EOQ;
$aProfils = Database::fetchArray($sQuery);
// $aProfils[][Nom de colonne] = Valeur

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a><img src="<?php echo(URL_ICONS_16X16); ?>/profil.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Les profils : Liste des profils</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<br />
<table class="list_tree">
	<caption>Liste des profils existants</caption>
	<thead>
		<tr>
			<th><a href="?page=profils&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter" /></a></th>
			<th>Nom</th>
			<th>Description</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td><a href="?page=profils&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter" /></a></td>
			<td colspan="4"></td>
		</tr>
	</tfoot>
	<tbody<?php echo (count($aProfils) > 25) ? ' class="div_scrollable_large"' : ''; ?>>
		<?php foreach($aProfils as $nRowNum => $aOneProfil): ?>
		<tr class="row<?php echo($nRowNum % 2); ?>">
			<td></td>
			<td><a href="?page=profils&amp;mode=edit&amp;profil_id=<?php echo($aOneProfil['PROFIL_ID']); ?>"><?php echo($aOneProfil['PROFIL_NAME']); ?></a></td>
			<td><?php echo($aOneProfil['PROFIL_COMMENT']); ?></td>
			<td>
				<a href="?page=profils&amp;mode=edit&amp;profil_id=<?php echo($aOneProfil['PROFIL_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
				<?php if(($aOneProfil['PROFIL_ID'] >= 10) && ($aOneProfil['USER_COUNT'] == 0)): /* On ne peut pas supprimer le profil administrateur et on ne peut pas supprimer tout profil affecté à un utilisateur */ ?>
				<a href="?page=profils&amp;mode=delete&amp;profil_id=<?php echo($aOneProfil['PROFIL_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
				<?php endif; ?>
			</td>
			<td style="width:15px;"></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>