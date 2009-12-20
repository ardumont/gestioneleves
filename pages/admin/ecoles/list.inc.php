<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('ecole_list');
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
$sEcoleVille = $oForm->getValue('ville_nom', $_POST, 'convert_string', -1);
$sEcoleDept = $oForm->getValue('dept_nom', $_POST, 'convert_string', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

$sQueryVille = ($sEcoleVille != -1) ? " AND ECOLE_VILLE = " . Database::prepareString($sEcoleVille) : "";
$sQueryDept = ($sEcoleDept != -1) ? " AND ECOLE_VILLE = " . Database::prepareString($sEcoleDept) : "";

// ===== La liste des ecoles =====
if($sEcoleVille != -1 || $sEcoleDept != -1)
{
	$sQuery = <<< ____EOQ
		SELECT
			ECOLE_ID,
			ECOLE_NOM,
			ECOLE_VILLE,
			ECOLE_DEPARTEMENT
		FROM ECOLES
		WHERE 1=1
		{$sQueryVille}
		ORDER BY ECOLE_VILLE ASC, ECOLE_NOM ASC
____EOQ;
	$aEcoles = Database::fetchArray($sQuery);
	// $aEcoles[][COLONNE] = VALEUR
}

// ===== La liste des villes =====
$sQuery = <<< EOQ
	SELECT
		DISTINCT ECOLE_VILLE
	FROM ECOLES
	ORDER BY ECOLE_VILLE ASC
EOQ;
$aVilles = Database::fetchArray($sQuery);
// $aVilles[][COLONNE] = VALEUR

// ===== La liste des départements =====
$sQuery = <<< EOQ
	SELECT
		DISTINCT ECOLE_DEPARTEMENT
	FROM ECOLES
	ORDER BY ECOLE_DEPARTEMENT ASC
EOQ;
$aDepts = Database::fetchArray($sQuery);
// $aDepts[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Liste des &eacute;coles</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<div id="help" style="display: none;">
	<table class="formulaire">
		<caption>Fonctionnement</caption>
		<tr>
			<td>
				Par défaut, cette page liste les écoles existantes dans la base de données de l'application.<br />
				Veuillez tout d'abord renseigner les critères de recherche d'une école.<br />
				<br />
				Vous pouvez modifier une école en cliquant sur son nom.<br />
				Vous pouvez également ajouter une école en cliquant sur le + en haut à gauche du tableau.
				<br />&nbsp;
			</td>
		</tr>
	</table>
</div>

<form method="post" action="?page=ecoles" name="formulaire" id="formulaire">
	<table class="formulaire">
		<caption>Crit&eacute;res de recherche</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Villes</td>
				<td>
					<select name="ville_nom" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionner une ville --</option>
						<?php foreach($aVilles as $aVille): ?>
							<option value="<?php echo($aVille['ECOLE_VILLE']); ?>"<?php echo($aVille['ECOLE_VILLE'] == $sEcoleVille ? ' selected="selected"' :''); ?>><?php echo($aVille['ECOLE_VILLE']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Départements</td>
				<td>
					<select name="dept_nom" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionner un département --</option>
						<?php foreach($aDepts as $aDept): ?>
							<option value="<?php echo($aDept['ECOLE_DEPARTEMENT']); ?>"<?php echo($aDept['ECOLE_DEPARTEMENT'] == $sEcoleDept ? ' selected="selected"' :''); ?>><?php echo($aDept['ECOLE_DEPARTEMENT']); ?></option>
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

<?php if($aEcoles != false): ?>
<table class="list_tree">
	<thead>
		<tr>
			<th><a href="?page=ecoles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Ecoles</th>
			<th>Villes</th>
			<th>D&eacute;partements</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=ecoles&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="5"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aEcoles as $nRowNum => $aEcole): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><a href="?page=ecoles&amp;mode=edit&amp;ecole_id=<?php echo($aEcole['ECOLE_ID']); ?>"><?php echo($aEcole['ECOLE_NOM']); ?></a></td>
			<td><?php echo($aEcole['ECOLE_VILLE']); ?></td>
			<td><?php echo($aEcole['ECOLE_DEPARTEMENT']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=ecoles&amp;mode=edit&amp;ecole_id=<?php echo($aEcole['ECOLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=ecoles&amp;mode=delete&amp;ecole_id=<?php echo($aEcole['ECOLE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
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
			Aucun critère de recherche n'a été renseigné ou aucune école ne correspond au(x) critère(s) de recherche.<br />
			<a href="?page=ecoles&amp;mode=add">Ajouter une école</a>
		</td>
	</tr>
</table>
<?php endif; ?>