<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('consultation_list');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

// Restriction sur l'annee scolaire courante
$sRestrictionAnneeScolaire =
	" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Création du formulaire =====
$oForm = new FormValidation();

// La classe
$nClasseId = $oForm->getValue('classe_id', $_POST, 'convert_int', -1);
// La compétence sur laquelle porte la synthèse
$nCompetenceId = $oForm->getValue('competence_id', $_POST, 'convert_int', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des classes =====
$sQuery = <<< EOQ
	SELECT
		CLASSE_ID,
		CONCAT(PROFESSEUR_NOM, ' - ', CLASSE_ANNEE_SCOLAIRE , ' - ', CLASSE_NOM) AS CLASSE_NOM
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

// ===== La liste des compétences =====
$sQuery = <<< EOQ
	SELECT
		DISTINCT COMPETENCE_ID,
		CONCAT(DOMAINE_NOM, ' - ', MATIERE_NOM, ' - ', COMPETENCE_NOM) AS COMPETENCE_NOM
	FROM EVALUATIONS_INDIVIDUELLES
		INNER JOIN COMPETENCES
			ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
		INNER JOIN EVALUATIONS_COLLECTIVES
			ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
		INNER JOIN CLASSES
			ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN MATIERES
				ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
		INNER JOIN DOMAINES
			ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
	WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
EOQ;
$aCompetences = Database::fetchArray($sQuery);
// $aCompetences[][COLONNE] = VALEUR

// Si nous possédons la classe, la période et la compétence, on peut charger la synthèse
if($nClasseId != -1 && $nCompetenceId != -1)
{
	// Calcule la moyenne pour un élève
	$aRes = Livret::recap_annuel_competence($nClasseId, $nCompetenceId);

	// Détail des informations calculés
	$aNotes = $aRes['NOTES'];
	$aPeriodes = $aRes['PERIODES'];
	$sClasseNom = $aRes['CLASSE_NOM'];
	$sCompetenceNom = $aRes['COMPETENCE_NOM'];
	$aEvalInds = $aRes['EVAL_INDS'];
} else {
	$aEvalInds = false;
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Synthèse annuelle de la compétence évaluée", $aObjectsToHide);
?>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<?php if($aEvalInds != false): ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="special.php?page=consultation_annuel_competence&amp;classe_id=<?php echo $nClasseId; ?>&amp;competence_id=<?php echo $nCompetenceId; ?>">Version imprimable</a>
<?php endif ?>

<div id="help" class="messagebox_info" style="display: none;">
	Cette page permet d'afficher une vue qui synthétise, pour une compétence évaluée choisie, les moyennes des évaluations individuelles
	pour chacun des élèves d'une classe du professeur connecté.<br />
	Pour cela, sélectionner la compétence et la classe puis
	cliquer sur le bouton <i>Rechercher</i> pour que la page se rafraîchisse.<br />
</div>

<form method="post" action="?page=consultations&amp;mode=competences_annuel" name="formulaire" id="formulaire">
	<table class="formulaire">
		<caption>Crit&eacute;res de recherche</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Compétences</td>
				<td>
					<select name="competence_id">
						<option value="-1">-- Sélectionnez une compétence évaluée --</option>
						<?php foreach($aCompetences as $aCompetence): ?>
							<option value="<?php echo($aCompetence['COMPETENCE_ID']); ?>"<?php echo ($nCompetenceId == $aCompetence['COMPETENCE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aCompetence['COMPETENCE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Classes</td>
				<td>
					<select name="classe_id">
						<option value="-1">-- Sélectionnez une classe --</option>
						<?php foreach($aClasses as $aClasse): ?>
							<option value="<?php echo($aClasse['CLASSE_ID']); ?>"<?php echo($aClasse['CLASSE_ID'] == $nClasseId ? ' selected="selected"' :''); ?>><?php echo($aClasse['CLASSE_NOM']); ?></option>
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
<?php if($aEvalInds != false): ?>
	<table class="formulaire">
		<caption>Moyenne par élève des évaluations individuelles sur la compétence<br />'<?php echo $sCompetenceNom; ?>'</caption>
		<tbody>
			<tr>
				<td>
					<!-- Les pages d'évaluations individuelles -->
					<table class="entete_n">
						<thead></thead>
						<tfoot></tfoot>
						<thead>
							<tr>
								<?php foreach($aNotes as $aNote): ?>
								<td class="<?php echo $aNote['NOTE_LABEL']; ?>"><?php echo $aNote['NOTE_LABEL']; ?> = <?php echo $aNote['NOTE_NOM']; ?></td>
								<?php endforeach; ?>
							</tr>
						</thead>
					</table>

					<table class="display">
						<tr><!-- 1ère ligne de titre -->
							<td></td>
							<td class="colonne1" colspan="<?php echo count($aPeriodes); ?>?>">classe <?php echo $sClasseNom; ?></td>
						</tr>
						<tr><!-- 2ème ligne de titre -->
							<td>Livret n°</td>
							<?php foreach($aPeriodes as $sPeriodeNom):?>
							<td class="colonne1" style="width: 25px;"><?php echo $sPeriodeNom; ?></td>
							<?php endforeach; ?>
						</tr>
						<?php $i = 0; ?>
						<?php foreach($aEvalInds as $sNomEleve => $aEvalsInds): ?>
							<tr class="row<?php echo $i%2; ?>">
								<td><?php echo $sNomEleve; ?></td>
								<?php foreach($aEvalsInds as $sPeriodeNom => $aNotes): ?>
									<?php if(isset($aNotes['NOTE_LABEL'])): ?>
									<td class="<?php echo $aNotes['NOTE_LABEL']; ?>" style="text-align: center;"><?php echo $aNotes['NOTE_LABEL']; ?></td>
									<?php else: ?>
									<td class="colonne<?php echo ($i+1)%2; ?>">&nbsp;</td>
									<?php endif; ?>
							<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
<?php else: ?>
<div class="messagebox_info">
	Aucun critère de recherche n'a été renseigné ou aucune recherche ne correspond au(x)
	critère(s) de recherche.<br />
</div>
<?php endif; ?>