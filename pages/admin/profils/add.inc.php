<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('admin_profil_add');
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
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a><img src="<?php echo(URL_ICONS_16X16); ?>/profil.png"/><img src="<?php echo(URL_ICONS_16X16); ?>/head_sep.png"/>Les profils : Ajouter un profil</h1>

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
	<a href="javascript:void(0);" onclick="$('input[type=checkbox].editable').attr('checked', 'checked');">Sélectionner tout</a>&nbsp;
	<a href="javascript:void(0);" onclick="$('input[type=checkbox].editable').removeAttr('checked');">Désélectionner tout</a>
	<fieldset>
		<legend>Droits standards pour le profil</legend>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Profil</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_profil_use">Utiliser</label></th>
					<td><input id="form_rights_profil_use" type="checkbox" name="profil_rights[profil_use]" checked="checked" disabled="disabled" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Eleves</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_eleve_use">Utiliser</label></th>
					<td><input id="form_rights_eleve_use" type="checkbox" name="profil_rights[eleve_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eleve_list">Lister</label></th>
					<td><input id="form_rights_eleve_list" type="checkbox" name="profil_rights[eleve_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eleve_add">Ajouter</label></th>
					<td><input id="form_rights_eleve_add" type="checkbox" name="profil_rights[eleve_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eleve_edit">Modifier</label></th>
					<td><input id="form_rights_eleve_edit" type="checkbox" name="profil_rights[eleve_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eleve_active">Activer/Désactiver</label></th>
					<td><input id="form_rights_eleve_active" type="checkbox" name="profil_rights[eleve_active]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Evalusations Individuelles</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_eval_ind_use">Utiliser</label></th>
					<td><input id="form_rights_eval_ind_use" type="checkbox" name="profil_rights[eval_ind_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eval_ind_list">Lister</label></th>
					<td><input id="form_rights_eval_ind_list" type="checkbox" name="profil_rights[eval_ind_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eval_ind_add">Ajouter</label></th>
					<td><input id="form_rights_eval_ind_add" type="checkbox" name="profil_rights[eval_ind_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eval_ind_edit">Modifier</label></th>
					<td><input id="form_rights_eval_ind_edit" type="checkbox" name="profil_rights[eval_ind_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eval_ind_delete">Supprimer</label></th>
					<td><input id="form_rights_eval_ind_delete" type="checkbox" name="profil_rights[eval_ind_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Evaluations collectives</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_eval_col_use">Utiliser</label></th>
					<td><input id="form_rights_eval_col_use" type="checkbox" name="profil_rights[eval_col_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eval_col_list">Lister</label></th>
					<td><input id="form_rights_eval_col_list" type="checkbox" name="profil_rights[eval_col_list]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eval_col_add">Ajouter</label></th>
					<td><input id="form_rights_eval_col_add" type="checkbox" name="profil_rights[eval_col_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eval_col_edit">Modifier</label></th>
					<td><input id="form_rights_eval_col_edit" type="checkbox" name="profil_rights[eval_col_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_eval_col_delete">Supprimer</label></th>
					<td><input id="form_rights_eval_col_delete" type="checkbox" name="profil_rights[eval_col_delete]" class="editable" /></td>
				</tr>
				<tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Livrets</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_livret_use">Utiliser</label></th>
					<td><input id="form_rights_livret_use" type="checkbox" name="profil_rights[livret_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_livret_list">Lister</label></th>
					<td><input id="form_rights_livret_list" type="checkbox" name="profil_rights[livret_list]" checked="checked" disabled="disabled" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Consultations</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_consultation_use">Utiliser</label></th>
					<td><input id="form_rights_consultation_use" type="checkbox" name="profil_rights[consultation_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_consultation_list">Lister</label></th>
					<td><input id="form_rights_consultation_list" type="checkbox" name="profil_rights[consultation_list]" checked="checked" disabled="disabled" /></td>
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
					<th><label for="form_rights_admin_profil_use">Utiliser</label></th>
					<td><input id="form_rights_admin_profil_use" type="checkbox" name="admin_profil_rights[admin_profil_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_profil_list">Lister</label></th>
					<td><input id="form_rights_admin_profil_list" type="checkbox" name="admin_profil_rights[admin_profil_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_profil_add">Ajouter</label></th>
					<td><input id="form_rights_admin_profil_add" type="checkbox" name="admin_profil_rights[admin_profil_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_profil_edit">Modifier</label></th>
					<td><input id="form_rights_admin_profil_edit" type="checkbox" name="admin_profil_rights[admin_profil_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_profil_delete">Supprimer</label></th>
					<td><input id="form_rights_admin_profil_delete" type="checkbox" name="admin_profil_rights[admin_profil_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Professeurs</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_professeur_use">Utiliser</label></th>
					<td><input id="form_rights_professeur_use" type="checkbox" name="profil_rights[professeur_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_professeur_list">Lister</label></th>
					<td><input id="form_rights_professeur_list" type="checkbox" name="profil_rights[professeur_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_professeur_add">Ajouter</label></th>
					<td><input id="form_rights_professeur_add" type="checkbox" name="profil_rights[professeur_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_professeur_edit">Modifier</label></th>
					<td><input id="form_rights_professeur_edit" type="checkbox" name="profil_rights[professeur_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_professeur_active">Supprimer</label></th>
					<td><input id="form_rights_professeur_active" type="checkbox" name="profil_rights[professeur_active]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Ecoles</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_ecole_use">Utiliser</label></th>
					<td><input id="form_rights_ecole_use" type="checkbox" name="profil_rights[ecole_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ecole_list">Lister</label></th>
					<td><input id="form_rights_ecole_list" type="checkbox" name="profil_rights[ecole_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ecole_add">Ajouter</label></th>
					<td><input id="form_rights_ecole_add" type="checkbox" name="profil_rights[ecole_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ecole_edit">Modifier</label></th>
					<td><input id="form_rights_ecole_edit" type="checkbox" name="profil_rights[ecole_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_ecole_delete">Supprimer</label></th>
					<td><input id="form_rights_ecole_delete" type="checkbox" name="profil_rights[ecole_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Classes</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_classe_use">Utiliser</label></th>
					<td><input id="form_rights_classe_use" type="checkbox" name="profil_rights[classe_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_classe_list">Lister</label></th>
					<td><input id="form_rights_classe_list" type="checkbox" name="profil_rights[classe_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_classe_add">Ajouter</label></th>
					<td><input id="form_rights_classe_add" type="checkbox" name="profil_rights[classe_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_classe_edit">Modifier</label></th>
					<td><input id="form_rights_classe_edit" type="checkbox" name="profil_rights[classe_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_classe_delete">Supprimer</label></th>
					<td><input id="form_rights_classe_delete" type="checkbox" name="profil_rights[classe_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Eleves</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_admin_eleve_use">Utiliser</label></th>
					<td><input id="form_rights_admin_eleve_use" type="checkbox" name="profil_rights[admin_eleve_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_eleve_list">Lister</label></th>
					<td><input id="form_rights_admin_eleve_list" type="checkbox" name="profil_rights[admin_eleve_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_eleve_add">Ajouter</label></th>
					<td><input id="form_rights_admin_eleve_add" type="checkbox" name="profil_rights[admin_eleve_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_eleve_edit">Modifier</label></th>
					<td><input id="form_rights_admin_eleve_edit" type="checkbox" name="profil_rights[admin_eleve_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_admin_eleve_delete">Supprimer</label></th>
					<td><input id="form_rights_admin_eleve_delete" type="checkbox" name="profil_rights[admin_eleve_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Cycles</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_cycle_use">Utiliser</label></th>
					<td><input id="form_rights_cycle_use" type="checkbox" name="profil_rights[cycle_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_cycle_list">Lister</label></th>
					<td><input id="form_rights_cycle_list" type="checkbox" name="profil_rights[cycle_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_cycle_add">Ajouter</label></th>
					<td><input id="form_rights_cycle_add" type="checkbox" name="profil_rights[cycle_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_cycle_edit">Modifier</label></th>
					<td><input id="form_rights_cycle_edit" type="checkbox" name="profil_rights[cycle_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_cycle_delete">Supprimer</label></th>
					<td><input id="form_rights_cycle_delete" type="checkbox" name="profil_rights[cycle_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Niveaux</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_niveau_use">Utiliser</label></th>
					<td><input id="form_rights_niveau_use" type="checkbox" name="profil_rights[niveau_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_niveau_list">Lister</label></th>
					<td><input id="form_rights_niveau_list" type="checkbox" name="profil_rights[niveau_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_niveau_add">Ajouter</label></th>
					<td><input id="form_rights_niveau_add" type="checkbox" name="profil_rights[niveau_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_niveau_edit">Modifier</label></th>
					<td><input id="form_rights_niveau_edit" type="checkbox" name="profil_rights[niveau_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_niveau_delete">Supprimer</label></th>
					<td><input id="form_rights_niveau_delete" type="checkbox" name="profil_rights[niveau_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Domaines</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_domaine_use">Utiliser</label></th>
					<td><input id="form_rights_domaine_use" type="checkbox" name="profil_rights[domaine_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_domaine_list">Lister</label></th>
					<td><input id="form_rights_domaine_list" type="checkbox" name="profil_rights[domaine_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_domaine_add">Ajouter</label></th>
					<td><input id="form_rights_domaine_add" type="checkbox" name="profil_rights[domaine_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_domaine_edit">Modifier</label></th>
					<td><input id="form_rights_domaine_edit" type="checkbox" name="profil_rights[domaine_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_domaine_delete">Supprimer</label></th>
					<td><input id="form_rights_domaine_delete" type="checkbox" name="profil_rights[domaine_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Matières</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_matiere_use">Utiliser</label></th>
					<td><input id="form_rights_matiere_use" type="checkbox" name="profil_rights[matiere_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_matiere_list">Lister</label></th>
					<td><input id="form_rights_matiere_list" type="checkbox" name="profil_rights[matiere_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_matiere_add">Ajouter</label></th>
					<td><input id="form_rights_matiere_add" type="checkbox" name="profil_rights[matiere_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_matiere_edit">Modifier</label></th>
					<td><input id="form_rights_matiere_edit" type="checkbox" name="profil_rights[matiere_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_matiere_delete">Supprimer</label></th>
					<td><input id="form_rights_matiere_delete" type="checkbox" name="profil_rights[matiere_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Compétences</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_competence_use">Utiliser</label></th>
					<td><input id="form_rights_competence_use" type="checkbox" name="profil_rights[competence_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_competence_list">Lister</label></th>
					<td><input id="form_rights_competence_list" type="checkbox" name="profil_rights[competence_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_competence_add">Ajouter</label></th>
					<td><input id="form_rights_competence_add" type="checkbox" name="profil_rights[competence_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_competence_edit">Modifier</label></th>
					<td><input id="form_rights_competence_edit" type="checkbox" name="profil_rights[competence_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_competence_delete">Supprimer</label></th>
					<td><input id="form_rights_competence_delete" type="checkbox" name="profil_rights[competence_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Notes</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_note_use">Utiliser</label></th>
					<td><input id="form_rights_note_use" type="checkbox" name="profil_rights[note_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_note_list">Lister</label></th>
					<td><input id="form_rights_note_list" type="checkbox" name="profil_rights[note_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_note_add">Ajouter</label></th>
					<td><input id="form_rights_note_add" type="checkbox" name="profil_rights[note_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_note_edit">Modifier</label></th>
					<td><input id="form_rights_note_edit" type="checkbox" name="profil_rights[note_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_note_delete">Supprimer</label></th>
					<td><input id="form_rights_note_delete" type="checkbox" name="profil_rights[note_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Périodes</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_periode_use">Utiliser</label></th>
					<td><input id="form_rights_periode_use" type="checkbox" name="profil_rights[periode_use]" checked="checked" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_periode_list">Lister</label></th>
					<td><input id="form_rights_periode_list" type="checkbox" name="profil_rights[periode_list]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_periode_add">Ajouter</label></th>
					<td><input id="form_rights_periode_add" type="checkbox" name="profil_rights[periode_add]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_periode_edit">Modifier</label></th>
					<td><input id="form_rights_periode_edit" type="checkbox" name="profil_rights[periode_edit]" class="editable" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_periode_delete">Supprimer</label></th>
					<td><input id="form_rights_periode_delete" type="checkbox" name="profil_rights[periode_delete]" class="editable" /></td>
				</tr>
			</tbody>
		</table>
		<table class="formulaire" style="float:left; margin-right:10px;">
			<caption>Imports</caption>
			<tbody>
				<tr>
					<th><label for="form_rights_import_csv_cycle">CSV cycles</label></th>
					<td><input id="form_rights_import_csv_cycle" type="checkbox" name="profil_rights[import_csv_cycle]" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_import_xml_cycle">XML cycles</label></th>
					<td><input id="form_rights_import_xml_cycle" type="checkbox" name="profil_rights[import_xml_cycle]" disabled="disabled" /></td>
				</tr>
				<tr>
					<th><label for="form_rights_import_xml_classe">XML Classes</label></th>
					<td><input id="form_rights_import_xml_classe" type="checkbox" name="profil_rights[import_xml_classe]" disabled="disabled" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<p>
		<input type="submit" name="action" value="Valider" />
		<input type="submit" name="action" value="Annuler" />
	</p>
</form>