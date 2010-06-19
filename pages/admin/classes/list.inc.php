<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('classe_list');
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

$oForm = new FormValidation();

// soumission via post, typiquement une fois le bouton rechercher appuye.
$sAnneeScolaire = $oForm->getValue('annee_scolaire', $_POST, 'convert_string', -1);
$nEcoleId = $oForm->getValue('ecole_id', $_POST, 'convert_int', -1);
$nProfesseurId = $oForm->getValue('professeur_id', $_POST, 'convert_int', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

$sQueryAnneeScolaire = ($sAnneeScolaire != -1) ? " AND CLASSE_ANNEE_SCOLAIRE = " . Database::prepareString($sAnneeScolaire) : "";
$sQueryEcole = ($nEcoleId != -1) ? " AND ECOLE_ID = {$nEcoleId}" : "";
$sQueryProfesseur = ($nProfesseurId != -1) ? " AND PROFESSEUR_ID = {$nProfesseurId}" : "";

if($sAnneeScolaire != -1 || $nEcoleId != -1 || $nProfesseurId != -1)
{
	// ===== La liste des classes =====
	$sQuery = <<< ____EOQ
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
		WHERE 1=1
		{$sQueryAnneeScolaire}
		{$sQueryEcole}
		{$sQueryProfesseur}
		ORDER BY CLASSE_NOM ASC
____EOQ;
	$aClasses = Database::fetchArray($sQuery);
	// $aClasses[][COLONNE] = VALEUR
}
// ===== La liste des années =====
$sQuery = <<< EOQ
	SELECT
		DISTINCT CLASSE_ANNEE_SCOLAIRE
	FROM CLASSES
	ORDER BY CLASSE_ANNEE_SCOLAIRE ASC
EOQ;
$aAnnees = Database::fetchArray($sQuery);
// $aAnnees[][COLONNE] = VALEUR

// ===== La liste des ecoles =====
$sQuery = <<< EOQ
	SELECT
		ECOLE_ID,
		CONCAT(ECOLE_VILLE, ' - ', ECOLE_NOM, ' - ', ECOLE_DEPARTEMENT) AS ECOLE_NOM
	FROM ECOLES
	ORDER BY ECOLE_VILLE ASC, ECOLE_NOM ASC
EOQ;
$aEcoles = Database::fetchArray($sQuery);
// $aEcoles[][COLONNE] = VALEUR

// ===== La liste des professeurs =====
$sQuery = <<< EOQ
	SELECT
		PROFESSEUR_ID,
		PROFESSEUR_NOM
	FROM PROFESSEURS
	ORDER BY PROFESSEUR_NOM ASC
EOQ;
$aProfesseurs = Database::fetchArray($sQuery);
// $aProfesseurs[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Liste des classes</h1>

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
	Par défaut, cette page liste les classes existantes dans l'application.<br />
	Veuillez tout d'abord renseigner les critères de recherche d'une classe.<br />
	<br />
	Vous pouvez modifier une classe en cliquant sur le nom de la classe.<br />
	Vous pouvez également ajouter une classe en cliquant sur le + en haut à gauche du tableau.
	<br />&nbsp;
</div>

<form method="post" action="?page=classes" name="formulaire" id="formulaire">
	<table class="formulaire">
		<caption>Crit&eacute;res de recherche</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Année scolaire</td>
				<td>
					<select name="annee_scolaire" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionner une année scolaire --</option>
						<?php foreach($aAnnees as $aAnnee): ?>
							<option value="<?php echo($aAnnee['CLASSE_ANNEE_SCOLAIRE']); ?>"<?php echo($aAnnee['CLASSE_ANNEE_SCOLAIRE'] == $sAnneeScolaire ? ' selected="selected"' :''); ?>><?php echo($aAnnee['CLASSE_ANNEE_SCOLAIRE']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Ecoles</td>
				<td>
					<select name="ecole_id" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionner une école --</option>
						<?php foreach($aEcoles as $aEcole): ?>
							<option value="<?php echo($aEcole['ECOLE_ID']); ?>"<?php echo($aEcole['ECOLE_ID'] == $nEcoleId ? ' selected="selected"' :''); ?>><?php echo($aEcole['ECOLE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Professeurs</td>
				<td>
					<select name="professeur_id" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionner un professeur --</option>
						<?php foreach($aProfesseurs as $aProfesseur): ?>
							<option value="<?php echo($aProfesseur['PROFESSEUR_ID']); ?>"<?php echo($aProfesseur['PROFESSEUR_ID'] == $nProfesseurId ? ' selected="selected"' :''); ?>><?php echo($aProfesseur['PROFESSEUR_NOM']); ?></option>
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

<?php if($aClasses != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=classes&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Professeurs</th>
			<th>Classes</th>
			<th>Ann&eacute;es scolaires</th>
			<th>Ecoles</th>
			<th>Villes</th>
			<th>D&eacute;partements</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=classes&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<td colspan="8"></td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aClasses as $nRowNum => $aClasse): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><?php echo($aClasse['PROFESSEUR_NOM']); ?></td>
			<td><a href="?page=classes&amp;mode=edit&amp;classe_id=<?php echo($aClasse['CLASSE_ID']); ?>"><?php echo($aClasse['CLASSE_NOM']); ?></a></td>
			<td><?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			<td><?php echo($aClasse['ECOLE_NOM']); ?></td>
			<td><?php echo($aClasse['ECOLE_VILLE']); ?></td>
			<td><?php echo($aClasse['ECOLE_DEPARTEMENT']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=classes&amp;mode=edit&amp;classe_id=<?php echo($aClasse['CLASSE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=classes&amp;mode=delete&amp;classe_id=<?php echo($aClasse['CLASSE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
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
			Aucun critère de recherche n'a été renseigné ou aucune classe ne correspond au(x) critère(s) de recherche.<br />
			<a href="?page=classes&amp;mode=add">Ajouter une classe</a>
		</td>
	</tr>
</table>
<?php endif; ?>