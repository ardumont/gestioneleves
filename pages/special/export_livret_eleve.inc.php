<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$oForm = new FormValidation();

// recuperation des ids de restrictions de recherche
$nEleveId = $oForm->getValue('eleve_id', $_POST, 'convert_int', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== Les informations sur l'élève =====
$sQuery = <<< EOQ
	SELECT DISTINCT
		ELEVE_NOM,
		CLASSE_ANNEE_SCOLAIRE,
		CLASSE_NOM,
		ECOLE_NOM,
		ECOLE_VILLE,
		NIVEAU_NOM,
		CYCLE_NOM
	FROM ELEVES
		INNER JOIN ELEVE_CLASSE
			ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
		INNER JOIN CLASSES
			ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN ECOLES
			ON CLASSES.ID_ECOLE = ECOLES.ECOLE_ID
		INNER JOIN NIVEAU_CLASSE
			ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
		INNER JOIN NIVEAUX
			ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
		INNER JOIN CYCLES
			ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
	WHERE ELEVE_ID = {$nEleveId}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
EOQ;
$aEleve = Database::fetchOneRow($sQuery);
// $aEleve[COLONNE] = VALEUR

// ===== Les informations sur l'élève =====
$sQuery = <<< EOQ
	SELECT DISTINCT
		PROFESSEUR_NOM,
		CLASSE_NOM,
		CLASSE_ANNEE_SCOLAIRE
	FROM ELEVES
		INNER JOIN ELEVE_CLASSE
			ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
		INNER JOIN CLASSES
			ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN PROFESSEURS
			ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
	WHERE ELEVE_ID = {$nEleveId}
	ORDER BY CLASSE_ANNEE_SCOLAIRE ASC
EOQ;
$aClassesEleve = Database::fetchArray($sQuery);
// $aClassesEleve[][COLONNE] = VALEUR

// ===== Les informations sur les notes =====
$sQuery = <<< EOQ
	SELECT
		NOTE_NOM,
		NOTE_LABEL
	FROM NOTES
	ORDER BY NOTE_NOTE DESC
EOQ;
$aNotes = Database::fetchArray($sQuery);

// ===== La liste des periodes =====
$sQuery = <<< EOQ
	SELECT
		PERIODE_NOM
	FROM PERIODES
	ORDER BY PERIODE_NOM ASC
EOQ;
$aPeriodes = Database::fetchArray($sQuery);
// $aPeriodes[][COLONNE] = VALEUR


// ===== La liste des classes =====
$sQuery = <<< EOQ
	SELECT
		CLASSE_NOM,
		NIVEAU_NOM
	FROM CLASSES
		INNER JOIN NIVEAU_CLASSE
			ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
		INNER JOIN NIVEAUX
			ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
		INNER JOIN CYCLES
			ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
	WHERE CYCLE_NOM = '{$aEleve['CYCLE_NOM']}'
	ORDER BY NIVEAU_ID ASC
EOQ;
$aClassesNiveaux = Database::fetchArray($sQuery);
// $aClassesNiveaux[][COLONNE] = VALEUR

// ===== La liste des compétences =====
$sQuery = <<< EOQ
	SELECT
		COMPETENCE_NOM,
		MATIERE_NOM,
		DOMAINE_NOM
	FROM COMPETENCES
		INNER JOIN MATIERES
			ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
		INNER JOIN DOMAINES
			ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
		INNER JOIN CYCLES
			ON DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
	WHERE CYCLE_NOM = '{$aEleve['CYCLE_NOM']}'
	ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
EOQ;
$aDomainesMatieresCompetences = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM'));
// $aDomainesMatieresCompetences[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][COLONNE] = VALEUR

// ===== La liste des evaluations individuelles a ce jour pour l'élève =====
$sQuery = <<< EOQ
	SELECT
		MATIERE_NOM,
		DOMAINE_NOM,
		COMPETENCE_NOM,
		NIVEAU_NOM,
		CLASSE_NOM,
		PERIODE_NOM,
		NOTE_LABEL
	FROM EVALUATIONS_INDIVIDUELLES
		INNER JOIN NOTES
			ON EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID
		INNER JOIN ELEVES
			ON EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID
		INNER JOIN EVALUATIONS_COLLECTIVES
			ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
		INNER JOIN CLASSES
			ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
		INNER JOIN NIVEAU_CLASSE
			ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
		INNER JOIN NIVEAUX
			ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
		INNER JOIN COMPETENCES
			ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
		INNER JOIN MATIERES
			ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
		INNER JOIN DOMAINES
			ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
		INNER JOIN PERIODES
			ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
	WHERE ELEVE_ID = {$nEleveId}
	ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
EOQ;
$aEvalInds= Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM', 'CLASSE_NOM', 'NIVEAU_NOM', 'PERIODE_NOM'));
// $aEvalInds[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][NOM DE LA CLASSE][NOM DU NIVEAU][NOM DE LA PERIODE][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

$aNomPrenom = explode(" ", $aEleve['ELEVE_NOM']);

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
			<td class="colonne<?php echo ($i+1)%2; ?>" colspan="<?php echo count($aPeriodes); ?>">classe <?php echo $aClasseNiveau['CLASSE_NOM']; ?> (<?php echo $aClasseNiveau['NIVEAU_NOM']; ?>)</td>
			<?php endforeach; ?>
		</tr>
		<tr><!-- 2ème ligne de titre -->
			<td>Livret n°</td>
			<?php foreach($aClassesNiveaux as $i => $aClasseNiveau): /* Pour chaque classe de l'élève */ ?>
				<?php foreach($aPeriodes as $aPeriode): ?>
				<td class="colonne<?php echo ($i+1)%2; ?>" style="width: 25px;"><?php echo $aPeriode['PERIODE_NOM']; ?></td>
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
						<td class="colonne<?php echo ($i+1)%2; ?>">
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
				<tr style="width: 500px; height: 150px;">
					<td style="width: 20%"><?php echo $aPeriode['PERIODE_NOM']; ?></td>
					<td style="width: 80%"></td>
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
					<td colspan="3"></td>
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
</body>
</html>