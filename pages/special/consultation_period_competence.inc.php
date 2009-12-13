<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Modification de la date =====
$oForm = new FormValidation();

// Periode concernée par la synthése
$nPeriodeId = $oForm->getValue('periode_id', $_GET, 'convert_int', -1);
// La classe
$nClasseId = $oForm->getValue('classe_id', $_GET, 'convert_int', -1);
// La compétence sur laquelle porte la synthèse
$nCompetenceId = $oForm->getValue('competence_id', $_GET, 'convert_int', -1);

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

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
}

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
	<link rel="stylesheet" type="text/css" href="main.css" media="all" />
</head>
<body>
	<h1>Synthèse périodique de la compétence</h1>
	
	<?php if($aEvalInds != false): ?>
		<table class="formulaire">
			<caption style="text-align:left;">Moyenne par élève des évaluations individuelles sur la compétence<br />'<?php echo $sCompetenceNom; ?>'</caption>
			<tbody>
				<tr>
					<td>
						<!-- Les pages d'évaluations individuelles -->
						<table class="entete_n">
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
</body>
</html>