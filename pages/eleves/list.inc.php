<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eleve_list');
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
$sQuery = <<< EOQ
	SELECT
		CLASSE_ID,
		CLASSE_NOM,
		CLASSE_ANNEE_SCOLAIRE,
		PROFESSEUR_NOM
	FROM CLASSES
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN PROFESSEURS
			ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
	WHERE PROFESSEURS.PROFESSEUR_ID = {$_SESSION['PROFESSEUR_ID']}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC
EOQ;
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
	$sQuery = <<< ____EOQ
		SELECT
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
		WHERE CLASSES.CLASSE_ID = {$nClasseId}
		ORDER BY CLASSE_NOM ASC
____EOQ;
	$aClasseRow = Database::fetchOneRow($sQuery);

	// ===== La liste des eleves de la classe =====
	$sQuery = <<< ____EOQ
		SELECT
			ELEVE_ID,
			ELEVE_NOM,
			DATE_FORMAT(ELEVE_DATE_NAISSANCE, '%d/%m/%Y') AS ELEVE_DATE_NAISSANCE,
			ID_CLASSE,
			ELEVE_ACTIF
		FROM ELEVES
			INNER JOIN ELEVE_CLASSE
				ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
		WHERE ELEVE_CLASSE.ID_CLASSE = {$nClasseId}
		ORDER BY ELEVE_NOM ASC
____EOQ;
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
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Liste des élèves</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />

<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<div id="help" class="messagebox_info" style="display: none;">
	Par défaut, cette page affiche tous les élèves par classe du professeur connecté.<br />
	Vous pouvez sélectionner une classe parmi la liste proposée puis cliquer sur le bouton <i>Rechercher</i>.<br />
	<br />
	Pour éditer un élève, cliquer sur le nom de l'élève puis modifier les propriétés désirées.<br />
	Pour ajouter un élève, cliquer sur le + en image dans l'angle en haut a gauche.
</div>

<?php if($aClasses != false): ?>
	<form method="post" action="?page=eleves" name="formulaire_eleve" id="formulaire_eleve">
		<table class="formulaire">
			<caption>Critéres de recherche</caption>
			<thead></thead>
			<tfoot></tfoot>
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
		<caption>Détails de la classe</caption>
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
				<th>Année scolaire</th>
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
				<th>Département</th>
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
					<th>Elèves</th>
					<th>Dates de naissance</th>
					<th>Activités</th>
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
		<table class="formulaire">
			<caption>Informations</caption>
			<tr>
				<td>
					Aucun élève affecté à cette classe.<br />
					<a href="?page=eleves&amp;mode=add">Ajouter un élève à cette classe.</a>
				</td>
			</tr>
		</table>
	<?php endif; ?>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucune classe n'a été affectée à ce professeur.<br />
			Vous devez d'abord <a href="admin.php?page=classes&amp;mode=add">créer une classe</a> puis l'affecter à ce professeur.
		</td>
	</tr>
</table>
<?php endif; ?>