<?php
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

// ===== La liste des utilisateurs (pour la combobox) =====
$sQuery = <<< EOQ
	SELECT
		PROFESSEUR_ID,
		PROFESSEUR_NOM
	FROM PROFESSEURS
	ORDER BY PROFESSEUR_NOM
EOQ;
$aUsers = Database::fetchColumnWithKey($sQuery);
// $aUsers[Id] = Nom

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<?php if(!isset($_SESSION['PROFESSEUR_ID'])): /* utilisateur non connecté */ ?>
<h1><a href="javascript:void(0);" style="color:white;" onclick="$('#identification').toggle('slow');">Identification</a></h1>
<div id="identification">
	<form method="post" action="?page=login_do">
		<table>
			<tr>
				<td><label for="form_auth_name">Professeur</label></td>
				<td>
					<select id="form_auth_name" name="professeur_id">
					<?php foreach($aUsers as $nKey => $sValue): ?>
					<option value="<?php echo($nKey); ?>"<?php echo ( isset($_SESSION['PROFESSEUR_ID']) && ( $nKey == $_SESSION['PROFESSEUR_ID'] ) ) ? ' selected="selected"' : '';?>><?php echo($sValue); ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="form_auth_password">Mot de passe</label></td>
				<td><input type="password" size="15" maxlength="15" name="professeur_password" /></td>
			</tr>
		</table>
		<div>
			<!-- pour le trou du cul d'ie qui sinon n'integre pas le champ action dans le formulaire -->
			<input type="hidden" name="action" value="Valider" />
			<input type="submit" name="action" value="Valider" />
		</div>
	</form>
</div>
<?php else: /* Utilisateur connecté */ ?>
<h1><a href="javascript:void(0);" style="color:white;" onclick="$('#identification').toggle('slow');"><?php echo $_SESSION['PROFESSEUR_NOM']; ?></a></h1>
<div id="identification">
	<h4>
		<a href="?page=profils&amp;mode=edit&amp;professeur_id=<?php echo $_SESSION['PROFESSEUR_ID']; ?>">
			<img src="<?php echo(URL_ICONS_16X16); ?>/user.png" />Modification du profil
		</a>
	</h4>
	<h4>
		<a href="?page=logout_do">
			<img src="<?php echo(URL_ICONS_16X16); ?>/out.png" />Se d&eacute;connecter
		</a>
	</h4>
</div>
<?php endif; ?>
