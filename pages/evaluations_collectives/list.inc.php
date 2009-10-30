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

$objForm = new FormValidation();

$nPeriodeId = $objForm->getValue('PERIODE_ID', $_POST, 'convert_int');
$nClasseId = $objForm->getValue('CLASSE_ID', $_POST, 'convert_int');

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des eleves du professeur pour l'annee courante =====
$sQuery = "SELECT " .
		  "  PERIODE_ID," .
		  "  PERIODE_NOM " .
		  " FROM PERIODES " .
		  " ORDER BY PERIODE_NOM ASC";
$aPeriodes = Database::fetchArray($sQuery);
// $aPeriodes[][COLONNE] = VALEUR

// ===== La liste des classes pour l'annee scolaire du professeur logge =====
$sQuery = "SELECT " .
		  "  CLASSE_ID," .
		  "  CLASSE_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE " .
		  " FROM CLASSES, PROFESSEUR_CLASSE, PROFESSEURS " .
		  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID " .
		  " AND PROFESSEURS.PROFESSEUR_ID = " . $_SESSION['PROFESSEUR_ID'] .
		  " {$sRestrictionAnneeScolaire} " .
		  " ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC";
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

// criteres de recherche
$sFiltres = "";
if($nPeriodeId != null && $nClasseId != null) {// eleve + classe
	// cette recherche peux ne rien donner
	// dans le cas ou l'eleve n'appartient pas a la classe selectionnee
	$sFiltres = " AND ID_PERIODE = {$nPeriodeId} " .
				" AND CLASSES.CLASSE_ID = {$nClasseId} ";
} else if($nClasseId != null) {//juste la classe
	$sFiltres = " AND CLASSES.CLASSE_ID = {$nClasseId} ";
} else if($nPeriodeId != null) {//juste l'eleve
	$sFiltres = " AND ID_PERIODE = {$nPeriodeId} ";
}

// ===== La liste des evaluations collectives a ce jour =====
$sQuery = "SELECT" .
		  "  EVAL_COL_ID, " .
		  "  EVAL_COL_NOM, " .
		  "  EVAL_COL_DESCRIPTION, " .
		  "  DATE_FORMAT(EVAL_COL_DATE, '%d/%m/%Y') AS EVAL_COL_DATE, " .
		  "  PERIODE_NOM, " .
		  "  CLASSE_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE " .
		  " FROM EVALUATIONS_COLLECTIVES, CLASSES, PERIODES " .
		  " WHERE EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " AND EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID " .
		  " {$sRestrictionAnneeScolaire} " .
		  " {$sFiltres} " .
		  " ORDER BY PERIODE_NOM ASC";
$aEvalCols= Database::fetchArray($sQuery);
// $aEvalCols[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des &eacute;valuations collectives</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<table class="formulaire">
	<caption>Fonctionnement</caption>
	<tr>
		<td>
Par d&eacute;faut, cette page affiche toutes les &eacute;valuations collectives saisies &agrave; ce
 jour par le professeur connect&eacute;.<br />
Vous pouvez toutefois ne filtrer que par classe ou par p&eacute;riode.<br />
Pour cela, s&eacute;lectionner une classe ou une p&eacute;riode puis cliquer
 sur le bouton <i>Rechercher</i>.<br />
Vous pouvez &eacute;galement filtrer sur la classe et la p&eacute;riode.
		</td>
	</tr>
</table>
<form method="post" action="?page=evaluations_collectives">
	<table class="formulaire">
		<caption>Crit&eacute;res de recherche</caption>
		<tfoot>
		</tfoot>
		<tbody>
			<tr>
				<td>Liste des classes de l'ann&eacute;e courante</td>
				<td>
					<select name="CLASSE_ID">
						<option value="0">-- S&eacute;lectionner une classe --</option>
						<?php foreach($aClasses as $aClasse): ?>
							<option value="<?php echo($aClasse['CLASSE_ID']); ?>"<?php echo($aClasse['CLASSE_ID'] == $nClasseId ? ' selected="selected"' :''); ?>><?php echo($aClasse['CLASSE_ANNEE_SCOLAIRE']. " - " . $aClasse['CLASSE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Liste des p&eacute;riodes</td>
				<td>
					<select name="PERIODE_ID">
						<option value="0">-- S&eacute;lectionner une p&eacute;riode --</option>
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
<br />
<?php if(count($aEvalCols) <= 0): ?>
	Aucune &eacute;valuation collective n'a &eacute;t&eacute; saisie &agrave; ce jour.
<?php else: ?>
<table class="list_tree">
	<caption>Liste des &eacute;valuations</caption>
	<thead>
		<tr>
			<th>Editer</th>
			<th>P&eacute;riodes</th>
			<th>Classes</th>
			<th>Ann&eacute;es scolaires</th>
			<th>Evaluations collectives</th>
			<th>Descriptions</th>
			<th>Dates</th>
			<th>Supprimer</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aEvalCols as $nRowNum => $aEvalCol): ?>
		<tr class="level0_row<?php echo($nRowNum%2); ?>">
			<td>
				<a href="?page=evaluations_collectives&amp;mode=edit&amp;eval_col_id=<?php echo($aEvalCol['EVAL_COL_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
			</td>
			<td><?php echo($aEvalCol['PERIODE_NOM']); ?></td>
			<td><?php echo($aEvalCol['CLASSE_NOM']); ?></td>
			<td><?php echo($aEvalCol['CLASSE_ANNEE_SCOLAIRE']); ?></td>
			<td><?php echo($aEvalCol['EVAL_COL_NOM']); ?></td>
			<td><pre><?php echo($aEvalCol['EVAL_COL_DESCRIPTION']); ?></pre></td>
			<td><?php echo($aEvalCol['EVAL_COL_DATE']); ?></td>
			<td>
				<a href="?page=evaluations_collectives&amp;mode=delete&amp;eval_col_id=<?php echo($aEvalCol['EVAL_COL_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.gif" alt="Supprimer" title="Supprimer" /></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
