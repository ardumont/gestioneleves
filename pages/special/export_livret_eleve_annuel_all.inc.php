<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('livret_list');
if($bHasRight == false)
{
	// Redirection
	header("Location: index.php?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// Recuperation des ids de restrictions de recherche
$oForm->read('classe_id', $_GET);
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

if($oForm->hasError() == true)
{
	// rechargement de la liste des eleves
	header("Location: index.php?page=livrets&mode=recap_annuel");
	return;
}

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

if($nClasseId != -1)
{
	foreach($aEleves as $i => $oEleve)
	{
		$aRes = Livret::recap_annuel($oEleve['ELEVE_ID']);

		$aEleveArr[$i] = $aRes['ELEVE'];
		$aClassesEleveArr[$i] = $aRes['CLASSES_ELEVES'];
		$aNotesArr[$i] = $aRes['NOTES'];
		$aNotesValuesArr[$i] = $aRes['NOTES_VALUES'];
		$aPeriodesArr[$i] = $aRes['PERIODES'];
		$aClassesNiveauxArr[$i] = $aRes['CLASSES_NIVEAUX'];
		$aDomainesMatieresCompetencesArr[$i] = $aRes['DOMAINES_MATIERES_COMPETENCES'];
		$aEvalIndsArr[$i] = $aRes['EVAL_INDS'];
		$aNomPrenomArr[$i] = $aRes['NOM_PRENOM'];
		$aCommentairesArr[$i] = $aRes['COMMENTAIRES'];
		$aConseilMaitresArr[$i] = $aRes['COMM_CONSEIL_MAITRES'];
	}
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

$sGuiTitle = "Livret d'évaluation";

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title><?php echo $sGuiTitle; ?></title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta content="Antoine Romain DUMONT" name="author" />

	<link rel="stylesheet" type="text/css" href="default.css" media="all" />
	<link rel="stylesheet" type="text/css" href="special.css" media="all" />
	<link rel="stylesheet" type="text/css" href="special.css" media="print" />
</head>
<body>
<?php foreach(range(0, count($aEleves) - 1) as $i): ?>
	<?php
	$aEleve = $aEleveArr[$i];
	$aClassesEleve = $aClassesEleveArr[$i];
	$aNotes = $aNotesArr[$i];
	$aNotesValues = $aNotesValuesArr[$i];
	$aPeriodes = $aPeriodesArr[$i];
	$aClassesNiveaux = $aClassesNiveauxArr[$i];
	$aDomainesMatieresCompetences = $aDomainesMatieresCompetencesArr[$i];
	$aEvalInds = $aEvalIndsArr[$i];
	$aNomPrenom = $aNomPrenomArr[$i];
	$aCommentaires = $aCommentairesArr[$i];
	$sConseilMaitres = $aConseilMaitresArr[$i];
	?>
		<!-- Page de présentation -->
		<div class="page_1">
			<!-- Premier titre -->
			<div class="titre1">Livret d'évaluation</div>
			<br />
			<!-- Présentation de l'école -->
			<div class="titre2">
				Ecole élémentaire publique<br />
				<?php echo $aEleve['ECOLE_NOM']; ?><br />
				<?php echo $aEleve['ECOLE_VILLE']; ?><br />
			</div>
			<br />
			<!-- Présentation du cycle -->
			<div class="titre3"><?php echo Livret::display_libelle_cycle($aEleve['CYCLE_NOM']); ?></div>
			<div class="titre4">Cycle <?php echo $aEleve['CYCLE_NOM']; ?></div>
			<br />
			<!--  Présentation de l'élève -->
			<div class="struct_identite_eleve">
				<table class="identite_eleve">
					<tr>
						<th>Nom :</th>
						<td><?php echo $aNomPrenom[0]; ?></td>
					</tr>
					<tr>
						<th>Prénom :</th>
						<td><?php echo $aNomPrenom[1]; ?></td>
					</tr>
				</table>
			</div>
			<br />
			<!-- Présentation de l'éducation nationale -->
			<div class="titre5">Education Nationale</div>
	
			<!-- Présentation de l'élève -->
			<div class="titre6">
				<table class="classe_eleve">
					<caption>Années scolaires et enseignants</caption>
					<thead></thead>
					<tfoot></tfoot>
					<tbody>
						<?php foreach($aClassesEleve as $aClasse): ?>
						<tr>
							<td><?php echo $aClasse['CLASSE_NOM']; ?></td>
							<td><?php echo $aClasse['CLASSE_ANNEE_SCOLAIRE']; ?></td>
							<td><?php echo $aClasse['PROFESSEUR_NOM']; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	
		<div style="page-break-after:always;"></div>
	
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
		<div class="titre4">Compétences de fin de cycle <?php echo $aEleve['CYCLE_NOM']; ?></div>
	
		<table class="display">
			<tr><!-- 1ère ligne de titre -->
				<td></td>
				<?php foreach($aClassesNiveaux as $i => $aClasseNiveau): /* Pour chaque classe de l'élève */ ?>
				<td class="colonne<?php echo ($i+1)%count($aPeriodes); ?>" colspan="<?php echo count($aPeriodes); ?>">classe <?php echo $aClasseNiveau['CLASSE_NOM']; ?> (<?php echo $aClasseNiveau['NIVEAU_NOM']; ?>)</td>
				<?php endforeach; ?>
			</tr>
			<tr><!-- 2ème ligne de titre -->
				<td>Livret n°</td>
				<?php foreach($aClassesNiveaux as $i => $aClasseNiveau): /* Pour chaque classe de l'élève */ ?>
					<?php foreach($aPeriodes as $aPeriode): ?>
					<td class="colonne<?php echo ($i+1)%count($aPeriodes); ?>" style="width: 25px;"><?php echo $aPeriode['PERIODE_NOM']; ?></td>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tr>
			<?php foreach($aDomainesMatieresCompetences as $sDomaine => $aMatieres): ?>
			<tr style="font-size: 1.1em; background-color: #848484;"><!-- Ligne du domaine -->
				<td colspan="<?php echo 1+count($aClassesNiveaux) * count($aPeriodes); ?>" style="text-align: center;"><?php echo $sDomaine; ?></td>
			</tr>
				<?php foreach($aMatieres as $sMatiere => $aCompetences): ?>
				<tr style="font-size: 1em;"><!-- Ligne de la matière -->
					<td colspan="<?php echo 1+count($aClassesNiveaux) * count($aPeriodes); ?>" style="text-align: left; background-color: #848484;"><?php echo $sMatiere; ?></td>
				</tr>
					<?php foreach($aCompetences as $sCompetence => $aCompetence): /* Pour chaque compétence */ ?>
					<tr style="font-size: 0.9em;"><!-- Ligne de la competence -->
						<td><?php echo $sCompetence; ?></td>
						<?php foreach($aClassesNiveaux as $i => $aClasseNiveau): /* Pour chaque classe de l'élève */ ?>
							<?php foreach($aPeriodes as $aPeriode): /* Pour chaque période */ ?>
							<td class="colonne<?php echo ($i+1)%count($aPeriodes); ?>">
								<?php $sNiveauNom = $aClasseNiveau['NIVEAU_NOM']; ?>
								<?php $sClasseNom = $aClasseNiveau['CLASSE_NOM']; ?>
								<?php $sPeriodeNom = $aPeriode['PERIODE_NOM']; ?>
								<?php $aToDisplay = $aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]; ?>
								<?php echo $aToDisplay['NOTE_LABEL']; ?>
							</td>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</table>
	
		<?php foreach($aClassesNiveaux as $i => $aClasseNiveau): /* Pour chaque classe de l'élève */ ?>
			<!-- Saut de ligne -->
			<div style="page-break-after:always;"></div>
			<!-- Tableau d'appréciations -->
			<table class="display" style="width: 1000px;">
				<thead>
					<tr>
						<td colspan="2">classe <?php echo $aClasseNiveau['CLASSE_NOM']; ?> (<?php echo $aClasseNiveau['NIVEAU_NOM']; ?>)</td>
					</tr>
					<tr>
						<td>N° livret</td>
						<td>APPRECIATIONS DE L'ENSEIGNANT</td>
					</tr>
				</thead>
				<tfoot></tfoot>
				<tbody>
					<?php foreach($aPeriodes as $aPeriode): /* Pour chaque période */ ?>
						<?php $sPeriodeNom = $aPeriode['PERIODE_NOM']; ?>
						<?php $nPeriodId = $aPeriode['PERIODE_ID']; ?>
						<?php $sCommentaire = $aCommentaires[$sPeriodeNom]['COMMENTAIRE_VALEUR']; ?>
					<tr style="width: 500px; height: 200px;">
						<td style="width: 20%"><?php echo $aPeriode['PERIODE_NOM']; ?></td>
						<td style="width: 80%"><pre><?php echo $sCommentaire; ?>&nbsp;</pre></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
	
			<!-- Saut de ligne -->
			<div style="page-break-after:always;"></div>
			<!-- Tableau des signatures -->
			<table class="display" style="width: 1000px;">
				<thead>
					<tr>
						<td colspan="4">classe <?php echo $aClasseNiveau['CLASSE_NOM']; ?> ..................</td>
					</tr>
					<tr>
						<td>N° livret</td>
						<td>L'enseignant(e)</td>
						<td>La directrice<br />Le directeur</td>
						<td>Les parents</td>
					</tr>
				</thead>
				<tfoot></tfoot>
				<tbody>
					<?php foreach($aPeriodes as $aPeriode): /* Pour chaque période */ ?>
					<tr style="height: 150px;">
						<td style="width: 10%"><?php echo $aPeriode['PERIODE_NOM']; ?></td>
						<td style="width: 30%"></td>
						<td style="width: 30%"></td>
						<td style="width: 30%"></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
	
			<!-- Tableau de conseil -->
			<table class="display" style="margin-top: 30px; width: 1000px;">
				<tbody></tbody>
				<tfoot></tfoot>
				<tbody>
					<tr>
						<td colspan="3">Avis du conseil des maîtres de cycle <?php echo $aEleve['CYCLE_NOM']; ?></td>
					</tr>
					<tr style="height: 150px;">
						<td colspan="3"><pre><?php echo $sConseilMaitres; ?></pre></td>
					</tr>
					<tr>
						<td>L'enseignant(e)</td>
						<td>La directrice<br />Le directeur</td>
						<td>Les parents</td>
					</tr>
					<tr style="height: 150px;">
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		<?php endforeach; ?>
	<?php endforeach; ?>
</body>
</html>