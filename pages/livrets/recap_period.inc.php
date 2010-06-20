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
	AND ELEVE_ACTIF=1
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
	$aPeriodesInfo = $aRes['PERIODES'];
	$aClassesNiveaux = $aRes['CLASSES_NIVEAUX'];
	$aDomainesMatieresCompetences = $aRes['DOMAINES_MATIERES_COMPETENCES'];
	$aEvalInds = $aRes['EVAL_INDS'];
	$aNomPrenom = $aRes['NOM_PRENOM'];

	// Récupération du commentaire sur la période de l'élève
	$sQuery = <<< ____EOQ
		SELECT COMMENTAIRE_VALEUR
		FROM COMMENTAIRES
		WHERE ID_ELEVE = {$nEleveId}
		AND ID_PERIODE = {$nPeriodeId}
		AND ID_CLASSE =  {$aClassesNiveaux['CLASSE_ID']}
____EOQ;
	$sCommentaire = Database::fetchOneValue($sQuery);
}

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Récapitulatif périodique d'un élève</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<?php if($nEleveId != -1 && $nPeriodeId != -1): ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="special.php?page=export_livret_eleve_period&amp;eleve_id=<?php echo $nEleveId; ?>&amp;periode_id=<?php echo $nPeriodeId; ?>">Version imprimable</a>
<?php endif ?>
<div id="help" class="messagebox_info" style="display: none;">
	Par défaut, cette page permet d'afficher un récapitulatif de l'activité périodique d'un élève de votre classe.<br />
	Vous sélectionnez l'élève de votre classe et la période désirée puis vous lancez l'affichage en cliquant sur
	le bouton <i>Afficher</i>.
	<br />
	Pour générer une version imprimable, cliquer sur le lien <i>Version imprimable</i> puis, après affichage de
	la nouvelle page, lancer une impression avec votre imprimante.
</div>

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
			<caption>Compétences - Cycle <?php echo $aEleveInfo['CYCLE_NOM']; ?> - Période <?php echo $aPeriodesInfo['PERIODE_NOM']; ?> - Classe <?php echo $aClassesNiveaux['CLASSE_NOM']; ?> - Elève <?php echo $aEleveInfo['ELEVE_NOM']; ?></caption>
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
								<td class="colonne1" colspan="1">classe <?php echo $aClassesNiveaux['CLASSE_NOM']; ?> (<?php echo $aClassesNiveaux['NIVEAU_NOM']; ?>)</td>
							</tr>
							<tr><!-- 2ème ligne de titre -->
								<td>Livret n°</td>
								<td class="colonne1" style="width: 25px;"><?php echo $aPeriodesInfo['PERIODE_NOM']; ?></td>
							</tr>
							<?php foreach($aDomainesMatieresCompetences as $sDomaine => $aMatieres): ?>
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
													$aToDisplay = $aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]; ?>
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
				<tr>
					<td>
						<br />
						Saisir un commentaire pour l'élève '<?php echo $aEleveInfo['ELEVE_NOM']; ?>' :<br />
						<form id="form_insert">
							<input type="hidden" name="eleve_id" value="<?php echo $nEleveId; ?>" />
							<input type="hidden" name="periode_id" value="<?php echo $nPeriodeId; ?>" />
							<input type="hidden" name="classe_id" value="<?php echo $aClassesNiveaux['CLASSE_ID']; ?>" />
							<input type="hidden" name="commentaire_hidden" value="<?php echo $sCommentaire; ?>" />
							<textarea name="commentaire_saisie" rows="5" cols="50" onblur="submitAjaxUpdateCommentaire('form_insert');"><?php echo $sCommentaire; ?></textarea>
						</form>
					</td>
				</tr>
			</tbody>
		</table>
	<?php else: ?>
		<div class="messagebox_info">
			Aucune compétence n'a été évaluée pour cet élève sur cette période.
		</div>
	<?php endif; ?>
<?php endif; ?>