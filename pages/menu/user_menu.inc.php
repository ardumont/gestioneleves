<?php
//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Vérification des valeurs =====

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des données
//==============================================================================

//==============================================================================
// Préparation de l'affichage
//==============================================================================

$bAfficheEleveList   = ProfilManager::hasRight('eleve_list');
$bAfficheEvalColList = ProfilManager::hasRight('eval_col_list');
$bAfficheEvalIndList = ProfilManager::hasRight('eval_ind_list');
$bAfficheLivretList  = ProfilManager::hasRight('livret_list');
$bAfficheConsultList = ProfilManager::hasRight('consultation_list');
$bAdminRights        = ProfilManager::hasAdminRight();

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><a href="javascript:void(0);" style="color:white;" onclick="$('#menu_identite').toggle('slow');">Menu</a></h1>
<div id="menu_identite">
<?php if(isset($_SESSION['PROFESSEUR_ID'])): /* utilisateur non connecté */?>
	<h4>
		<a href="?page=home">
			<img src="<?php echo(URL_ICONS_16X16); ?>/home.png" />Accueil
		</a>
	</h4>
	<h5>&nbsp;</h5>
	<?php if($bAfficheEleveList): ?>
	<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#menu_eleves').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Elèves</a></h3>
	<div id="menu_eleves">
		<h4>
			<a href="?page=eleves">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Lister
			</a>
		</h4>
		<h5>&nbsp;</h5>
	</div>
	<?php endif; ?>
	<?php if($bAfficheEvalColList): ?>
	<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#menu_evaluations_collectives').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Evaluations collectives</a></h3>
	<div id="menu_evaluations_collectives">
		<h4>
			<a href="?page=evaluations_collectives">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Lister
			</a>
		</h4>
		<h5>&nbsp;</h5>
	</div>
	<?php endif; ?>
	<?php if($bAfficheEvalIndList): ?>
	<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#menu_evaluations_individuelles').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Evaluations individuelles</a></h3>
	<div id="menu_evaluations_individuelles">
		<h4>
			<a href="?page=evaluations_individuelles">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Lister
			</a>
		</h4>
		<h5>&nbsp;</h5>
	</div>
	<?php endif; ?>
	<?php if($bAfficheLivretList): ?>
	<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#menu_livrets_par_eleves').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Livrets par élève</a></h3>
	<div id="menu_livrets_par_eleves">
		<h4>
			<a href="?page=livrets&amp;mode=recap_period">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Récapitulatif périodique
			</a>
		</h4>
		<h4>
			<a href="?page=livrets&amp;mode=recap_annuel">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Récapitulatif annuel
			</a>
		</h4>
		<h4>
			<a href="?page=livrets&amp;mode=recap_cycle">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Récapitulatif cycle
			</a>
		</h4>
		<h5>&nbsp;</h5>
	</div>
	<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#menu_livrets_par_classes').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Livrets par classe</a></h3>
	<div id="menu_livrets_par_classes">
		<h4>
			<a href="?page=livrets&amp;mode=recap_period_all">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Récapitulatif périodique
			</a>
		</h4>
		<h4>
			<a href="?page=livrets&amp;mode=recap_annuel_all">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Récapitulatif annuel
			</a>
		</h4>
		<h5>&nbsp;</h5>
	</div>
	<?php endif; ?>
	<?php if($bAfficheConsultList): ?>
	<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#menu_consultations').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Consultations</a></h3>
	<div id="menu_consultations">
		<h4>
			<a href="?page=consultations&amp;mode=competences_period">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Compétences par période
			</a>
		</h4>
		<h4>
			<a href="?page=consultations&amp;mode=competences_annuel">
				<img src="<?php echo(URL_ICONS_16X16); ?>/blank.png" />Compétences par année
			</a>
		</h4>
		<h5>&nbsp;</h5>
	</div>
	<?php endif; ?>
	<h3><a href="javascript:void(0);" style="color:white;" onclick="$('#menu_aide').toggle('slow');"><img src="<?php echo(URL_ICONS_16X16); ?>/hhelp.png" />Aide/Info</a></h3>
	<div id="menu_aide">
		<h4>
			<a href="?page=contributeurs">
				<img src="<?php echo(URL_ICONS_16X16); ?>/contributeur.png" />Contributeurs
			</a>
		</h4>
		<?php if($bAdminRights): ?>
		<h4>
			<a href="admin.php">
				<img src="<?php echo(URL_ICONS_16X16); ?>/admin.png" />Page d'administration
			</a>
		</h4>
		<?php endif; ?>
	</div>
<?php else: ?>
	<br />Identification requise
<?php endif; ?>
</div>
