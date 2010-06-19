<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('livret_list');
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

$oForm = new FormValidation();

// Recuperation des ids de restrictions de recherche
$oForm->read('classe_id', $_POST);
$oForm->testError0(null, 'exist',       "Il manque le champ classe_id !");
$oForm->testError0(null, 'blank',       "Il manque l'id de la classe !");
$oForm->testError0(null, 'convert_int', "L'identifiant de la classe doit être un entier !");
$nClasseId = $oForm->get(null, -1);

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
	AND ELEVE_ACTIF=1
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

if($nClasseId != -1)
{
	foreach($aEleves as $i => $oEleve)
	{
		$aRes = Livret::recap_annuel($oEleve['ELEVE_ID']);

		$aEleveInfoArr[$i] = $aRes['ELEVE'];
		$aClassesEleveArr[$i] = $aRes['CLASSES_ELEVES'];
		$aNotesArr[$i] = $aRes['NOTES'];
		$aNotesValuesArr[$i] = $aRes['NOTES_VALUES'];
		$aPeriodesInfoArr[$i] = $aRes['PERIODES'];
		$aClassesNiveauxArr[$i] = $aRes['CLASSES_NIVEAUX'];
		$aDomainesMatieresCompetencesArr[$i] = $aRes['DOMAINES_MATIERES_COMPETENCES'];
		$aEvalIndsArr[$i] = $aRes['EVAL_INDS'];
		$aNomPrenomArr[$i] = $aRes['NOM_PRENOM'];
		$aCommentairesArr[$i] = $aRes['COMMENTAIRES'];
	}
}

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Récapitulatif annuel d'un élève</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<?php if($nClasseId != -1): ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="special.php?page=export_livret_eleve_annuel_all&amp;classe_id=<?php echo $nClasseId; ?>">Version imprimable</a>
<?php endif ?>
<div id="help" class="messagebox_info" style="display: none;">
	Cette page permet d'afficher un récapitulatif de l'activité annuelle pour chaque élève.<br />
	Vous sélectionnez la classe puis vous lancez l'affichage en cliquant sur le bouton <i>Afficher</i>.
	<br />
	Pour générer une version imprimable, cliquer sur le lien <i>Version imprimable</i> puis, après affichage de
	la nouvelle page, lancer une impression avec votre imprimante.
</div>

<form method="post" action="?page=livrets&amp;mode=recap_annuel_all" name="formulaire_list" id="formulaire_list">
	<table class="formulaire">
		<caption>Critères de sélection</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
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

