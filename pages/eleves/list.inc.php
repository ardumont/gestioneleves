<?php
//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Modification de la date =====
$oForm = new FormValidation();

// soumission via post, typiquement une fois le bouton rechercher appuye.
$nClasseId = $oForm->getValue('CLASSE_ID', $_POST, 'convert_int');

// soumission via get, typiquement apres l'activation ou desactivation d'un eleve
if($nClasseId == null)
{
	$nClasseId = $oForm->getValue('classe_id', $_GET, 'convert_int');
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
		  " AND PROFESSEURS.PROFESSEUR_ID = " . $_SESSION['PROFESSEUR_ID'] .
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
	<form method="post" action="?page=eleves">
		<table class="formulaire">
			<caption>Crit&eacute;res de recherche</caption>
			<tfoot>
			</tfoot>
			<tbody>
				<tr>
					<td>Classe</td>
					<td>
						<select name="CLASSE_ID">
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
					<th><a href="?page=eleves&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
					<th>El&egrave;ves</th>
					<th>Dates de naissance</th>
					<th>Activit&eacute;s</th>
					<th colspan="2">Actions</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><a href="?page=eleves&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
					<th colspan="5"></th>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach($aEleves as $nRowNum => $aEleve): ?>
				<tr class="level0_row<?php echo(($nRowNum)%2); ?>">
					<td></td>
					<td>
						<a href="?page=eleves&amp;mode=edit&amp;eleve_id=<?php echo($aEleve['ELEVE_ID']); ?>">
							<?php echo($aEleve['ELEVE_NOM']); ?>
						</a>
					</td>
					<td><?php echo($aEleve['ELEVE_DATE_NAISSANCE']); ?></td>
					<td>
						<?php if($aEleve['ELEVE_ACTIF'] == 1): ?>
							<a href="?page=eleves&amp;mode=desactive&amp;eleve_id=<?php echo($aEleve['ELEVE_ID']); ?>">
								Actif
							</a>
						<?php else: ?>
							<a href="?page=eleves&amp;mode=active&amp;eleve_id=<?php echo($aEleve['ELEVE_ID']); ?>">
								Non actif
							</a>
						<?php endif; ?>
					</td>
					<!-- Edition -->
					<td>
						<a href="?page=eleves&amp;mode=edit&amp;eleve_id=<?php echo($aEleve['ELEVE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
					</td>
					<!-- Suppression -->
					<td>
						<a href="?page=eleves&amp;mode=delete&amp;eleve_id=<?php echo($aEleve['ELEVE_ID']); ?>&amp;classe_id=<?php echo($aEleve['ID_CLASSE']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		Aucun &eacute;l&egrave;ve affect&eacute; &agrave; cette classe.
	<?php endif; ?>
<?php else: ?>
	Aucune classe n'a &eacute;t&eacute; affect&eacute;e &agrave; ce professeur.<br />
	Vous devez d'abord <a href="admin.php?page=classes&amp;mode=add">cr&eacute;er une classe</a> puis l'affecter &agrave; ce professeur.
<?php endif; ?>