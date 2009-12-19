<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

//$bHasRight = ProfilManager::hasRight('profil_add');
//if($bHasRight == false)
//{
//	// Redirection
//	header("Location: ?page=no_rights");
//	return;
//}

//==============================================================================
// Préparation des données
//==============================================================================

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

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><img src="<?php echo(URL_ICONS_16X16); ?>/profil.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Les profils : Ajouter un profil</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=profils&amp;mode=add_do">
	<table class="formulaire">
		<caption>Identité du profil</caption>
		<tbody>
			<tr>
				<th><label for="form_profil_name">Nom</label></th>
				<td><input id="form_profil_name" type="text" name="profil_name" size="30" maxlength="30" /></td>
			</tr>
			<tr>
				<th><label for="form_profil_comment">Description :</label></th>
				<td>
					<textarea id="form_profil_comment" name="profil_description" cols="50" rows="10"></textarea>
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
					<td><input id="form_rights_application_list" type="checkbox" name="profil_rights[application_list]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_application_add">Ajouter</label></th>
					<td><input id="form_rights_application_add" type="checkbox" name="profil_rights[application_add]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_application_edit">Modifier</label></th>
					<td><input id="form_rights_application_edit" type="checkbox" name="profil_rights[application_edit]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_application_delete">Supprimer</label></th>
					<td><input id="form_rights_application_delete" type="checkbox" name="profil_rights[application_delete]" /></td>
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
					<td><input id="form_rights_project_list" type="checkbox" name="profil_rights[project_list]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_view">Voir</label></th>
					<td><input id="form_rights_project_view" type="checkbox" name="profil_rights[project_view]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_add">Ajouter</label></th>
					<td><input id="form_rights_project_add" type="checkbox" name="profil_rights[project_add]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_edit">Modifier</label></th>
					<td><input id="form_rights_project_edit" type="checkbox" name="profil_rights[project_edit]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_project_delete">Supprimer</label></th>
					<td><input id="form_rights_project_delete" type="checkbox" name="profil_rights[project_delete]" /></td>
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
					<td><input id="form_rights_task_add" type="checkbox" name="profil_rights[task_add]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_edit">Modifier</label></th>
					<td><input id="form_rights_task_edit" type="checkbox" name="profil_rights[task_edit]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_delete">Supprimer</label></th>
					<td><input id="form_rights_task_delete" type="checkbox" name="profil_rights[task_delete]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_change_project">Changer de projet</label></th>
					<td><input id="form_rights_task_change_project" type="checkbox" name="profil_rights[task_change_project]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_task_change_parent">Attacher sur une autre tâche</label></th>
					<td><input id="form_rights_task_change_parent" type="checkbox" name="profil_rights[task_change_parent]" /></td>
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
					<td><input id="form_rights_activity_list" type="checkbox" name="profil_rights[activity_list]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_activity_edit">Saisir</label></th>
					<td><input id="form_rights_activity_edit" type="checkbox" name="profil_rights[activity_edit]" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire">
			<caption>Vues récapitulatives</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_summary_user">Utilisateur</label></th>
					<td><input id="form_rights_summary_user" type="checkbox" name="profil_rights[summary_user]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_summary_team">Equipe</label></th>
					<td><input id="form_rights_summary_team" type="checkbox" name="profil_rights[summary_team]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_summary_project">Projets</label></th>
					<td><input id="form_rights_summary_project" type="checkbox" name="profil_rights[summary_project]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_summary_project_detail">Projets détaillés</label></th>
					<td><input id="form_rights_summary_project_detail" type="checkbox" name="profil_rights[summary_project_detail]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_summary_vision_globale">Vision globale des applications</label></th>
					<td><input id="form_rights_summary_vision_globale" type="checkbox" name="profil_rights[summary_vision_globale]" /></td>
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
					<td><input id="form_rights_profil_list" type="checkbox" name="profil_rights[profil_list]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_profil_add">Ajouter</label></th>
					<td><input id="form_rights_profil_add" type="checkbox" name="profil_rights[profil_add]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_profil_edit">Modifier</label></th>
					<td><input id="form_rights_profil_edit" type="checkbox" name="profil_rights[profil_edit]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_profil_delete">Supprimer</label></th>
					<td><input id="form_rights_profil_delete" type="checkbox" name="profil_rights[profil_delete]" /></td>
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
					<td><input id="form_rights_user_list" type="checkbox" name="profil_rights[user_list]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_user_add">Ajouter</label></th>
					<td><input id="form_rights_user_add" type="checkbox" name="profil_rights[user_add]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_user_edit">Modifier</label></th>
					<td><input id="form_rights_user_edit" type="checkbox" name="profil_rights[user_edit]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_user_active">Activer/Désactiver</label></th>
					<td><input id="form_rights_user_active" type="checkbox" name="profil_rights[user_active]" /></td>
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
					<td><input id="form_rights_ttask_list" type="checkbox" name="profil_rights[ttask_list]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ttask_add">Ajouter</label></th>
					<td><input id="form_rights_ttask_add" type="checkbox" name="profil_rights[ttask_add]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ttask_edit">Modifier</label></th>
					<td><input id="form_rights_ttask_edit" type="checkbox" name="profil_rights[ttask_edit]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ttask_delete">Supprimer</label></th>
					<td><input id="form_rights_ttask_delete" type="checkbox" name="profil_rights[ttask_delete]" /></td>
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
					<td><input id="form_rights_bank_holiday_list" type="checkbox" name="profil_rights[bank_holiday_list]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_bank_holiday_add">Ajouter</label></th>
					<td><input id="form_rights_bank_holiday_add" type="checkbox" name="profil_rights[bank_holiday_add]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_bank_holiday_edit">Modifier</label></th>
					<td><input id="form_rights_bank_holiday_edit" type="checkbox" name="profil_rights[bank_holiday_edit]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_bank_holiday_delete">Supprimer</label></th>
					<td><input id="form_rights_bank_holiday_delete" type="checkbox" name="profil_rights[bank_holiday_delete]" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Administration générale Droits</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_admin_calcul_denormalise">Champs dénormalisés</label></th>
					<td><input id="form_rights_admin_calcul_denormalise" type="checkbox" name="profil_rights[admin_calcul_denormalise]" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_calcul_arborescence">Arborescences des projets</label></th>
					<td><input id="form_rights_admin_calcul_arborescence" type="checkbox" name="profil_rights[admin_calcul_arborescence]" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<p>
		<input type="submit" name="action" value="Valider" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>