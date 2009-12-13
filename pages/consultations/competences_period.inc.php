<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Modification de la date =====
$oForm = new FormValidation();

// Récupère éventuellement le cycle à filtrer
$nCycleId = $oForm->getValue('cycle_id', $_POST, 'convert_int', -1);
// Récupère éventuellement le domaine à filtrer
$nDomaineId = $oForm->getValue('domaine_id', $_POST, 'convert_int', -1);
// Récupère éventuellement la matière à filtrer
$nMatiereId = $oForm->getValue('matiere_id', $_POST, 'convert_int', -1);

// Periode concernée par la synthése
$nPeriodeId = $oForm->getValue('periode_id', $_POST, 'convert_int', -1);
// La classe
$nClasseId = $oForm->getValue('classe_id', $_POST, 'convert_int', -1);
// La compétence sur laquelle porte la synthèse
$nCompetenceId = $oForm->getValue('competence_id', $_POST, 'convert_int', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

$sQueryCycleId = ($nCycleId != -1) ? " AND ID_CYCLE = {$nCycleId}" : "";
$sQueryDomaineId = ($nDomaineId != -1) ? " AND ID_DOMAINE = {$nDomaineId}" : "";
$sQueryMatiereId = ($nMatiereId != -1) ? " AND ID_MATIERE = {$nMatiereId}" : "";

// ===== La liste des cycles =====
$sQuery = <<< EOQ
	SELECT
		CYCLE_NOM,
		CYCLE_ID
	FROM CYCLES
	WHERE 1=1
	ORDER BY CYCLE_NOM ASC
EOQ;
$aCycles = Database::fetchArray($sQuery);
// $aCycles[][COLONNE] = VALEUR

// ===== La liste des domaines =====
$sQuery = <<< EOQ
	SELECT
		DOMAINE_ID,
		CONCAT(CYCLE_NOM, ' - ', DOMAINE_NOM) AS DOMAINE_NOM
	FROM DOMAINES
		INNER JOIN CYCLES
			ON DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
	WHERE 1=1
	{$sQueryCycleId}
	ORDER BY DOMAINE_NOM ASC
EOQ;
$aDomaines = Database::fetchArray($sQuery);
// $aDomaines[][COLONNE] = VALEUR

// ===== La liste des matieres =====
$sQuery = <<< EOQ
	SELECT
		MATIERE_ID,
		CONCAT(CYCLE_NOM, ' - ', DOMAINE_NOM, ' - ', MATIERE_NOM) AS MATIERE_NOM
	FROM MATIERES
		INNER JOIN DOMAINES
			ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
		INNER JOIN CYCLES
			ON DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
	WHERE 1=1
	{$sQueryDomaineId}
	{$sQueryCycleId}
	ORDER BY MATIERE_NOM ASC
EOQ;
$aMatieres = Database::fetchArray($sQuery);
// $aMatieres[][COLONNE] = VALEUR

// ===== La liste des competences =====
$sQuery = <<< EOQ
	SELECT
		COMPETENCE_ID,
		CONCAT(COMPETENCE_NOM) AS COMPETENCE_NOM
	FROM COMPETENCES
		INNER JOIN MATIERES
			ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
		INNER JOIN DOMAINES
			ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
		INNER JOIN CYCLES
			ON DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
	WHERE 1=1
	{$sQueryDomaineId}
	{$sQueryCycleId}
	{$sQueryMatiereId}
	ORDER BY CYCLE_NOM ASC, DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
EOQ;
$aCompetences = Database::fetchArray($sQuery);
// $aCompetences[][COLONNE] = VALEUR

// ===== La liste des classes =====
$sQuery = <<< EOQ
	SELECT
		CLASSE_ID,
		CLASSE_NOM,
		CLASSE_ANNEE_SCOLAIRE,
		PROFESSEUR_NOM
	FROM CLASSES
		INNER JOIN PROFESSEUR_CLASSE
			ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
		INNER JOIN PROFESSEURS
			ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
	WHERE PROFESSEURS.PROFESSEUR_ID = {$_SESSION['PROFESSEUR_ID']}
	ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC
EOQ;
$aClasses = Database::fetchArray($sQuery);
// $aClasses[][COLONNE] = VALEUR

// ===== La liste des periodes =====
$sQuery = <<< EOQ
	SELECT
		PERIODE_ID,
		PERIODE_NOM,
		PERIODE_DATE_DEBUT,
		PERIODE_DATE_FIN
	FROM PERIODES
	ORDER BY PERIODE_NOM ASC
EOQ;
$aPeriodes = Database::fetchArray($sQuery);
// $aPeriodes[][COLONNE] = VALEUR

// Si nous possédons la classe, la période et la compétence, on peut charger la synthèse
if($nClasseId != -1 && $nPeriodeId != -1 && $nCompetenceId != -1)
{
	// Calcule la moyenne pour un élève
	$aRes = Livret::recap_period_competence($nClasseId, $nPeriodeId, $nCompetenceId);

	// Détail des informations calculés
	$aNotes = $aRes['NOTES'];
	$sPeriodeNom = $aRes['PERIODE_NOM'];
	$sClasseNom = $aRes['CLASSE_NOM'];
	$sCompetenceNom = $aRes['COMPETENCE_NOM'];
	$aEvalInds = $aRes['EVAL_INDS'];
} else {
	$aEvalInds = false;
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Synthèse périodique de la compétence</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<?php if($aEvalInds != false): ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="special.php?page=consultation_period_competence&amp;classe_id=<?php echo $nClasseId; ?>&amp;periode_id=<?php echo $nPeriodeId; ?>&amp;competence_id=<?php echo $nCompetenceId; ?>">Version imprimable</a>
<?php endif ?>

<div id="help" style="display: none;">
	<table class="formulaire">
		<caption>Fonctionnement</caption>
		<tr>
			<td>
				Cette page permet d'afficher une vue qui synthétise, pour une compétence choisie, les moyennes des évaluations individuelles 
				pour chacun des élèves d'une classe du professeur connecté.<br />
				Pour cela, sélectionner un cycle ou un domaine ou une matière ou bien encore une combinaison de ces filtres
				puis cliquer sur le bouton <i>Rechercher</i> pour que la page se rafraîchisse.<br />
				<br />
				Une fois la compétence choisie, sélectionner la classe puis la page se rafraichit avec la moyenne pour chaque élève.
				<br />&nbsp;
			</td>
		</tr>
	</table>
</div>

<form method="post" action="?page=consultations&amp;mode=competences_period" name="formulaire" id="formulaire">
	<table class="formulaire">
		<caption>Crit&eacute;res de recherche</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td colspan="2" style="font-weight:bold; color:blue;">optionnels</td>
			</tr>
			<tr>
				<td>Cycle</td>
				<td>
					<select name="cycle_id" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionnez un cycle --</option>
						<?php foreach($aCycles as $aCycle): ?>
							<option value="<?php echo($aCycle['CYCLE_ID']); ?>"<?php echo ($nCycleId == $aCycle['CYCLE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aCycle['CYCLE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Domaines</td>
				<td>
					<select name="domaine_id" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionnez un domaine --</option>
						<?php foreach($aDomaines as $aDomaine): ?>
							<option value="<?php echo($aDomaine['DOMAINE_ID']); ?>"<?php echo ($nDomaineId == $aDomaine['DOMAINE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aDomaine['DOMAINE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Matières</td>
				<td>
					<select name="matiere_id" onchange="document.getElementById('formulaire').submit();">
						<option value="-1">-- Sélectionnez une matière --</option>
						<?php foreach($aMatieres as $aMatiere): ?>
							<option value="<?php echo($aMatiere['MATIERE_ID']); ?>"<?php echo ($nMatiereId == $aMatiere['MATIERE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aMatiere['MATIERE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-weight:bold; color:red;">obligatoires</td>
			</tr>
			<tr>
				<td>Compétences</td>
				<td>
					<select name="competence_id">
						<option value="-1">-- Sélectionnez une compétence --</option>
						<?php foreach($aCompetences as $aCompetence): ?>
							<option value="<?php echo($aCompetence['COMPETENCE_ID']); ?>"<?php echo ($nCompetenceId == $aCompetence['COMPETENCE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aCompetence['COMPETENCE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Classes</td>
				<td>
					<select name="classe_id">
						<?php foreach($aClasses as $aClasse): ?>
							<option value="-1">-- Sélectionnez une classe --</option>
							<option value="<?php echo($aClasse['CLASSE_ID']); ?>"<?php echo($aClasse['CLASSE_ID'] == $nClasseId ? ' selected="selected"' :''); ?>><?php echo($aClasse['PROFESSEUR_NOM'] . " - " .$aClasse['CLASSE_ANNEE_SCOLAIRE']. " - " . $aClasse['CLASSE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
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
				<td><input type="submit" name="action" value="Rechercher" /></td>
			</tr>
		</tbody>
	</table>
</form>
<?php if($aEvalInds != false): ?>
	<table class="formulaire">
		<caption>Moyenne par élève des évaluations individuelles sur la compétence<br />'<?php echo $sCompetenceNom; ?>'</caption>
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
							<td class="colonne1" colspan="1">classe <?php echo $sClasseNom; ?></td>
						</tr>
						<tr><!-- 2ème ligne de titre -->
							<td>Livret n°</td>
							<td class="colonne1" style="width: 25px;"><?php echo $sPeriodeNom; ?></td>
						</tr>
						<?php $i = 0; ?>
						<?php foreach($aEvalInds as $sNomEleve => $aNotes): ?>
						<tr class="row<?php echo $i%2; ?>">
							<td><?php echo $sNomEleve; ?></td>
							<?php if(isset($aNotes['NOTE_LABEL'])): ?>
							<td class="<?php echo $aNotes['NOTE_LABEL']; ?>" style="text-align: center;"><?php echo $aNotes['NOTE_LABEL']; ?></td>
							<?php else: ?>
								<td class="colonne<?php echo ($i+1)%2; ?>">&nbsp;</td>
							<?php endif; ?>
						</tr>
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
			Aucun critère de recherche n'a été renseigné ou aucune recherche ne correspond au(x) 
			critère(s) de recherche.<br />
		</td>
	</tr>
</table>
<?php endif; ?>