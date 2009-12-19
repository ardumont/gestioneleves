<?php
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

// recupere l'id de l'eleve du formulaire $_GET
$nClasseId = $oForm->getValue('classe_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La classe recherchée =====
$sQuery = "SELECT" .
		  "  CLASSE_ID," .
		  "  CLASSE_NOM, " .
		  "  PROFESSEUR_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE, " .
		  "  ECOLE_ID, " .
		  "  ECOLE_NOM, " .
		  "  ECOLE_VILLE, " .
		  "  PROFESSEUR_ID, " .
		  "  ID_NIVEAU, " .
		  "  ECOLE_DEPARTEMENT " .
		  " FROM CLASSES, PROFESSEUR_CLASSE, PROFESSEURS, ECOLES, NIVEAU_CLASSE " .
		  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID " .
		  " AND CLASSES.ID_ECOLE = ECOLES.ECOLE_ID " .
		  " AND CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE " .
		  " AND CLASSE_ID = {$nClasseId}" .
		  " ORDER BY CLASSE_NOM ASC";
$aClasse = Database::fetchOneRow($sQuery);
// $aClasse[COLONNE] = VALEUR
// id de l'école recherchée
$nEcoleId = $aClasse['ECOLE_ID'];

// ===== La liste des niveaux pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  NIVEAU_ID," .
		  "  NIVEAU_NOM " .
		  " FROM NIVEAUX, CYCLES " .
		  " WHERE NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC";
$aNiveaux = Database::fetchColumnWithKey($sQuery);
// $aNiveaux[NIVEAU_ID] = NIVEAU_NOM

// ===== La liste des ecoles pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  ECOLE_ID," .
		  "  ECOLE_NOM, " .
		  "  ECOLE_VILLE, " .
		  "  ECOLE_DEPARTEMENT " .
		  " FROM ECOLES " .
		  " ORDER BY ECOLE_VILLE ASC, ECOLE_NOM ASC";
$aEcoles = Database::fetchArray($sQuery);
// $aEcoles[][COLONNE] = VALEUR

// ===== La liste des professeurs pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  PROFESSEUR_ID," .
		  "  PROFESSEUR_NOM " .
		  " FROM PROFESSEURS " .
		  " ORDER BY PROFESSEUR_NOM ASC";
$aProfesseurs = Database::fetchColumnWithKey($sQuery);
// $aProfesseurs[PROFESSEUR_ID] = PROFESSEUR_NOM

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Edition d'une classe</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=classes&amp;mode=edit_do">
	<table class="formulaire">
		<caption>D&eacute;tail de la classe</caption>
		<tr>
			<td>Ecole</td>
			<td>
				<select name="ID_ECOLE">
					<?php foreach($aEcoles as $sEcole): ?>
						<option value="<?php echo($sEcole['ECOLE_ID']); ?>"<?php echo($sEcole['ECOLE_ID'] == $nEcoleId ? ' selected="selected"':''); ?>><?php echo($sEcole['ECOLE_NOM']." - ".$sEcole['ECOLE_VILLE']); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Professeur</td>
			<td>
				<select name="ID_PROFESSEUR">
					<?php foreach($aProfesseurs as $nKey => $sValue): ?>
						<option value="<?php echo($nKey); ?>"<?php echo($aClasse['PROFESSEUR_ID'] == $nKey ? ' selected="selected"':''); ?>><?php echo($sValue); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Niveau de la classe</td>
			<td>
				<select name="ID_NIVEAU">
					<?php foreach($aNiveaux as $nKey => $sValue): ?>
						<option value="<?php echo($nKey); ?>"<?php echo($aClasse['ID_NIVEAU'] == $nKey ? ' selected="selected"':''); ?>><?php echo($sValue); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Nom de la classe</td>
			<td><input type="text" name="CLASSE_NOM" value="<?php echo($aClasse['CLASSE_NOM']); ?>" size="<?php echo(CLASSE_NOM); ?>" maxlength="<?php echo(CLASSE_NOM); ?>" /></td>
		</tr>
		<tr>
			<td>Ann&eacute;e scolaire (AAAA-AAAA+1)</td>
			<td><input type="text" name="CLASSE_ANNEE_SCOLAIRE" value="<?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']); ?>" size="<?php echo(CLASSE_ANNEE_SCOLAIRE); ?>" maxlength="<?php echo(CLASSE_ANNEE_SCOLAIRE); ?>" /></td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="CLASSE_ID" value="<?php echo($aClasse['CLASSE_ID']) ?>" />
				<input type="submit" name="action" value="Modifier" />
			</td>
			<td>
				<input type="submit" name="action" value="Annuler" />
			</td>
		</tr>
	</table>
</form>
