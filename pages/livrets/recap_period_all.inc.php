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
$oForm->read('classe_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ classe_id !");
$oForm->testError0(null, 'blank',       "Il manque l'id de la classe !");
$oForm->testError0(null, 'convert_int', "L'identifiant de la classe doit être un entier !");
$nClasseId = $oForm->get(null, -1);

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
	FROM CLASSES
	WHERE CLASSE_ID = {$nClasseId}
EOQ;

$oForm->readArray('query1', Database::fetchOneRow($sQuery));
$oForm->testError0('query1.EXIST', 'exist', "L'identifiant de la classe \"{$nClasseId}\" n'est pas valide !");

$sQuery = <<< EOQ
	SELECT
		1 EXIST
	FROM PERIODES
	WHERE PERIODE_ID = {$nPeriodeId}
EOQ;

$oForm->readArray('query2', Database::fetchOneRow($sQuery));
$oForm->testError0('query2.EXIST', 'exist', "L'identifiant de la période \"{$nPeriodeId}\" n'est pas valide !");

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

$sQueryRestClasse = ($nClasseId != -1) ? " AND CLASSE_ID = {$nClasseId}" : "";

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
	{$sQueryRestClasse}
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

// ===== La liste des classes =====
$sQuery = <<< EOQ
	SELECT
		CLASSE_ID,
		CONCAT(PROFESSEUR_NOM, ' - ', CLASSE_ANNEE_SCOLAIRE , ' - ', CLASSE_NOM) AS CLASSE_NOM,
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
	ORDER BY CLASSE_NOM ASC
EOQ;
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

if($nClasseId != -1 && $nPeriodeId != -1)
{
	foreach($aEleves as $i => $oEleve)
	{
		$aRes = Livret::recap_period($oEleve['ELEVE_ID'], $nPeriodeId);

		$aEleveInfo[$i] = $aRes['ELEVE'];
		$aClassesEleve[$i] = $aRes['CLASSES_ELEVES'];
		$aNotes[$i] = $aRes['NOTES'];
		$aPeriodesInfo[$i] = $aRes['PERIODES'];
		$aClassesNiveaux[$i] = $aRes['CLASSES_NIVEAUX'];
		$aDomainesMatieresCompetences[$i] = $aRes['DOMAINES_MATIERES_COMPETENCES'];
		$aEvalInds[$i] = $aRes['EVAL_INDS'];
		$aNomPrenom[$i] = $aRes['NOM_PRENOM'];
	}
}

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Récapitulatif périodique des élèves de la classe</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<?php if($nClasseId != -1 && $nPeriodeId != -1): ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="special.php?page=export_livret_eleve_period_all&amp;classe_id=<?php echo $nClasseId; ?>&amp;periode_id=<?php echo $nPeriodeId; ?>">Version imprimable</a>
<?php endif ?>
<div id="help" style="display: none;">
	<table class="formulaire">
		<caption>Fonctionnement</caption>
		<tr>
			<td>
				Par défaut, cette page permet d'afficher un récapitulatif de l'activité périodique de tous les élèves de votre classe.<br />
				Vous sélectionnez la classe et la période désirée puis vous lancez l'affichage en cliquant sur
				le bouton <i>Afficher</i>.
				<br />
				Pour générer une version imprimable, cliquer sur le lien <i>Version imprimable</i> puis, après affichage de
				la nouvelle page, lancer une impression avec votre imprimante.
				<br />&nbsp;
			</td>
		</tr>
	</table>
</div>

<form method="post" action="?page=livrets&amp;mode=recap_period_all" name="formulaire_list" id="formulaire_list">
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
				<td>Liste des classes de l'année courante</td>
				<td>
					<select name="classe_id">
						<option value="-1">-- Sélectionner une classe --</option>
						<?php foreach($aClasses as $aClasse): ?>
							<option value="<?php echo($aClasse['CLASSE_ID']); ?>"<?php echo($aClasse['CLASSE_ID'] == $nClasseId ? ' selected="selected"' :''); ?>><?php echo($aClasse['CLASSE_NOM']); ?></option>
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

<?php if($nClasseId != -1 && $nPeriodeId != -1): ?>
	<?php foreach($aEleves as $i => $aEleve): ?>
	<?php if($aEvalInds[$i] != false): ?>
		<table class="formulaire">
			<caption>Compétences - Cycle <?php echo $aEleveInfo[$i]['CYCLE_NOM']; ?> - Période <?php echo $aPeriodesInfo[$i]['PERIODE_NOM']; ?> - Classe <?php echo $aClassesNiveaux[$i]['CLASSE_NOM']; ?> - Elève <?php echo $aEleveInfo[$i]['ELEVE_NOM']; ?></caption>
			<tbody>
				<tr>
					<td>
						<!-- Les pages d'évaluations individuelles -->
						<table class="entete_n">
							<thead></thead>
							<tfoot></tfoot>
							<thead>
								<tr>
									<?php foreach($aNotes[$i] as $aNote): ?>
									<td class="<?php echo $aNote['NOTE_LABEL']; ?>"><?php echo $aNote['NOTE_LABEL']; ?> = <?php echo $aNote['NOTE_NOM']; ?></td>
									<?php endforeach; ?>
								</tr>
							</thead>
						</table>

						<table class="display">
							<tr><!-- 1ère ligne de titre -->
								<td></td>
								<td class="colonne1" colspan="1">classe <?php echo $aClassesNiveaux[$i]['CLASSE_NOM']; ?> (<?php echo $aClassesNiveaux[$i]['NIVEAU_NOM']; ?>)</td>
							</tr>
							<tr><!-- 2ème ligne de titre -->
								<td>Livret n°</td>
								<td class="colonne1" style="width: 25px;"><?php echo $aPeriodesInfo[$i]['PERIODE_NOM']; ?></td>
							</tr>
							<?php foreach($aDomainesMatieresCompetences[$i] as $sDomaine => $aMatieres): ?>
							<tr class="domaine"><!-- Ligne du domaine -->
								<td colspan="2" style="text-align: center;"><?php echo $sDomaine; ?></td>
							</tr>
								<?php foreach($aMatieres as $sMatiere => $aCompetences): ?>
								<tr class="matiere"><!-- Ligne de la matière -->
									<td colspan="2"><?php echo $sMatiere; ?></td>
								</tr>
									<?php $nRow = 0; ?>
									<?php foreach($aCompetences as $sCompetence => $aCompetence): /* Pour chaque compétence */ ?>
									<tr class="row<?php echo (($nRow++)%2); ?>"><!-- Ligne de la competence -->
										<td><?php echo $sCompetence; ?></td>
											<?php $sNiveauNom = $aClassesNiveaux[$i]['NIVEAU_NOM']; ?>
											<?php $sClasseNom = $aClassesNiveaux[$i]['CLASSE_NOM']; ?>
											<?php $sPeriodeNom = $aPeriodesInfo[$i]['PERIODE_NOM']; ?>
											<?php if(isset($aEvalInds[$i][$sDomaine]) &&
												   isset($aEvalInds[$i][$sDomaine][$sMatiere]) &&
												   isset($aEvalInds[$i][$sDomaine][$sMatiere][$sCompetence]) &&
												   isset($aEvalInds[$i][$sDomaine][$sMatiere][$sCompetence][$sClasseNom]) &&
												   isset($aEvalInds[$i][$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom]) &&
												   isset($aEvalInds[$i][$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]))
												{
													$aToDisplay = $aEvalInds[$i][$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]; ?>
													<td class="<?php echo $aToDisplay['NOTE_LABEL']; ?>"><?php echo $aToDisplay['NOTE_LABEL']; ?></td>
												<?php } else { ?>
													<td class="colonne<?php echo ($i+1)%2; ?>">&nbsp;</td>
												<?php } ?>
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
				Aucune compétence n'a été évaluée pour l'élève '<?php echo $aEleve['ELEVE_NOM']; ?>' sur cette période.
			</td>
		</tr>
	</table>
	<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>