<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('professeur_edit');
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
// Action du formulaire
//==============================================================================

$oForm = new FormValidation();

// Récupère l'id de l'élève du formulaire $_GET
$nProfesseurId = $oForm->getValue('professeur_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// On ne fait plus de test s'il y a eu une erreur.
$oForm->setStopAll($oForm->hasError());

// Vérification de l'existence de toutes les tâches saisies
$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PROFESSEURS
	WHERE PROFESSEUR_ID = {$nProfesseurId}
EOQ;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant du professeur \"{$nProfesseurId}\" n'est pas valide !");

//==============================================================================
// Actions du formulaire
//==============================================================================

if($oForm->hasError() == true)
{
	// On stocke toutes les erreurs de formulaire.
	Message::addErrorFromFormValidation($oForm->getError());

	// Retourne sur la page appelante
	header("Location: ?page=professeurs");
	return;
}

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des professeurs pour l'affichage dans le select =====
$sQuery = <<< EOQ
	SELECT
  		PROFESSEUR_ID,
  		PROFESSEUR_NOM,
  		PROFESSEUR_PROFIL_ID
  	FROM PROFESSEURS
  		INNER JOIN PROFILS
  			ON PROFESSEUR_PROFIL_ID = PROFIL_ID
  	WHERE PROFESSEUR_ID={$nProfesseurId}
	ORDER BY PROFESSEUR_NOM ASC
EOQ;
$aProfesseur = Database::fetchOneRow($sQuery);
// $aProfesseur[Nom de colonne] = Valeur de colonne

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

echo h1("Edition d'un professeur", $aObjectsToHide);

if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=professeurs&amp;mode=edit_do">
	<table class="formulaire">
		<caption>D&eacute;tail du professeur</caption>
		<tr>
			<td>Nom</td>
			<td><input type="text" name="PROFESSEUR_NOM" value="<?php echo($aProfesseur['PROFESSEUR_NOM']); ?>" size="<?php echo(PROFESSEUR_NOM); ?>" maxlength="<?php echo(PROFESSEUR_NOM); ?>" /></td>
		</tr>
		<tr>
			<td>Profil</td>
			<td>
				<select name="profil_id">
					<?php foreach($aProfils as $aProfil): ?>
						<option value="<?php echo($aProfil['PROFIL_ID']); ?>"<?php echo ($aProfesseur['PROFESSEUR_PROFIL_ID'] == $aProfil['PROFIL_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aProfil['PROFIL_NAME']); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="PROFESSEUR_ID" value="<?php echo($aProfesseur['PROFESSEUR_ID']) ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
