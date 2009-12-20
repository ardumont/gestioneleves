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

//restriction sur l'annee scolaire courante
$sRestrictionAnneeScolaire =
	" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

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

$oForm->read('periode_id', $_GET);
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

if($oForm->hasError() == true)
{
	// rechargement de la liste des eleves
	header("Location: index.php?page=livrets&mode=recap_period_all");
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
	{$sRestrictionAnneeScolaire}
	{$sQueryRestClasse}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
EOQ;
$aEleves = Database::fetchArray($sQuery);
// $aEleves[][COLONNE] = VALEUR

if($nClasseId != -1 && $nPeriodeId != -1)
{
	foreach($aEleves as $i => $oEleve)
	{
		$aRes = Livret::recap_period($oEleve['ELEVE_ID'], $nPeriodeId);

		$aElevesInfo[$i] = $aRes['ELEVE'];
		$aClassesEleve[$i] = $aRes['CLASSES_ELEVES'];
		$aNotes[$i] = $aRes['NOTES'];
		$aPeriodes[$i] = $aRes['PERIODES'];
		$aClassesNiveaux[$i] = $aRes['CLASSES_NIVEAUX'];
		$aDomainesMatieresCompetences[$i] = $aRes['DOMAINES_MATIERES_COMPETENCES'];
		$aEvalInds[$i] = $aRes['EVAL_INDS'];
		$aNomPrenom[$i] = $aRes['NOM_PRENOM'];
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
	<?php foreach($aEleves as $i => $oEleve): ?>
	<!-- Page de présentation -->
	<div class="page_1">
		<!-- Premier titre -->
		<div class="titre1">Livret d'évaluation</div>
		<!-- Présentation de l'école -->
		<div class="titre2">
			Ecole élémentaire publique<br />
			<?php echo $aElevesInfo[$i]['ECOLE_NOM']; ?><br />
			<?php echo $aElevesInfo[$i]['ECOLE_VILLE']; ?><br />
		</div>
		<!-- Présentation du cycle -->
		<div class="titre3"><?php echo Livret::display_libelle_cycle($aElevesInfo[$i]['CYCLE_NOM']); ?></div>
		<div class="titre4">Cycle <?php echo $aElevesInfo[$i]['CYCLE_NOM']; ?></div>
		<!--  Présentation de l'élève -->
		<div class="struct_identite_eleve">
			<table class="identite_eleve">
				<tr>
					<th>Nom :</th>
					<td><?php echo $aNomPrenom[$i][0]; ?></td>
				</tr>
				<tr>
					<th>Prénom :</th>
					<td><?php echo $aNomPrenom[$i][1]; ?></td>
				</tr>
			</table>
		</div>
		<!-- Présentation de l'éducation nationale -->
		<div class="titre5">Education Nationale</div>

		<!-- Présentation de l'élève -->
		<div class="titre6">
			<table class="classe_eleve">
				<caption>Années scolaire et enseignant(e)</caption>
				<thead></thead>
				<tfoot></tfoot>
				<tbody>
					<tr>
						<td><?php echo $aClassesEleve[$i]['CLASSE_NOM']; ?></td>
						<td><?php echo $aClassesEleve[$i]['CLASSE_ANNEE_SCOLAIRE']; ?></td>
						<td><?php echo $aClassesEleve[$i]['PROFESSEUR_NOM']; ?></td>
					</tr>
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
				<?php foreach($aNotes[$i] as $aNote): ?>
				<td><?php echo $aNote['NOTE_LABEL']; ?> = <?php echo $aNote['NOTE_NOM']; ?></td>
				<?php endforeach; ?>
			</tr>
		</thead>
	</table>
	<div class="titre4">Compétences de fin de cycle <?php echo $aElevesInfo[$i]['CYCLE_NOM']; ?></div>

	<table class="display">
		<tr><!-- 1ère ligne de titre -->
			<td></td>
			<td class="colonne1" colspan="1">classe <?php echo $aClassesNiveaux[$i]['CLASSE_NOM']; ?> (<?php echo $aClassesNiveaux[$i]['NIVEAU_NOM']; ?>)</td>
		</tr>
		<tr><!-- 2ème ligne de titre -->
			<td>Livret n°</td>
			<td class="colonne1" style="width: 25px;"><?php echo $aPeriodes[$i]['PERIODE_NOM']; ?></td>
		</tr>
		<?php foreach($aDomainesMatieresCompetences[$i] as $sDomaine => $aMatieres): ?>
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
						<?php $sNiveauNom = $aClassesNiveaux[$i]['NIVEAU_NOM']; ?>
						<?php $sClasseNom = $aClassesNiveaux[$i]['CLASSE_NOM']; ?>
						<?php $sPeriodeNom = $aPeriodes[$i]['PERIODE_NOM']; ?>
						<?php $aToDisplay = $aEvalInds[$i][$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]; ?>
						<?php echo $aToDisplay['NOTE_LABEL']; ?>&nbsp;
					</td>
				</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>

	<!-- Saut de ligne -->
	<div style="page-break-after:always;"></div>

	<!-- Tableau d'appréciations -->
	<table class="display" style="width: 1000px;">
		<thead>
			<tr>
				<td colspan="2">classe <?php echo $aClassesNiveaux[$i]['CLASSE_NOM']; ?> (<?php echo $aClassesNiveaux[$i]['NIVEAU_NOM']; ?>)</td>
			</tr>
			<tr>
				<td>N° livret</td>
				<td>APPRECIATIONS DE L'ENSEIGNANT</td>
			</tr>
		</thead>
		<tfoot></tfoot>
		<tbody>
			<tr style="width: 500px; height: 250px;">
				<td style="width: 20%"><?php echo $aPeriodes[$i]['PERIODE_NOM']; ?></td>
				<td style="width: 80%">&nbsp;</td>
			</tr>
		</tbody>
	</table>

	<!-- Tableau des signatures -->
	<table class="display" style="width: 1000px; margin-top: 30px;">
		<thead>
			<tr>
				<td colspan="4">classe <?php echo $aClassesNiveaux[$i]['CLASSE_NOM']; ?></td>
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
			<tr style="height: 150px;">
				<td style="width: 10%"><?php echo $aPeriodes[$i]['PERIODE_NOM']; ?></td>
				<td style="width: 30%">&nbsp;</td>
				<td style="width: 30%">&nbsp;</td>
				<td style="width: 30%">&nbsp;</td>
			</tr>
		</tbody>
	</table>
	<!-- Saut de page -->
	<div style="page-break-after:always;"></div>
	<?php endforeach; ?>
</body>
</html>