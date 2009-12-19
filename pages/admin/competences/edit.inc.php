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
$nCompetenceId = $oForm->getValue('competence_id', $_GET, 'convert_int');

// recupere l'id de l'eleve du formulaire $_GET
$nMatiereId = $oForm->getValue('matiere_id', $_GET, 'convert_int');

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des competences =====
$sQuery = "SELECT" .
		  "  COMPETENCE_ID, " .
		  "  COMPETENCE_NOM, " .
		  "  MATIERE_ID, " .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM COMPETENCES, MATIERES, DOMAINES, CYCLES " .
		  " WHERE COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID " .
		  " AND MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " AND COMPETENCE_ID = {$nCompetenceId} " .
		  " AND MATIERE_ID = {$nMatiereId} ";
$aCompetence = Database::fetchOneRow($sQuery);
// $aCompetence[COLONNE] = VALEUR

// ===== La liste des matieres pour l'affichage dans le select =====
$sQuery = "SELECT" .
		  "  MATIERE_ID," .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_NOM, " .
		  "  CYCLE_NOM " .
		  " FROM MATIERES, DOMAINES, CYCLES " .
		  " WHERE MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID " .
		  " ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC";
$aMatieres = Database::fetchArray($sQuery);
// $aMatieres[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>

<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Edition de la comp&eacute;tence</h1>
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=competences&amp;mode=edit_do">
	<table border="1" class="formulaire">
		<caption>Modifier cette comp&eacute;tence</caption>
		<thead>
		</thead>
		<tbody>
			<tr>
				<td>Liste des mati&egrave;res</td>
				<td>
					<select name="ID_MATIERE">
						<?php foreach($aMatieres as $aMatiere): ?>
							<option value="<?php echo($aMatiere['MATIERE_ID']); ?>"<?php echo($aMatiere['MATIERE_ID'] == $nMatiereId ? ' selected="selected"':''); ?>><?php echo($aMatiere['CYCLE_NOM']." - ".$aMatiere['DOMAINE_NOM'] . " - " . $aMatiere['MATIERE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Comp&eacute;tence</td>
				<td>
					<textarea cols="50" rows="10" name="COMPETENCE_NOM"><?php echo($aCompetence['COMPETENCE_NOM']); ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="COMPETENCE_ID" value="<?php echo($aCompetence['COMPETENCE_ID']); ?>" />
					<input type="submit" name="action" value="Modifier" />
				</td>
				<td>
					<input type="submit" name="action" value="Annuler" />
				</td>
			</tr>
		</tbody>
	</table>
</form>
