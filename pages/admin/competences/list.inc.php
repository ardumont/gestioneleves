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

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

$sQueryCycleId = "";
if($nCycleId != -1)
{
	$sQueryCycleId = " AND ID_CYCLE = {$nCycleId}";
}

$sQueryDomaineId = "";
if($nDomaineId != -1)
{
	$sQueryDomaineId = " AND ID_DOMAINE = {$nDomaineId}";
}

$sQueryMatiereId = "";
if($nMatiereId != -1)
{
	$sQueryMatiereId = " AND ID_MATIERE = {$nMatiereId}";
}

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
		DOMAINE_NOM
	FROM DOMAINES
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
		MATIERE_NOM
	FROM MATIERES
	WHERE 1=1
	{$sQueryDomaineId}
	ORDER BY MATIERE_NOM ASC
EOQ;
$aMatieres = Database::fetchArray($sQuery);
// $aMatieres[][COLONNE] = VALEUR

// ===== La liste des competences =====
$sQuery = <<< EOQ
	SELECT
		COMPETENCE_ID,
		COMPETENCE_NOM,
		MATIERE_ID,
		MATIERE_NOM,
		DOMAINE_NOM,
		CYCLE_NOM
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

$aCompetences = Database::fetchArrayWithMultiKey($sQuery, array('CYCLE_NOM', 'DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM'));
// $aCompetences[][COLONNE] = VALEUR

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des comp&eacute;tences</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<table class="formulaire">
	<caption>Fonctionnement</caption>
	<tr>
		<td>
			Par défaut, cette page affiche l'ensemble des compétences existantes dans l'application.<br />
			Cette page permet de filtrer sur un cycle, un domaine ou une matière pour faciliter la lecture.<br />
			Pour cela, sélectionner un cycle ou un domaine ou une matière ou bien encore une combinaison de ces filtres
			 puis cliquer sur le bouton <i>Rechercher</i> pour que la page se rafraîchisse.<br />
			<br />
			Vous pouvez modifier une compétence en cliquant sur le nom de la compétence.<br />
			Vous pouvez également ajouter une compétence en cliquant sur le + en haut à gauche du tableau.
			<br />&nbsp;
		</td>
	</tr>
</table>

<form method="post" action="?page=competences" name="formulaire_competence" id="formulaire_competence">
	<table class="formulaire">
		<caption>Crit&eacute;res de recherche</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Cycle</td>
				<td>
					<select name="cycle_id" onchange="document.getElementById('formulaire_competence').submit();">
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
					<select name="domaine_id" onchange="document.getElementById('formulaire_competence').submit();">
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
					<select name="matiere_id" onchange="document.getElementById('formulaire_competence').submit();">
						<option value="-1">-- Sélectionnez une matière --</option>
						<?php foreach($aMatieres as $aMatiere): ?>
							<option value="<?php echo($aMatiere['MATIERE_ID']); ?>"<?php echo ($nMatiereId == $aMatiere['MATIERE_ID']) ? ' selected="selected"' : ''; ?>><?php echo($aMatiere['MATIERE_NOM']); ?></option>
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
<?php if($aCompetences != false): ?>
	<table class="list_tree">
		<thead>
			<tr>
				<th><a href="?page=competences&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
				<th>Cycles</th>
				<th>Domaines</th>
				<th>Mati&egrave;res</th>
				<th>Comp&eacute;tences</th>
				<th colspan="2">Actions</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><a href="?page=competences&amp;mode=add"><img src="<?php echo(URL_ICONS_16X16); ?>/add.png" alt="Ajouter" title="Ajouter"/></a></th>
				<td colspan="6"></td>
			</tr>
		</tfoot>
		<tbody>
			<?php $i = 0; ?>
			<?php foreach($aCompetences as $sCycle => $aCycle): ?>
			<!-- Ligne du cycle -->
			<tr class="level0_row<?php echo ($i++)%2; ?>">
				<td></td>
				<!-- Nom du cycle -->
				<th><?php echo($sCycle); ?></th>
				<!-- Le reste -->
				<td colspan="5"></td>
			</tr>
				<?php foreach($aCycle as $sDomaineNom => $aDomaineNom): ?>
				<!-- Ligne du nom de domaine -->
				<tr class="level0_row<?php echo ($i++)%2; ?>">
					<td></td>
					<!-- Nom du cycle -->
					<th></th>
					<!-- Nom du domaine -->
					<th><?php echo($sDomaineNom); ?></th>
					<!-- Le reste -->
					<td colspan="4"></td>
				</tr>
					<?php foreach($aDomaineNom as $sMatiereNom => $aMatiereNom): ?>
					<!-- Ligne de la matiere -->
					<tr class="level0_row<?php echo ($i++)%2; ?>">
						<td></td>
						<!-- Nom du cycle -->
						<th></th>
						<!-- Nom du domaine -->
						<th></th>
						<!-- Nom de la matiere -->
						<th><?php echo($sMatiereNom); ?></th>
						<!-- Le reste -->
						<td colspan="3"></td>
					</tr>
						<?php foreach($aMatiereNom as $sCompetenceNom => $aCompetence): ?>
						<!-- Ligne de la competence -->
						<tr class="level0_row<?php echo ($i++)%2; ?>">
							<td></td>
							<!-- Nom du cycle -->
							<th></th>
							<!-- Nom du domaine -->
							<th></th>
							<!-- Nom de la matiere -->
							<th></th>
							<!-- Nom de la compétence -->
							<td>
								<a href="?page=competences&amp;mode=edit&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>"><?php echo($sCompetenceNom); ?></a>
							</td>
							<!-- Edition -->
							<td>
								<a href="?page=competences&amp;mode=edit&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/edit.png" alt="Editer" title="Editer" /></a>
							</td>
							<!-- Suppression -->
							<td>
								<a href="?page=competences&amp;mode=delete&amp;competence_id=<?php echo($aCompetence['COMPETENCE_ID']); ?>&amp;matiere_id=<?php echo($aCompetence['MATIERE_ID']); ?>"><img src="<?php echo(URL_ICONS_16X16); ?>/delete.png" alt="Supprimer" title="Supprimer" /></a>
							</td>
						</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			<?php if($nCycleId != -1 || $nMatiereId != -1 || $nDomaineId != -1): ?>
				Aucune compétence n'a été renseignée pour ces critères de recherche.<br />
			<?php else:?>
				Aucune compétence n'a été renseignée à ce jour.<br />
				<a href="?page=competences&amp;mode=add">Ajouter une compétence</a>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php endif; ?>