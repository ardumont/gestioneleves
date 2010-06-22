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

//if($oForm->hasError() == true)
//{
//	// rechargement de la liste des eleves
//	header("Location: ?page=livrets&mode=recap_cycle");
//	return;
//}

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

//==============================================================================
// Preparation de l'affichage
//==============================================================================

if($nEleveId != -1)
{
	$aRes = Livret::recap_cycle($nEleveId);

	$aEleveInfo = $aRes['ELEVE'];
	$aClassesEleve = $aRes['CLASSES_ELEVES'];
	$aNotes = $aRes['NOTES'];
	$aNotesValues = $aRes['NOTES_VALUES'];
	$aPeriodesInfo = $aRes['PERIODES'];
	$aClassesNiveaux = $aRes['CLASSES_NIVEAUX'];
	$aDomainesMatieresCompetences = $aRes['DOMAINES_MATIERES_COMPETENCES'];
	$aEvalInds = $aRes['EVAL_INDS'];
	$aNomPrenom = $aRes['NOM_PRENOM'];
}

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Récapitulatif du cycle de l'élève", $aObjectsToHide);
?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Récapitulatif du cycle de l'élève</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<?php if($nEleveId != -1): ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="special.php?page=export_livret_eleve_cycle&amp;eleve_id=<?php echo $nEleveId; ?>">Version imprimable</a>
<?php endif ?>
<div id="help" class="messagebox_info" style="display: none;">
	Par défaut, cette page permet d'afficher un récapitulatif de l'activité annuelle d'un élève de votre classe.<br />
	Vous sélectionnez l'élève de votre classe puis vous lancez l'affichage en cliquant sur le bouton <i>Afficher</i>.
	<br />
	Pour générer une version imprimable, cliquer sur le lien <i>Version imprimable</i> puis, après affichage de
	la nouvelle page, lancer une impression avec votre imprimante.
</div>

<form method="post" action="?page=livrets&amp;mode=recap_cycle" name="formulaire_list" id="formulaire_list">
	<table class="formulaire">
		<caption>Critères de sélection</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Liste des élèves de l'année courante</td>
				<td>
					<select name="eleve_id" onchange="document.getElementById('formulaire_list').submit();">
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

<?php if($nEleveId != -1): ?>
	<?php if($aEvalInds != false): ?>
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
		</tbody>
	</table>
	<?php else: ?>
		<div class="messagebox_info">
			Aucune compétence n'a été évaluée pour cet élève sur l'année scolaire.
		</div>
	<?php endif; ?>
<?php endif; ?>