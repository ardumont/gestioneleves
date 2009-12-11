<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

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

// ===== La liste des classes =====
$sQuery = <<< EOQ
	SELECT
		CLASSE_ID,
		CLASSE_NOM, 
		PROFESSEUR_NOM, 
		CLASSE_ANNEE_SCOLAIRE, 
		ECOLE_NOM, 
		ECOLE_VILLE, 
		ECOLE_DEPARTEMENT 
	FROM CLASSES
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN PROFESSEURS
			ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
		INNER JOIN ECOLES
			ON CLASSES.ID_ECOLE = ECOLES.ECOLE_ID 
	ORDER BY CLASSE_NOM ASC
EOQ;
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1>Ajout d'une classe</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=classes&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter une classe</caption>
		<tbody>
			<tr>
				<td>Ecole</td>
				<td>
					<select name="ID_ECOLE">
						<?php foreach($aEcoles as $sEcole): ?>
							<option value="<?php echo($sEcole['ECOLE_ID']); ?>"><?php echo($sEcole['ECOLE_NOM']." - ".$sEcole['ECOLE_VILLE']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Professeur</td>
				<td>
					<select name="ID_PROFESSEUR">
						<?php foreach($aProfesseurs as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Niveau de la classe</td>
				<td>
					<select name="ID_NIVEAU">
						<?php foreach($aNiveaux as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Nom</td>
				<td><input type="text" size="10" maxlength="<?php echo CLASSE_NOM; ?>" name="CLASSE_NOM" /></td>
			</tr>
			<tr>
				<td>Ann&eacute;e scolaire (AAAA-AAAA+1)</td>
				<td><input type="text" size="10" maxlength="<?php echo CLASSE_ANNEE_SCOLAIRE; ?>" name="CLASSE_ANNEE_SCOLAIRE" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<?php if($aClasses != false): ?>
<table class="list_tree">
	<caption>Liste des classes</caption>
	<thead>
		<tr>
			<th>Professeurs</th>
			<th>Classes</th>
			<th>Ann&eacute;es scolaires</th>
			<th>Ecoles</th>
			<th>Villes</th>
			<th>D&eacute;partements</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aClasses as $nRowNum => $aClasse): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td><?php echo($aClasse['PROFESSEUR_NOM']); ?></td>
			<td><?php echo($aClasse['CLASSE_NOM']); ?></td>
			<td><?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			<td><?php echo($aClasse['ECOLE_NOM']); ?></td>
			<td><?php echo($aClasse['ECOLE_VILLE']); ?></td>
			<td><?php echo($aClasse['ECOLE_DEPARTEMENT']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucune classe n'a été renseignée à ce jour.
		</td>
	</tr>
</table>
<?php endif; ?>
