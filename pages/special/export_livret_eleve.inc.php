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
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
EOQ;
$aClassesEleve = Database::fetchArray($sQuery);
// $aClassesEleve[][COLONNE] = VALEUR

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
	<meta content="Lionel SAURON" name="author" />
	<meta content="Antoine Romain DUMONT" name="author" />

	<link rel="stylesheet" type="text/css" href="default.css" media="all" />
	<link rel="stylesheet" type="text/css" href="special.css" media="all" />
</head>
<body>
	<!-- Page de présentation -->
	<div id="page_1">
		<!-- Premier titre -->
		<div id="titre1">Livret d'évaluation</div>
		<br />
		<!-- Présentation de l'école -->
		<div id="titre2">
			Ecole élémentaire publique<br />
			<?php echo $aEleve['ECOLE_NOM']; ?><br />
			<?php echo $aEleve['ECOLE_VILLE']; ?><br />
		</div>
		<br />
		<!-- Présentation du cycle -->
		<div id="titre3">Cycle des<br />apprentissages<br />fondamentaux</div>
		<div id="titre4">Cycle <?php echo $aEleve['CYCLE_NOM']; ?></div>
		<br />
		<!--  Présentation de l'élève -->
		<div id="struct_identite_eleve">
			<table id="identite_eleve">
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
		<div id="titre5">Education Nationale - XXX circonscription</div>

		<!-- Présentation de l'élève -->
		<div id="titre6">
			<table id="classe_eleve">
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

	<!-- Les pages d'évaluations individuelles -->
	<div class="page_n">

	</div>
</body>
</html>