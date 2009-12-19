<?php
//==============================================================================
// Préparation des données
//==============================================================================

// On récupère l'id de l'utilisateur rangé en session
$nUserId = $_SESSION['PROFESSEUR_ID'];

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Vérification des valeurs =====

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

// ===== Informations sur l'utilisateur =====
$sQuery = <<< EOQ
	SELECT
		PROFESSEUR_NOM,
		PROFESSEUR_ID
	FROM PROFESSEURS
	WHERE PROFESSEUR_ID = {$nUserId}
EOQ;

$aThisUser = Database::fetchOneRow($sQuery);
// $aThisUser[Nom de colonne] = Valeur

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a><img src="<?php echo(URL_ICONS_16X16); ?>/user.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Mon Profil</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=profils&amp;mode=edit_do">
	<table class="formulaire">
		<caption>Identité</caption>
		<tbody>
			<tr>
				<th>Nom</th>
				<td><?php echo($aThisUser['PROFESSEUR_NOM']); ?></td>
			</tr>
			<tr>
				<th><label for="form_user_pwd">Nouveau mot de passe</label></th>
				<td><input id="form_user_pwd" type="password" name="user_pwd" /></td>
			</tr>
			<tr>
				<th><label for="form_user_pwd_conf">Confirmation du nouveau mot de passe</label></th>
				<td><input id="form_user_pwd_conf" type="password" name="user_pwd_conf" /></td>
			</tr>
		</tbody>
	</table>
	<p>
		<input type="hidden" name="professeur_id" value="<?php echo $aThisUser['PROFESSEUR_ID']; ?>" />
		<input type="submit" name="action" value="Valider" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>
