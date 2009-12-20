<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('profil_edit');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Les fonctions pour cette page
//==============================================================================

/**
 * Permet de d'afficher les coches pour les checkbox des droits.
 *
 * retourne une chaine vide ou checked="checked"
 *
 * @author Lionel SAURON
 * @version 1.0
 * @public
 *
 * @param $sRightCode(string) Le code du droit
 * @return (string) Code HTML pour la checkbox
 */
function isGuiChecked($sRightCode)
{
	// On récupere les variables déclarées en dehors de la fonction
	$aRights   = $GLOBALS['aRights'];
	$nProfilId = $GLOBALS['nProfilId'];

	// On coche si on trouve le code dans le tableau des droits
	$sHtmlCode = (in_array($sRightCode, $aRights) == true) ? "checked=\"checked\"" : "";

	// On coche si l'id du profil est le profil administrateur (id = 1)
	$sHtmlCode = ($nProfilId == 1) ? "checked=\"checked\"" : $sHtmlCode;

	return $sHtmlCode;
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
		1 EXIST
	FROM PROFILS
	WHERE PROFIL_ID = {$nProfilId}
_EOQ_;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant du profil \"{$nProfilId}\" n'est pas valide !");

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
		PROFIL_NAME,
		PROFIL_COMMENT
	FROM PROFILS
	WHERE PROFIL_ID = {$nProfilId}
_EOQ_;

$aThisProfil = Database::fetchOneRow($sQuery);
// $aThisProfil[Nom de colonne] = Valeur

$sQuery = <<< _EOQ_
	SELECT
		PROFIL_RIGHT
	FROM PROFILS_REL_RIGHTS
	WHERE PROFIL_ID = {$nProfilId}
_EOQ_;

$aRights = Database::fetchColumn($sQuery);
// $aRights[] = Valeur

//==============================================================================
// Préparation de l'affichage
//==============================================================================

