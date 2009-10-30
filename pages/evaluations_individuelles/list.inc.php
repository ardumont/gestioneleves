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

// recuperation de l'id de l'eleve une fois qu'il a ete trouve
// (via module de recherche)
$nEleveId = $objForm->getValue('ELEVE_ID', $_POST, 'convert_int');
$nClasseId = $objForm->getValue('CLASSE_ID', $_POST, 'convert_int');

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des eleves du professeur pour l'annee courante =====
$sQuery = "SELECT DISTINCT " .
		  "  ELEVE_ID," .
		  "  ELEVE_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE, " .
		  "  CLASSE_NOM " .
		  " FROM ELEVES, ELEVE_CLASSE, CLASSES, PROFESSEUR_CLASSE " .
		  " WHERE ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
		  " AND ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID" .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = " . $_SESSION['PROFESSEUR_ID'] .
		  " {$sRestrictionAnneeScolaire} " .
		  " ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC";
$aEleves = Database::fetchArray($sQuery);
// $aEleves[][COLONNE] = VALEUR

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
if($nEleveId != null && $nClasseId != null) {// eleve + classe
	// cette recherche peux ne rien donner
	// dans le cas ou l'eleve n'appartient pas a la classe selectionnee
	$sFiltres = " AND ELEVES.ELEVE_ID = {$nEleveId} " .
				" AND CLASSES.CLASSE_ID = {$nClasseId} ";
} else if($nClasseId != null) {//juste la classe
	$sFiltres = " AND CLASSES.CLASSE_ID = {$nClasseId} ";
} else if($nEleveId != null) {//juste l'eleve
	$sFiltres = " AND ELEVES.ELEVE_ID = {$nEleveId} ";
}

// ===== La liste des evaluations individuelles a ce jour =====
$sQuery = "SELECT" .
		  "  ELEVE_NOM, " .
		  "  CLASSE_NOM, " .
		  "  NOTE_NOM, " .
		  "  EVAL_IND_ID, " .
		  "  EVAL_IND_COMMENTAIRE, " .
		  "  COMPETENCE_NOM, " .
		  "  MATIERE_NOM, " .
		  "  DOMAINE_NOM " .
		  " FROM EVALUATIONS_INDIVIDUELLES, NOTES, ELEVES, ELEVE_CLASSE, CLASSES, " .
		  " COMPETENCES, MATIERES, DOMAINES, PROFESSEUR_CLASSE " .
		  " WHERE EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID " .
		  " AND EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID " .
		  " AND ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
		  " AND ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID " .
		  " AND CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
		  " AND EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID " .
		  " AND COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID " .
		  " AND MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID " .
		  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = " . $_SESSION['PROFESSEUR_ID'] .
		  " {$sRestrictionAnneeScolaire} " .
		  " {$sFiltres} " .
		  " ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC";
$aEvalInds= Database::fetchArray($sQuery);
// $aEvalInds[][COLONNE] = VALEUR	

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des &eacute;valuations individuelles</h1>

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
Par d&eacute;faut, cette page affiche toutes les &eacute;valuations individuelles saisies &agrave; ce
 jour par le professeur connect&eacute;.<br />
Vous pouvez toutefois ne filtrer que par classe ou par &eacute;l&egrave;ve.<br />
Pour cela, s&eacute;lectionner une classe ou un &eacute;l&egrave;ve puis cliquer
 sur le bouton <i>Rechercher</i>.<br />
Vous pouvez &eacute;galement filtrer sur la classe et l'&eacute;l&egrave;ve.<br />
Attention, toutefois, si l'&eacute;l&egrave;ve n'appartient pas &agrave; la 
classe, aucun r&eacute;sultat ne s'affichera.
		</td>
	</tr>
</table>
<form method="post" action="?page=evaluations_individuelles">
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
				<td>Liste des &eacute;l&egrave;ves de l'ann&eacute;e courante</td>
				<td>
					<select name="ELEVE_ID">
						<option value="0">-- S&eacute;lectionner un &eacute;l&egrave;ve --</option>
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
<br />
<?php if(count($aEvalInds) <= 0): ?>
	Aucune &eacute;valuation individuelle n'a &eacute;t&eacute; saisie &agrave; ce jour pour ces crit&egrave;res.
<?php else: ?>
	<table>
		<caption>Liste des &eacute;valuations individuelles</caption>
		<thead>
			<tr>
				<th>Editer</th>
				<th>El&egrave;ves</th>
				<th>Classes</th>
				<th>Domaines</th>
				<th>Mati&egrave;res</th>
				<th>Comp&eacute;tences</th>
				<th>Notes</th>
				<th>Commentaires</th>
				<th>Supprimer</th>
			</tr>
		</thead>
		<tfoot>
		</tfoot>
		<tbody>
			<?php foreach($aEvalInds as $nRowNum => $aEvalInd): ?>
			<tr class="ligne<?php echo($nRowNum%2); ?>">
				<td>
					<a href="?page=evaluations_individuelles&amp;mode=edit&amp;eval_ind_id=<?php echo($aEvalInd['EVAL_IND_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
				</td>
				<td><?php echo($aEvalInd['ELEVE_NOM']); ?></td>
				<td><?php echo($aEvalInd['CLASSE_NOM']); ?></td>
				<td><?php echo($aEvalInd['DOMAINE_NOM']); ?></td>
				<td><?php echo($aEvalInd['MATIERE_NOM']); ?></td>
				<td><?php echo($aEvalInd['COMPETENCE_NOM']); ?></td>
				<td><?php echo($aEvalInd['NOTE_NOM']); ?></td>
				<td><pre><?php echo($aEvalInd['EVAL_IND_COMMENTAIRE']); ?></pre></td>
				<td>
					<a href="?page=evaluations_individuelles&amp;mode=delete&amp;eval_ind_id=<?php echo($aEvalInd['EVAL_IND_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.gif" alt="Supprimer" title="Supprimer" /></a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>