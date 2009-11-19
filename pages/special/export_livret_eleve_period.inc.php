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

if($oForm->hasError() == true)
{
	// rechargement de la liste des eleves
	header("Location: index.php?page=livrets&mode=livret_period");
	return;
}

//==============================================================================
// Traitement des donnees
//==============================================================================

$aRes = Livret::recap_period($nEleveId, $nPeriodeId);

$aEleve = $aRes['ELEVE'];
$aClassesEleve = $aRes['CLASSES_ELEVES'];
$aNotes = $aRes['NOTES'];
$aNotesValues = $aRes['NOTES_VALUES'];
$aPeriodes = $aRes['PERIODES'];
$aClassesNiveaux = $aRes['CLASSES_NIVEAUX'];
$aDomainesMatieresCompetences = $aRes['DOMAINES_MATIERES_COMPETENCES'];
$aEvalInds = $aRes['EVAL_INDS'];
$aNomPrenom = $aRes['NOM_PRENOM'];

//==============================================================================
// Preparation de l'affichage
//==============================================================================

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
		<div class="titre3">Cycle des<br />apprentissages<br />fondamentaux</div>
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
		<div class="titre5">Education Nationale - XXX circonscription</div>

		<!-- Présentation de l'élève -->
		<div class="titre6">
			<table class="classe_eleve">
				<caption>Années scolaire et enseignant(e)</caption>
				<thead></thead>
				<tfoot></tfoot>
				<tbody>
					<tr>
						<td><?php echo $aClassesEleve['CLASSE_NOM']; ?></td>
						<td><?php echo $aClassesEleve['CLASSE_ANNEE_SCOLAIRE']; ?></td>
						<td><?php echo $aClassesEleve['PROFESSEUR_NOM']; ?></td>
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
			<td class="colonne1" colspan="1">classe <?php echo $aClassesNiveaux['CLASSE_NOM']; ?> (<?php echo $aClassesNiveaux['NIVEAU_NOM']; ?>)</td>
		</tr>
		<tr><!-- 2ème ligne de titre -->
			<td>Livret n°</td>
			<td class="colonne1" style="width: 25px;"><?php echo $aPeriodes['PERIODE_NOM']; ?></td>
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
						<?php $sPeriodeNom = $aPeriodes['PERIODE_NOM']; ?>
						<?php $aToDisplay = $aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]; ?>
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
				<td colspan="2">classe <?php echo $aClassesNiveaux['CLASSE_NOM']; ?> (<?php echo $aClassesNiveaux['NIVEAU_NOM']; ?>)</td>
			</tr>
			<tr>
				<td>N° livret</td>
				<td>APPRECIATIONS DE L'ENSEIGNANT</td>
			</tr>
		</thead>
		<tfoot></tfoot>
		<tbody>
			<tr style="width: 500px; height: 150px;">
				<td style="width: 20%"><?php echo $aPeriodes['PERIODE_NOM']; ?></td>
				<td style="width: 80%">&nbsp;</td>
			</tr>
		</tbody>
	</table>

	<!-- Tableau des signatures -->
	<table class="display" style="width: 1000px; margin-top: 30px;">
		<thead>
			<tr>
				<td colspan="4">classe <?php echo $aClassesNiveaux['CLASSE_NOM']; ?></td>
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
				<td style="width: 10%"><?php echo $aPeriodes['PERIODE_NOM']; ?></td>
				<td style="width: 30%">&nbsp;</td>
				<td style="width: 30%">&nbsp;</td>
				<td style="width: 30%">&nbsp;</td>
			</tr>
		</tbody>
	</table>
</body>
</html>