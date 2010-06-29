<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('matiere_edit');
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

// recupere l'id de la matiere du formulaire $_GET
$nMatiereId = $oForm->getValue('matiere_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des domaines pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  DOMAINE_ID," .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM DOMAINES, CYCLES " .
		  " WHERE DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC";
$aDomaines = Database::fetchArray($sQuery);
// $aDomaines[][COLONNE] = VALEUR

// ===== La matiere =====
$sQuery = "SELECT" .
		  "  MATIERE_ID," .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_ID, " .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM" .
		  " FROM MATIERES, DOMAINES, CYCLES " .
		  " WHERE MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " AND MATIERES.MATIERE_ID = {$nMatiereId} ";
$aMatiere = Database::fetchOneRow($sQuery);
// $aMatiere[COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Edition d'une matière", $aObjectsToHide);

if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=matieres&amp;mode=edit_do">
	<table class="formulaire">
		<caption>D&eacute;tail de la mati&egrave;re</caption>
		<tr>
			<td>Liste des domaines</td>
			<td>
				<select name="ID_DOMAINE">
					<?php foreach($aDomaines as $aDomaine): ?>
						<option value="<?php echo($aDomaine['DOMAINE_ID']); ?>"><?php echo($aDomaine['CYCLE_NOM']." - ".$aDomaine['DOMAINE_NOM']); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Nom de la mati&egrave;re</td>
			<td><input type="text" name="MATIERE_NOM" size="100" maxlength="<?php echo(MATIERE_NOM); ?>" value="<?php echo($aMatiere['MATIERE_NOM']) ?>" /></td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="MATIERE_ID" value="<?php echo($aMatiere['MATIERE_ID']) ?>" />
				<input type="submit" value="Modifier" name="action" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
