<?php
//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Modification de la date =====
$objFormEleves = new FormValidation();

// soumission via post, typiquement une fois le bouton rechercher appuye.
$nClasseId = $objFormEleves->getValue('CLASSE_ID', $_POST, 'convert_int');

// soumission via get, typiquement apres l'activation ou desactivation d'un eleve
if($nClasseId == null)
{
	$nClasseId = $objFormEleves->getValue('classe_id', $_GET, 'convert_int');
}

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

// ===== La liste des classes =====
$sQuery = "SELECT" .
		  "  CLASSE_ID," .
		  "  CLASSE_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE, " .
		  "	 PROFESSEUR_NOM ".
		  " FROM CLASSES, PROFESSEUR_CLASSE, PROFESSEURS " .
		  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID " .
		  " ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC";
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

// classe par defaut est la premiere de la liste
if($nClasseId == null && $aClasses != false)
{
	$nClasseId = $aClasses[0]['CLASSE_ID'];
}

if($aClasses != false)
{
	// ===== Les informations de la classe =====
	$sQuery = "SELECT" .
			  "  CLASSE_NOM, " .
			  "  PROFESSEUR_NOM, " .
			  "  CLASSE_ANNEE_SCOLAIRE, " .
			  "  ECOLE_NOM, " .
			  "  ECOLE_VILLE, " .
			  "  ECOLE_DEPARTEMENT " .
			  " FROM CLASSES, PROFESSEUR_CLASSE, PROFESSEURS, ECOLES " .
			  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
			  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID " .
			  " AND CLASSES.ID_ECOLE = ECOLES.ECOLE_ID " .
			  " AND CLASSES.CLASSE_ID = {$nClasseId} " .
			  " ORDER BY CLASSE_NOM ASC";
	$aClasseRow = Database::fetchOneRow($sQuery);

	// ===== La liste des eleves de la classe =====
	$sQuery = "SELECT " .
			  "  ELEVE_ID," .
			  "  ELEVE_NOM, " .
			  "  DATE_FORMAT(ELEVE_DATE_NAISSANCE, '%d/%m/%Y') AS ELEVE_DATE_NAISSANCE, " .
			  "  ID_CLASSE, " .
			  "  ELEVE_ACTIF " .
			  " FROM ELEVES, ELEVE_CLASSE " .
			  " WHERE ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
			  " AND ELEVE_CLASSE.ID_CLASSE = {$nClasseId}" .
			  " ORDER BY ELEVE_NOM ASC";
	$aEleves = Database::fetchArray($sQuery);
	// $aEleves[][COLONNE] = VALEUR
}

//==============================================================================
// Préparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des &eacute;l&egrave;ves</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<?php if($aClasses != false): ?>
	<form method="post" action="?page=eleves" name="formulaire_eleve" id="formulaire_eleve">
		<table class="formulaire">
			<caption>Crit&eacute;res de recherche</caption>
			<tfoot>
			</tfoot>
			<tbody>
				<tr>
					<td>Classe</td>
					<td>
						<select name="CLASSE_ID" onchange="document.getElementById('formulaire_eleve').submit();">
							<?php foreach($aClasses as $aClasse): ?>
								<option value="<?php echo($aClasse['CLASSE_ID']); ?>"<?php echo($aClasse['CLASSE_ID'] == $nClasseId ? ' selected="selected"' :''); ?>><?php echo($aClasse['PROFESSEUR_NOM'] . " - " .$aClasse['CLASSE_ANNEE_SCOLAIRE']. " - " . $aClasse['CLASSE_NOM']); ?></option>
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
	<table class="list_tree">
		<caption>D&eacute;tails de la classe</caption>
		<thead>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
			<tr class="level0_row0">
				<th>Classe</th>
				<td><?php echo($aClasseRow['CLASSE_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<th>Ann&eacute;e scolaire</th>
				<td><?php echo($aClasseRow['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			</tr>
			<tr class="level0_row0">
				<th>Professeur</th>
				<td><?php echo($aClasseRow['PROFESSEUR_NOM']); ?></td>
			</tr>
			<tr class="level0_row1">
				<th>Ecole</th>
				<td><?php echo($aClasseRow['ECOLE_NOM']); ?></td>
			</tr>
			<tr class="level0_row0">
				<th>Ville</th>
				<td><?php echo($aClasseRow['ECOLE_VILLE']); ?></td>
			</tr>
			<tr class="level0_row1">
				<th>D&eacute;partement</th>
				<td><?php echo($aClasseRow['ECOLE_DEPARTEMENT']); ?></td>
			</tr>
		</tbody>
	</table>
	<br />
	<?php if(count($aEleves) > 0): ?>
		<table class="list_tree">
			<thead>
				<tr>
					<th>El&egrave;ves</th>
					<th>Dates de naissance</th>
					<th>Activit&eacute;s</th>
				</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
				<?php foreach($aEleves as $nRowNum => $aEleve): ?>
				<tr class="level0_row<?php echo(($nRowNum)%2); ?>">
					<td><?php echo($aEleve['ELEVE_NOM']); ?></td>
					<td><?php echo($aEleve['ELEVE_DATE_NAISSANCE']); ?></td>
					<td>
						<?php if($aEleve['ELEVE_ACTIF'] == 1): ?>
							Actif
						<?php else: ?>
							Non actif
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		Aucun &eacute;l&egrave;ve affect&eacute; &agrave; cette classe.
	<?php endif; ?>
<?php else: ?>
	Aucune classe n'a été renseignée à ce jour.<br />
	<a href="?page=classes&amp;mode=add">Ajouter une classe</a> puis <br />
	<a href="index.php?page=eleves&amp;mode=add">Ajouter des élèves</a>
<?php endif; ?>