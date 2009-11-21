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

$oForm = new FormValidation();

// Recuperation des ids de restrictions de recherche
$oForm->read('eleve_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ eleve_id !");
$oForm->testError0(null, 'blank',       "Il manque l'id de l'élève !");
$oForm->testError0(null, 'convert_int', "L'identifiant de l'élève doit être un entier !");
$nEleveId = $oForm->get(null, -1);

$oForm->read('periode_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ periode_id !");
$oForm->testError0(null, 'blank',       "Il manque l'id de la période !");
$oForm->testError0(null, 'convert_int', "L'identifiant de la période doit être un entier !");
$nPeriodeId = $oForm->get(null, -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

// ===== Vérification des valeurs =====

$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM ELEVES
	WHERE ELEVE_ID = {$nEleveId}
EOQ;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant de l'élève \"{$nEleveId}\" n'est pas valide !");

$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PERIODES
	WHERE PERIODE_ID = {$nPeriodeId}
EOQ;

$oForm->readArray('query2', Database::fetchOneRow($sQuery));
$oForm->testError0('query2.EXIST', 'exist', "L'identifiant de la période \"{$nPeriodeId}\" n'est pas valide !");

//if($oForm->hasError() == true)
//{
//	// rechargement de la liste des eleves
//	header("Location: ?page=livrets&mode=recap_period");
//	return;
//}

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des eleves du professeur pour l'annee courante =====
$sQuery = <<< EOQ
	SELECT DISTINCT
		ELEVE_ID,
		ELEVE_NOM,
		CLASSE_ANNEE_SCOLAIRE,
		CLASSE_NOM
	FROM ELEVES
		INNER JOIN ELEVE_CLASSE
			ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
		INNER JOIN CLASSES
			ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
	WHERE PROFESSEUR_CLASSE.ID_PROFESSEUR = {$_SESSION['PROFESSEUR_ID']}
	{$sRestrictionAnneeScolaire}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
EOQ;
$aEleves = Database::fetchArray($sQuery);
// $aEleves[][COLONNE] = VALEUR

// ===== La liste des periodes =====
$sQuery = <<< EOQ
	SELECT
		PERIODE_NOM,
		PERIODE_ID
	FROM PERIODES
	ORDER BY PERIODE_NOM ASC
EOQ;
$aPeriodes = Database::fetchArray($sQuery);
// $aPeriodes[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

if($nEleveId != -1 && $nPeriodeId != -1)
{
	$aRes = Livret::recap_period($nEleveId, $nPeriodeId);

	$aEleveInfo = $aRes['ELEVE'];
	$aClassesEleve = $aRes['CLASSES_ELEVES'];
	$aNotes = $aRes['NOTES'];
//	$aNotesValues = $aRes['NOTES_VALUES'];
	$aPeriodesInfo = $aRes['PERIODES'];
	$aClassesNiveaux = $aRes['CLASSES_NIVEAUX'];
	$aDomainesMatieresCompetences = $aRes['DOMAINES_MATIERES_COMPETENCES'];
	$aEvalInds = $aRes['EVAL_INDS'];
	$aNomPrenom = $aRes['NOM_PRENOM'];
}

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Récapitulatif périodique d'un élève</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<table class="formulaire">
	<caption>Fonctionnement</caption>
	<tr>
		<td>
			Par défaut, cette page permet d'afficher un récapitulatif de l'activité périodique d'un élève de votre classe.<br />
			Vous sélectionnez l'élève de votre classe et la période désirée puis vous lancez l'affichage en cliquant sur
			le bouton <i>Afficher</i>.
		</td>
	</tr>
</table>

<form method="post" action="?page=livrets&amp;mode=recap_period" name="formulaire_list" id="formulaire_list">
	<table class="formulaire">
		<caption>Critères de sélection</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Liste des périodes</td>
				<td>
					<select name="periode_id">
						<option value="-1">-- Sélectionner une période --</option>
						<?php foreach($aPeriodes as $aPeriode): ?>
							<option value="<?php echo($aPeriode['PERIODE_ID']); ?>"<?php echo($aPeriode['PERIODE_ID'] == $nPeriodeId ? ' selected="selected"' :''); ?>><?php echo($aPeriode['PERIODE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Liste des élèves de l'année courante</td>
				<td>
					<select name="eleve_id">
						<option value="-1">-- Sélectionner un élève --</option>
						<?php foreach($aEleves as $aEleve): ?>
							<option value="<?php echo($aEleve['ELEVE_ID']); ?>"<?php echo($aEleve['ELEVE_ID'] == $nEleveId ? ' selected="selected"' :''); ?>><?php echo($aEleve['CLASSE_ANNEE_SCOLAIRE']. " - " . $aEleve['CLASSE_NOM'] . " - " . $aEleve['ELEVE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="action" value="Afficher" /></td>
			</tr>
		</tbody>
	</table>
</form>

<?php if($nEleveId != -1 && $nPeriodeId != -1): ?>
	<?php if($aEvalInds != false): ?>
		<table class="formulaire">
			<caption>Compétences de fin de cycle <?php echo $aEleveInfo['CYCLE_NOM']; ?></caption>
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
									<td><?php echo $aNote['NOTE_LABEL']; ?> = <?php echo $aNote['NOTE_NOM']; ?></td>
									<?php endforeach; ?>
								</tr>
							</thead>
						</table>

						<table class="display">
							<tr><!-- 1ère ligne de titre -->
								<td></td>
								<td class="colonne1" colspan="1">classe <?php echo $aClassesNiveaux['CLASSE_NOM']; ?> (<?php echo $aClassesNiveaux['NIVEAU_NOM']; ?>)</td>
							</tr>
							<tr><!-- 2ème ligne de titre -->
								<td>Livret n°</td>
								<td class="colonne1" style="width: 25px;"><?php echo $aPeriodesInfo['PERIODE_NOM']; ?></td>
							</tr>
							<?php foreach($aDomainesMatieresCompetences as $sDomaine => $aMatieres): ?>
							<tr style="font-size: 1.1em; background-color: #848484;"><!-- Ligne du domaine -->
								<td colspan="2" style="text-align: center;"><?php echo $sDomaine; ?></td>
							</tr>
								<?php foreach($aMatieres as $sMatiere => $aCompetences): ?>
								<tr style="font-size: 1em;"><!-- Ligne de la matière -->
									<td colspan="2" style="text-align: left; background-color: #848484;"><?php echo $sMatiere; ?></td>
								</tr>
									<?php foreach($aCompetences as $sCompetence => $aCompetence): /* Pour chaque compétence */ ?>
									<tr style="font-size: 0.9em;"><!-- Ligne de la competence -->
										<td><?php echo $sCompetence; ?></td>
										<td class="colonne1">
											<?php $sNiveauNom = $aClassesNiveaux['NIVEAU_NOM']; ?>
											<?php $sClasseNom = $aClassesNiveaux['CLASSE_NOM']; ?>
											<?php $sPeriodeNom = $aPeriodesInfo['PERIODE_NOM']; ?>
											<?php if(isset($aEvalInds[$sDomaine]) &&
												   isset($aEvalInds[$sDomaine][$sMatiere]) &&
												   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence]) &&
												   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom]) &&
												   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom]) &&
												   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]))
												{
													$aToDisplay = $aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom];
													echo $aToDisplay['NOTE_LABEL'];
												} else { ?>
													&nbsp;
												<?php } ?>
										</td>
									</tr>
									<?php endforeach; ?>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	<?php else: ?>
	<table class="formulaire">
		<caption>Informations</caption>
		<tr>
			<td>
				Aucune compétence n'a été évaluée pour cet élève sur cette période.
			</td>
		</tr>
	</table>
	<?php endif; ?>
<?php endif; ?>