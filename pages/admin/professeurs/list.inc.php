<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('professeur_list');
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
$nNiveauId = $oForm->getValue('niveau_id', $_POST, 'convert_int', -1);
$nEcoleId = $oForm->getValue('ecole_id', $_POST, 'convert_int', -1);
$sAnneeScolaire = $oForm->getValue('annee_scolaire', $_POST, 'convert_string', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des professeurs =====

$sQueryNiveau = ($nNiveauId != -1) ? " AND NIVEAU_ID = {$nNiveauId}" : "";
$sQueryEcole = ($nEcoleId != -1) ? " AND ECOLE_ID = {$nEcoleId}" : "";
$sQueryAnnee = ($sAnneeScolaire != -1) ? " AND CLASSE_ANNEE_SCOLAIRE = " . Database::prepareString($sAnneeScolaire) : "";

$sQuery = <<< EOQ
	SELECT
		DISTINCT PROFESSEUR_ID,
		PROFESSEUR_NOM,
		PROFIL_NAME
	FROM PROFESSEURS
		INNER JOIN PROFESSEUR_CLASSE
			ON PROFESSEURS.PROFESSEUR_ID = PROFESSEUR_CLASSE.ID_PROFESSEUR
		INNER JOIN CLASSES
			ON PROFESSEUR_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN NIVEAU_CLASSE
			ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
		INNER JOIN NIVEAUX
			ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
		INNER JOIN ECOLES
			ON CLASSES.ID_ECOLE = ECOLES.ECOLE_ID
		INNER JOIN PROFILS
			ON PROFIL_ID = PROFESSEUR_PROFIL_ID
	WHERE 1=1
	{$sQueryNiveau}
	{$sQueryEcole}
	{$sQueryAnnee}
	ORDER BY PROFESSEUR_NOM ASC
EOQ;
$aProfesseurs = Database::fetchArray($sQuery);
// $aProfesseurs[][COLONNE] = VALEUR

// ===== La liste des années =====
$sQuery = <<< EOQ
	SELECT
		DISTINCT CLASSE_ANNEE_SCOLAIRE
	FROM CLASSES
	ORDER BY CLASSE_ANNEE_SCOLAIRE ASC
EOQ;
$aAnnees = Database::fetchArray($sQuery);
// $aAnnees[][COLONNE] = VALEUR

// ===== La liste des niveaux =====
$sQuery = <<< EOQ
	SELECT
		NIVEAU_ID,
		NIVEAU_NOM
	FROM NIVEAUX
		INNER JOIN CYCLES
			ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
	ORDER BY CYCLE_NOM ASC
EOQ;
$aNiveaux = Database::fetchArray($sQuery);
// $aNiveaux[][COLONNE] = VALEUR

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

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Liste des professeurs</h1>

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
	Par défaut, cette page affiche les professeurs référencés dans l'application.<br />
	Veuillez tout d'abord renseigner les critères de recherche d'un professeur.<br />
	Vous pouvez modifier un professeur en cliquant sur le nom du professeur.<br />
	Pour ajouter un professeur, cliquer sur le plus en haut à gauche du tableau.
</div>

<form method="post" action="?page=professeurs" name="formulaire" id="formulaire">
	<table class="formulaire">
		<caption>Crit&eacute;res de recherche</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
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
				<td>Niveaux</td>
				<td>
					<select name="niveau_id" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionner un niveau --</option>
						<?php foreach($aNiveaux as $aNiveau): ?>
							<option value="<?php echo($aNiveau['NIVEAU_ID']); ?>"<?php echo($aNiveau['NIVEAU_ID'] == $nNiveauId ? ' selected="selected"' :''); ?>><?php echo($aNiveau['NIVEAU_NOM']); ?></option>
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

<?php if($aProfesseurs != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=professeurs&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Professeurs</th>
			<th>Profils</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=professeurs&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="4"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aProfesseurs as $nRowNum => $aProfesseur): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<!-- Professeur -->
			<td>
				<a href="?page=professeurs&amp;mode=edit&amp;professeur_id=<?php echo($aProfesseur['PROFESSEUR_ID']); ?>"><?php echo($aProfesseur['PROFESSEUR_NOM']); ?></a>
			</td>
			<!-- Profils -->
			<td><?php echo($aProfesseur['PROFIL_NAME']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=professeurs&amp;mode=edit&amp;professeur_id=<?php echo($aProfesseur['PROFESSEUR_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=professeurs&amp;mode=delete&amp;professeur_id=<?php echo($aProfesseur['PROFESSEUR_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<div class="messagebox_info">
	Aucun critère de recherche n'a été renseigné ou aucun professeur ne correspond au(x) critère(s) de recherche.<br />
	<a href="?page=professeurs&amp;mode=add">Ajouter un professeur</a>
</div>
<?php endif; ?>