// On grise si l'id du profil est le profil administrateur (id = 1)
$sGuiAdminDisabled = ($nProfilId == 1) ? "disabled=\"disabled\"" : "";

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a><img src="<?php echo(URL_ICONS_16X16); ?>/profil.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Les profils : Edition d'un profil</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=profils&amp;mode=edit_do">
	<table class="formulaire">
		<caption>Identité du profil</caption>
		<tbody>
			<tr>
				<th><label for="form_profil_name">Nom</label></th>
				<td><input id="form_profil_name" type="text" name="profil_name" size="30" maxlength="30" value="<?php echo($aThisProfil['PROFIL_NAME']); ?>" /></td>
			</tr>
			<tr>
				<th><label for="form_profil_comment">Description :</label></th>
				<td>
					<textarea id="form_profil_comment" name="profil_description" cols="50" rows="10"><?php echo($aThisProfil['PROFIL_COMMENT']); ?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	<fieldset>
		<legend>Droits standard pour le profil</legend>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Applications</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_application_use">Utiliser</label></th>
					<td><input id="form_rights_application_use" type="checkbox" name="profil_rights[application_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_application_list">Lister/Voir</label></th>
					<td><input id="form_rights_application_list" type="checkbox" name="profil_rights[application_list]" <?php echo(isGuiChecked('application_list')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_application_add">Ajouter</label></th>
					<td><input id="form_rights_application_add" type="checkbox" name="profil_rights[application_add]" <?php echo(isGuiChecked('application_add')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_application_edit">Modifier</label></th>
					<td><input id="form_rights_application_edit" type="checkbox" name="profil_rights[application_edit]" <?php echo(isGuiChecked('application_edit')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_application_delete">Supprimer</label></th>
					<td><input id="form_rights_application_delete" type="checkbox" name="profil_rights[application_delete]" <?php echo(isGuiChecked('application_delete')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Projets</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_project_use">Utiliser</label></th>
					<td><input id="form_rights_project_use" type="checkbox" name="profil_rights[project_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_list">Lister</label></th>
					<td><input id="form_rights_project_list" type="checkbox" name="profil_rights[project_list]" <?php echo(isGuiChecked('project_list')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_view">Voir</label></th>
					<td><input id="form_rights_project_view" type="checkbox" name="profil_rights[project_view]" <?php echo(isGuiChecked('project_view')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_add">Ajouter</label></th>
					<td><input id="form_rights_project_add" type="checkbox" name="profil_rights[project_add]" <?php echo(isGuiChecked('project_add')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_edit">Modifier</label></th>
					<td><input id="form_rights_project_edit" type="checkbox" name="profil_rights[project_edit]" <?php echo(isGuiChecked('project_edit')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_delete">Supprimer</label></th>
					<td><input id="form_rights_project_delete" type="checkbox" name="profil_rights[project_delete]" <?php echo(isGuiChecked('project_delete')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Tâches</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_task_use">Utiliser</label></th>
					<td><input id="form_rights_task_use" type="checkbox" name="profil_rights[task_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_list">Lister/Voir</label></th>
					<td><input id="form_rights_task_list" type="checkbox" name="profil_rights[task_list]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_add">Ajouter</label></th>
					<td><input id="form_rights_task_add" type="checkbox" name="profil_rights[task_add]" <?php echo(isGuiChecked('task_add')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_edit">Modifier</label></th>
					<td><input id="form_rights_task_edit" type="checkbox" name="profil_rights[task_edit]" <?php echo(isGuiChecked('task_edit')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_delete">Supprimer</label></th>
					<td><input id="form_rights_task_delete" type="checkbox" name="profil_rights[task_delete]" <?php echo(isGuiChecked('task_delete')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_change_project">Changer de projet</label></th>
					<td><input id="form_rights_task_change_project" type="checkbox" name="profil_rights[task_change_project]" <?php echo(isGuiChecked('task_change_project')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_change_parent">Attacher sur une autre tâche</label></th>
					<td><input id="form_rights_task_change_parent" type="checkbox" name="profil_rights[task_change_parent]" <?php echo(isGuiChecked('task_change_parent')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Activité/Congés</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_activity_use">Utiliser</label></th>
					<td><input id="form_rights_activity_use" type="checkbox" name="profil_rights[activity_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_activity_list">Lister/Voir</label></th>
					<td><input id="form_rights_activity_list" type="checkbox" name="profil_rights[activity_list]" <?php echo(isGuiChecked('activity_list')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_activity_edit">Saisir</label></th>
					<td><input id="form_rights_activity_edit" type="checkbox" name="profil_rights[activity_edit]" <?php echo(isGuiChecked('activity_edit')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire">
			<caption>Vues récapitulatives</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_summary_user">Utilisateur</label></th>
					<td><input id="form_rights_summary_user" type="checkbox" name="profil_rights[summary_user]" <?php echo(isGuiChecked('summary_user')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_summary_team">Equipe</label></th>
					<td><input id="form_rights_summary_team" type="checkbox" name="profil_rights[summary_team]" <?php echo(isGuiChecked('summary_team')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_summary_project">Projets</label></th>
					<td><input id="form_rights_summary_project" type="checkbox" name="profil_rights[summary_project]" <?php echo(isGuiChecked('summary_project')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_summary_project_detail">Projets détaillés</label></th>
					<td><input id="form_rights_summary_project_detail" type="checkbox" name="profil_rights[summary_project_detail]" <?php echo(isGuiChecked('summary_project_detail')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_summary_vision_globale">Vision globale des applications</label></th>
					<td><input id="form_rights_summary_vision_globale" type="checkbox" name="profil_rights[summary_vision_globale]" <?php echo(isGuiChecked('summary_vision_globale')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<fieldset>
		<legend>Droits d'administration pour le profil</legend>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Profils/Droits</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_profil_use">Utiliser</label></th>
					<td><input id="form_rights_profil_use" type="checkbox" name="profil_rights[profil_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_profil_list">Lister/Voir</label></th>
					<td><input id="form_rights_profil_list" type="checkbox" name="profil_rights[profil_list]" <?php echo(isGuiChecked('profil_list')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_profil_add">Ajouter</label></th>
					<td><input id="form_rights_profil_add" type="checkbox" name="profil_rights[profil_add]" <?php echo(isGuiChecked('profil_add')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_profil_edit">Modifier</label></th>
					<td><input id="form_rights_profil_edit" type="checkbox" name="profil_rights[profil_edit]" <?php echo(isGuiChecked('profil_edit')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_profil_delete">Supprimer</label></th>
					<td><input id="form_rights_profil_delete" type="checkbox" name="profil_rights[profil_delete]" <?php echo(isGuiChecked('profil_delete')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Utilisateurs</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_user_use">Utiliser</label></th>
					<td><input id="form_rights_user_use" type="checkbox" name="profil_rights[user_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_user_list">Lister/Voir</label></th>
					<td><input id="form_rights_user_list" type="checkbox" name="profil_rights[user_list]" <?php echo(isGuiChecked('user_list')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_user_add">Ajouter</label></th>
					<td><input id="form_rights_user_add" type="checkbox" name="profil_rights[user_add]" <?php echo(isGuiChecked('user_add')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_user_edit">Modifier</label></th>
					<td><input id="form_rights_user_edit" type="checkbox" name="profil_rights[user_edit]" <?php echo(isGuiChecked('user_edit')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_user_active">Activer/Désactiver</label></th>
					<td><input id="form_rights_user_active" type="checkbox" name="profil_rights[user_active]" <?php echo(isGuiChecked('user_active')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Tâches templates</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_ttask_use">Utiliser</label></th>
					<td><input id="form_rights_ttask_use" type="checkbox" name="profil_rights[ttask_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ttask_list">Lister/Voir</label></th>
					<td><input id="form_rights_ttask_list" type="checkbox" name="profil_rights[ttask_list]" <?php echo(isGuiChecked('ttask_list')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ttask_add">Ajouter</label></th>
					<td><input id="form_rights_ttask_add" type="checkbox" name="profil_rights[ttask_add]" <?php echo(isGuiChecked('ttask_add')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ttask_edit">Modifier</label></th>
					<td><input id="form_rights_ttask_edit" type="checkbox" name="profil_rights[ttask_edit]" <?php echo(isGuiChecked('ttask_edit')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ttask_delete">Supprimer</label></th>
					<td><input id="form_rights_ttask_delete" type="checkbox" name="profil_rights[ttask_delete]" <?php echo(isGuiChecked('ttask_delete')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Jours fériés</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_bank_holiday_use">Utiliser</label></th>
					<td><input id="form_rights_bank_holiday_use" type="checkbox" name="profil_rights[bank_holiday_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_bank_holiday_list">Lister/Voir</label></th>
					<td><input id="form_rights_bank_holiday_list" type="checkbox" name="profil_rights[bank_holiday_list]" <?php echo(isGuiChecked('bank_holiday_list')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_bank_holiday_add">Ajouter</label></th>
					<td><input id="form_rights_bank_holiday_add" type="checkbox" name="profil_rights[bank_holiday_add]" <?php echo(isGuiChecked('bank_holiday_add')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_bank_holiday_edit">Modifier</label></th>
					<td><input id="form_rights_bank_holiday_edit" type="checkbox" name="profil_rights[bank_holiday_edit]" <?php echo(isGuiChecked('bank_holiday_edit')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_bank_holiday_delete">Supprimer</label></th>
					<td><input id="form_rights_bank_holiday_delete" type="checkbox" name="profil_rights[bank_holiday_delete]" <?php echo(isGuiChecked('bank_holiday_delete')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Administration générale Droits</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_admin_calcul_denormalise">Champs dénormalisés</label></th>
					<td><input id="form_rights_admin_calcul_denormalise" type="checkbox" name="profil_rights[admin_calcul_denormalise]" <?php echo(isGuiChecked('admin_calcul_denormalise')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_calcul_arborescence">Arborescences des projets</label></th>
					<td><input id="form_rights_admin_calcul_arborescence" type="checkbox" name="profil_rights[admin_calcul_arborescence]" <?php echo(isGuiChecked('admin_calcul_arborescence')); ?> <?php echo($sGuiAdminDisabled); ?> /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<p>
		<input type="hidden" name="profil_id" value="<?php echo($nProfilId) ?>" />

		<input type="submit" name="action" value="Valider" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>