<?php if($nClasseId != -1): ?>
	<?php foreach(range(0, count($aEleves) - 1) as $i): ?>
		<?php
		$aEleveInfo = $aEleveInfoArr[$i];
		$aClassesEleve = $aClassesEleveArr[$i];
		$aNotes = $aNotesArr[$i];
		$aNotesValues = $aNotesValuesArr[$i];
		$aPeriodesInfo = $aPeriodesInfoArr[$i];
		$aClassesNiveaux = $aClassesNiveauxArr[$i];
		$aDomainesMatieresCompetences = $aDomainesMatieresCompetencesArr[$i];
		$aEvalInds = $aEvalIndsArr[$i];
		$aNomPrenom = $aNomPrenomArr[$i];
		$aCommentaires = $aCommentairesArr[$i];

		if($aEvalInds != false): ?>
		<!-- Affichage du récapitulatif -->
		<table class="formulaire">
			<caption>Compétences - Cycle <?php echo $aEleveInfo['CYCLE_NOM']; ?> - Elève <?php echo $aEleveInfo['ELEVE_NOM']; ?></caption>
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
								<?php foreach($aClassesNiveaux as $i => $aClasseNiveau): /* Pour chaque classe de l'élève */ ?>
								<td class="colonne<?php echo ($i+1)%count($aPeriodesInfo); ?>" colspan="<?php echo count($aPeriodesInfo); ?>">classe <?php echo $aClasseNiveau['CLASSE_NOM']; ?> (<?php echo $aClasseNiveau['NIVEAU_NOM']; ?>)</td>
								<?php endforeach; ?>
							</tr>
							<tr><!-- 2ème ligne de titre -->
								<td>Livret n°</td>
								<?php foreach($aClassesNiveaux as $i => $aClasseNiveau): /* Pour chaque classe de l'élève */ ?>
									<?php foreach($aPeriodesInfo as $aPeriode): ?>
									<td class="colonne<?php echo ($i+1)%count($aPeriodesInfo); ?>" style="width: 25px;"><?php echo $aPeriode['PERIODE_NOM']; ?></td>
									<?php endforeach; ?>
								<?php endforeach; ?>
							</tr>
							<?php foreach($aDomainesMatieresCompetences as $sDomaine => $aMatieres): ?>
							<tr class="domaine"><!-- Ligne du domaine -->
								<td colspan="<?php echo 1+count($aClassesNiveaux) * count($aPeriodesInfo); ?>" style="text-align: center;"><?php echo $sDomaine; ?></td>
							</tr>
								<?php foreach($aMatieres as $sMatiere => $aCompetences): ?>
								<tr class="matiere"><!-- Ligne de la matière -->
									<td colspan="<?php echo 1+count($aClassesNiveaux) * count($aPeriodesInfo); ?>"><?php echo $sMatiere; ?></td>
								</tr>
									<?php $nRow = 0; ?>
									<?php foreach($aCompetences as $sCompetence => $aCompetence): /* Pour chaque compétence */ ?>
									<tr class="row<?php echo (($nRow++)%2); ?>"><!-- Ligne de la competence -->
										<td><?php echo $sCompetence; ?></td>
										<?php foreach($aClassesNiveaux as $i => $aClasseNiveau): /* Pour chaque classe de l'élève */ ?>
											<?php foreach($aPeriodesInfo as $aPeriode): /* Pour chaque période */ ?>
												<?php $sNiveauNom = $aClasseNiveau['NIVEAU_NOM']; ?>
												<?php $sClasseNom = $aClasseNiveau['CLASSE_NOM']; ?>
												<?php $sPeriodeNom = $aPeriode['PERIODE_NOM']; ?>
												<?php
													if(isset($aEvalInds[$sDomaine]) &&
													   isset($aEvalInds[$sDomaine][$sMatiere]) &&
													   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence]) &&
													   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom]) &&
													   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom]) &&
													   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]))
													{
														$aToDisplay = $aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]; ?>
														<td class="<?php echo $aToDisplay['NOTE_LABEL']; ?>"><?php echo $aToDisplay['NOTE_LABEL']; ?></td>
													<?php } else { ?>
														<td class="colonne<?php echo ($i+1)%count($aPeriodesInfo); ?>">&nbsp;</td>
													<?php } ?>
											<?php endforeach; ?>
										<?php endforeach; ?>
									</tr>
									<?php endforeach; ?>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						Saisir un commentaire pour chaque période pour l'élève '<?php echo $aEleveInfo['ELEVE_NOM']; ?>' :
					</td>
				</tr>
				<tr><!-- Les appréciations -->
					<td>
						<table>
							<thead>
								<tr>
									<th>Période</th>
									<th>Appréciations</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($aPeriodesInfo as $nRow => $aRows): ?>
									<?php $nClasseId = $aEleveInfo['CLASSE_ID']; ?>
									<?php $nEleveId = $aEleveInfo['ELEVE_ID']; ?>
									<?php $sPeriodeNom = $aRows['PERIODE_NOM']; ?>
									<?php $nPeriodId = $aRows['PERIODE_ID']; ?>
									<?php $sCommentaire = $aCommentaires[$sPeriodeNom]['COMMENTAIRE_VALEUR']; ?>
								<tr>
									<td><?php echo $sPeriodeNom; ?></td>
									<td>
										<form id="form_insert_<?php echo $nEleveId; ?>_<?php echo $nPeriodId; ?>">
											<input type="hidden" name="eleve_id" value="<?php echo $nEleveId; ?>" />
											<input type="hidden" name="periode_id" value="<?php echo $nPeriodId; ?>" />
											<input type="hidden" name="classe_id" value="<?php echo $nClasseId; ?>" />
											<input type="hidden" name="commentaire_hidden" value="<?php echo $sCommentaire; ?>" />
											<textarea name="commentaire_saisie" rows="5" cols="50" onblur="submitAjaxUpdateCommentaire('form_insert_<?php echo $nEleveId; ?>_<?php echo $nPeriodId; ?>');"><?php echo $sCommentaire; ?></textarea>
										</form>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
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
						Aucune compétence n'a été évaluée pour cet élève sur l'année scolaire.
					</td>
				</tr>
			</table>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>