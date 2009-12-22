<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('admin_profil_delete');
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

$oForm = new FormValidation();

$oForm->read('profil_id', $_GET);
$oForm->testError0(null, 'exist',       "Il manque l'identifiant du profil !");
$oForm->testError0(null, 'blank',       "Il manque l'identifiant du profil !");
$oForm->testError0(null, 'convert_int', "L'identifiant du profil doit être un entier !");
$nProfilId = $oForm->get(null);

// ===== Vérification des valeurs =====

// S'il y a eu une erreur sur l'id, ça ne sert à rien d'aller plus loin.
if($oForm->hasError('profil_id') == true)
{
	Message::addErrorFromFormValidation($oForm->getError());

	// Rechargement
	header("Location: ?page=profils");
	return;
}

$sQuery = <<< _EOQ_
	SELECT
		1 EXIST,
		COUNT(USER_PROFIL_ID) USER_COUNT
	FROM PROFILS
		LEFT OUTER JOIN USERS ON USER_PROFIL_ID = PROFIL_ID
	WHERE PROFIL_ID = {$nProfilId}
	GROUP BY PROFIL_ID
_EOQ_;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant du profil \"{$nProfilId}\" n'est pas valide !");

$oForm->testError1('profil_id',    		'min_value_equal',  1, "Ce profil est un profil système. Il ne peut pas être supprimé !");
$oForm->testError1('query1.USER_COUNT', 'equal', 			0, "Des utilisateurs utilisent ce profil. Suppression impossible !");

// S'il y a eu une erreur, on ne va pas plus loin.
if($oForm->hasError() == true)
{
	Message::addErrorFromFormValidation($oForm->getError());

	// Retour page précédente
	header("Location: ?page=profils");
	return;
}

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

// ===== Information sur la donnée à traiter =====
$sQuery = <<< _EOQ_
	SELECT
		PROFIL_NAME
	FROM PROFILS
	WHERE PROFIL_ID = {$nProfilId}
_EOQ_;

$aThisProfil = Database::fetchOneRow($sQuery);
// $aThisProfil[Nom de colonne] = Valeur

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a><img src="<?php echo(URL_ICONS_16X16); ?>/profil.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Les profils : Suppression d'un profil</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=profils&amp;mode=delete_do">
	<fieldset>
		<legend>Confirmation</legend>
		<p>Etes-vous sûr de vouloir supprimer le profil "<?php echo($aThisProfil['PROFIL_NAME']); ?>" ?</p>
	</fieldset>
	<p>
		<input type="hidden" name="profil_id" value="<?php echo($nProfilId) ?>" />

		<input type="submit" name="action" value="Supprimer" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>