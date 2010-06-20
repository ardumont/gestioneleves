<?php
//==============================================================================
// Préparation des données
//==============================================================================

// Récupération d'objets cachés
$sQuery = <<< EOQ
	SELECT HO_LIBELLE, HO_LIBELLE
	FROM HIDDEN_OBJECTS
EOQ;
$aObjectsToHide = Database::fetchColumn($sQuery);

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

//==============================================================================
// Préparation de l'affichage
//==============================================================================

$bAdminProfList = ProfilManager::hasRight('professeur_list');
$bAdminEcoleList = ProfilManager::hasRight('ecole_list');
$bAdminClasseList = ProfilManager::hasRight('classe_list');
$bAdminEleveList = ProfilManager::hasRight('admin_eleve_list');
$bAdminCycleList = ProfilManager::hasRight('cycle_list');
$bAdminNiveauList = ProfilManager::hasRight('niveau_list');
$bAdminDomaineList = ProfilManager::hasRight('domaine_list');
$bAdminMatiereList = ProfilManager::hasRight('matiere_list');
$bAdminCompetenceList = ProfilManager::hasRight('competence_list');
$bAdminNoteList = ProfilManager::hasRight('note_list');
$bAdminPeriodeList = ProfilManager::hasRight('periode_list');

$bAdminImportCsvCycle = ProfilManager::hasRight('import_csv_cycle');
$bAdminImportXmlCycle = ProfilManager::hasRight('import_xml_cycle');
$bAdminImportXmlClasse = ProfilManager::hasRight('import_xml_classe');

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0);" style="color:white;" onclick="showOrHide('administration');">Administration</a></h1>
<div id="administration" <?php echo manage_display_hidden_objects($aObjectsToHide, 'administration'); ?>>
	<h4>
		<a href="?page=home">
			<img src="<?php echo(URL_ICONS_16X16); ?>/home.png" />La page d'accueil
		</a>
	</h4>
	<?php if(ProfilManager::hasRight('admin_profil_list')): ?>
	<h2><a href="javascript:void(0);" style="color:white;" onclick="showOrHide('administrer');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Administrer</a></h2>
	<div id="administrer" <?php echo manage_display_hidden_objects($aObjectsToHide, 'administrer'); ?>>
		<h4>
			<a href="?page=profils">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Profils
			</a>
		</h4>
	</div>
	<?php endif; ?>
	<?php if($bAdminProfList || $bAdminEcoleList || $bAdminClasseList || $bAdminEleveList ||
			 $bAdminCycleList || $bAdminNiveauList || $bAdminDomaineList || $bAdminMatiereList ||
			 $bAdminCompetenceList || $bAdminNoteList || $bAdminPeriodeList): ?>
	<h2><a href="javascript:void(0);" style="color:white;" onclick="showOrHide('gestion');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Gestion</a></h2>
	<div id="gestion" <?php echo manage_display_hidden_objects($aObjectsToHide, 'gestion'); ?>>
		<?php if($bAdminProfList): ?>
		<h4>
			<a href="?page=professeurs">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Professeurs
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminEcoleList): ?>
		<h4>
			<a href="?page=ecoles">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Ecoles
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminClasseList): ?>
		<h4>
			<a href="?page=classes">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Classes
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminEleveList): ?>
		<h4>
			<a href="?page=eleves">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Elèves
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminCycleList): ?>
		<h4>
			<a href="?page=cycles">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Cycles
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminNiveauList): ?>
		<h4>
			<a href="?page=niveaux">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Niveaux
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminDomaineList): ?>
		<h4>
			<a href="?page=domaines">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Domaines
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminMatiereList): ?>
		<h4>
			<a href="?page=matieres">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Mati&egrave;res
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminCompetenceList): ?>
		<h4>
			<a href="?page=competences">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Comp&eacute;tences
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminNoteList): ?>
		<h4>
			<a href="?page=notes">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Notes
			</a>
		</h4>
		<?php endif; ?>
		<?php if($bAdminPeriodeList): ?>
		<h4>
			<a href="?page=periodes">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />P&eacute;riodes
			</a>
		</h4>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if($bAdminImportCsvCycle || $bAdminImportXmlCycle || $bAdminImportXmlClasse): ?>
	<h2><a href="javascript:void(0);" style="color:white;" onclick="showOrHide('imports');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Import</a></h2>
	<div id="imports" <?php echo manage_display_hidden_objects($aObjectsToHide, 'imports'); ?>>
		<?php if($bAdminImportCsvCycle || $bAdminImportXmlCycle): ?>
		<h3><a href="javascript:void(0);" style="color:white;" onclick="showOrHide('imports_cycle');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Cycles</a></h3>
		<div id="imports_cycle" <?php echo manage_display_hidden_objects($aObjectsToHide, 'imports_cycle'); ?>>
			<?php if($bAdminImportCsvCycle): ?>
			<h4>
				<a href="?page=imports&amp;mode=imports_csv">
					<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Import CSV
				</a>
			</h4>
			<?php endif; ?>
			<?php if($bAdminImportXmlCycle): ?>
			<h4>
				<a href="?page=imports&amp;mode=imports_xml">
					<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Import XML
				</a>
			</h4>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php if($bAdminImportXmlClasse): ?>
		<h3><a href="javascript:void(0);" style="color:white;" onclick="showOrHide('imports_classe');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Classes</a></h3>
		<div id="imports_classe" <?php echo manage_display_hidden_objects($aObjectsToHide, 'imports_classe'); ?>>
			<h4>
				<a href="?page=imports&amp;mode=imports_xml_classe">
					<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Import XML
				</a>
			</h4>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>