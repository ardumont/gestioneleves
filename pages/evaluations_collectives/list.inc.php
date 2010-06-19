<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('eval_col_list');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

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

$nPeriodeId = $oForm->getValue('PERIODE_ID', $_POST, 'convert_int', -1);
$nClasseId = $oForm->getValue('CLASSE_ID', $_POST, 'convert_int', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des eleves du professeur pour l'annee courante =====
$sQuery = <<< EOQ
	SELECT
		PERIODE_ID,
		PERIODE_NOM
	FROM PERIODES
	ORDER BY PERIODE_NOM ASC
EOQ;
$aPeriodes = Database::fetchArray($sQuery);
// $aPeriodes[][COLONNE] = VALEUR

// ===== La liste des classes pour l'annee scolaire du professeur logge =====
$sQuery = <<< EOQ
	SELECT
		CLASSE_ID,
		CLASSE_NOM,
		CLASSE_ANNEE_SCOLAIRE
	FROM CLASSES
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN PROFESSEURS
			ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
	WHERE PROFESSEURS.PROFESSEUR_ID = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC
EOQ;
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

// criteres de recherche
$sQueryPeriodeId = ($nPeriodeId != -1) ? " AND ID_PERIODE = {$nPeriodeId} " : "";
$sQueryClasseId = ($nClasseId != -1) ? " AND CLASSES.CLASSE_ID = {$nClasseId} " : "";

// ===== La liste des evaluations collectives a ce jour =====
$sQuery = <<< EOQ
	SELECT
		EVAL_COL_ID,
		EVAL_COL_NOM,
		EVAL_COL_DESCRIPTION,
		DATE_FORMAT(EVAL_COL_DATE, '%d/%m/%Y') AS EVAL_COL_DATE,
		PERIODE_NOM,
		CLASSE_NOM,
		CLASSE_ANNEE_SCOLAIRE
	FROM EVALUATIONS_COLLECTIVES
		INNER JOIN CLASSES
			ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PERIODES
			ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
	WHERE 1=1
	{$sRestrictionAnneeScolaire}
	{$sQueryPeriodeId}
	{$sQueryClasseId}
	ORDER BY PERIODE_NOM ASC
EOQ;
$aEvalCols = Database::fetchArray($sQuery);
// $aEvalCols[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Liste des évaluations collectives</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<div id="help" class="messagebox_info" style="display: none;">
	Par défaut, cette page affiche toutes les évaluations collectives saisies à ce jour par le professeur connecté.<br />
	Vous pouvez toutefois ne filtrer que par classe ou par période.<br />
	Pour cela, sélectionner une classe ou une période puis cliquer sur le bouton <i>Rechercher</i>.
	<br />&nbsp;
</div>

<form method="post" action="?page=evaluations_collectives" name="formulaire_eval_col" id="formulaire_eval_col">
	<table class="formulaire">
		<caption>Critères de recherche</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Liste des classes de l'année courante</td>
				<td>
					<select name="CLASSE_ID" onchange="document.getElementById('formulaire_eval_col').submit();">
						<option value="-1">-- Sélectionner une classe --</option>
						<?php foreach($aClasses as $aClasse): ?>
							<option value="<?php echo($aClasse['CLASSE_ID']); ?>"<?php echo($aClasse['CLASSE_ID'] == $nClasseId ? ' selected="selected"' :''); ?>><?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']. " - " . $aClasse['CLASSE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Liste des périodes</td>
				<td>
					<select name="PERIODE_ID" onchange="document.getElementById('formulaire_eval_col').submit();">
						<option value="-1">-- Sélectionner une période --</option>
						<?php foreach($aPeriodes as $aPeriode): ?>
							<option value="<?php echo($aPeriode['PERIODE_ID']); ?>"<?php echo($aPeriode['PERIODE_ID'] == $nPeriodeId ? ' selected="selected"' :''); ?>><?php echo($aPeriode['PERIODE_NOM']); ?></option>
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

<?php if(count($aEvalCols) <= 0): ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucune évaluation collective n'a été saisie à ce jour.<br />
			<a href="?page=evaluations_collectives&amp;mode=add">Ajouter une évaluation collective</a>
		</td>
	</tr>
</table>
<?php else: ?>
<table class="list_tree">
	<caption>Liste des évaluations</caption>
	<thead>
		<tr>
			<th><a href="?page=evaluations_collectives&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th>Périodes</th>
			<th>Classes</th>
			<th>Années scolaires</th>
			<th>Evaluations collectives</th>
			<th>Descriptions</th>
			<th>Dates</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><a href="?page=evaluations_collectives&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
			<th colspan="8"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aEvalCols as $nRowNum => $aEvalCol): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td></td>
			<td><?php echo($aEvalCol['PERIODE_NOM']); ?></td>
			<td><?php echo($aEvalCol['CLASSE_NOM']); ?></td>
			<td><?php echo($aEvalCol['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			<td><?php echo($aEvalCol['EVAL_COL_NOM']); ?></td>
			<td><pre><?php echo($aEvalCol['EVAL_COL_DESCRIPTION']); ?></pre></td>
			<td><?php echo($aEvalCol['EVAL_COL_DATE']); ?></td>
			<!-- Edition -->
			<td>
				<a href="?page=evaluations_collectives&amp;mode=edit&amp;eval_col_id=<?php echo($aEvalCol['EVAL_COL_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<!-- Suppression -->
			<td>
				<a href="?page=evaluations_collectives&amp;mode=delete&amp;eval_col_id=<?php echo($aEvalCol['EVAL_COL_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
