<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//restriction sur l'annee scolaire courante
$sRestrictionAnneeScolaire =
	" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// recuperation des ids de restrictions de recherche
$nEleveId = $oForm->getValue('ELEVE_ID', $_POST, 'convert_int', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des eleves du professeur pour l'annee courante =====
$sQuery = <<< EOQ
	SELECT DISTINCT
		ELEVE_ID,
		ELEVE_NOM,
		CLASSE_ANNEE_SCOLAIRE,
		CLASSE_NOM
	FROM ELEVES
		INNER JOIN ELEVE_CLASSE
			ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
		INNER JOIN CLASSES
			ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
	WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
EOQ;
$aEleves = Database::fetchArray($sQuery);
// $aEleves[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Génération de livrets</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<table class="formulaire">
	<caption>Fonctionnement</caption>
	<tr>
		<td>
			Par défaut, cette page permet de générer des livrets pour un élève.<br />
			Vous sélectionnez la classe et l'élève puis vous lancez la génération du livret.<br />
		</td>
	</tr>
</table>

<form method="post" action="special.php?page=export_livret_eleve" name="formulaire_list" id="formulaire_list">
	<table class="formulaire">
		<caption>Critères de sélection</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Liste des élèves de l'année courante</td>
				<td>
					<select name="eleve_id" onchange="document.getElementById('formulaire_list').submit();">
						<option value="-1">-- Sélectionner un élève --</option>
						<?php foreach($aEleves as $aEleve): ?>
							<option value="<?php echo($aEleve['ELEVE_ID']); ?>"<?php echo($aEleve['ELEVE_ID'] == $nEleveId ? ' selected="selected"' :''); ?>><?php echo($aEleve['CLASSE_ANNEE_SCOLAIRE']. " - " . $aEleve['CLASSE_NOM'] . " - " . $aEleve['ELEVE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="action" value="Rechercher" /></td>
			</tr>
		</tbody>
	</table>
</form>
