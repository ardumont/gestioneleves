<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== Recupere le nom de l'utilisateur en cours =====
$sUserName = "";

if(isset($_SESSION['PROFESSEUR_ID']))
{
	$sQuery =
		"SELECT PROFESSEUR_NOM " .
		"FROM PROFESSEURS " .
		"WHERE PROFESSEUR_ID = ".$_SESSION['PROFESSEUR_ID'];
	// recupere le nom du professeur directement dans la variable
	$sUserName = Database::fetchOneValue($sQuery);
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

$sGuiTitle = "Bienvenue" . ( isset($sUserName) ? " $sUserName," : "," );

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<?php if(Message::hasError() == true): ?>
	<h1>Erreur lors de l'authentification</h1>
	<ul class="form_error">
		<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
		<li><?php echo($sErrorMessage); ?></li>
		<?php endforeach; ?>
	</ul>
	<br />
<?php endif; ?>

<h1><?php echo($sGuiTitle); ?></h1>
<p>Voil&agrave; le gestionnaire d'&eacute;valuations de vos joyeux diablotins.</p>

<h2>TODO</h2>
<dl>
	<dd>
		<dt><h3><a href="javascript:showOrHide('_legende');">LEGENDE</a></h3></dt>
		<div id="_legende" style="display:none;">
			<table style="border:0;">
				<tr>
					<td>
						<ul>
							<li class="bug_majeur" title="Bug majeur">Bug majeur</li>
							<li class="bug_mineur" title="Bug mineur">Bug mineur</li>
							<li class="bug_faible" title="Bug faible">Bug faible</li>
						</ul>
					</td>
					<td>
						<ul>
							<li class="evolution_majeure" title="Evolution majeure">Evolution majeure</li>
							<li class="evolution_mineure" title="Evolution mineure">Evolution mineure</li>
							<li class="evolution_faible" title="Evolution faible">Evolution faible</li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<dt><h3><a href="javascript:showOrHide('_documentations');">DOCUMENTATIONS</a></h3></dt>
		<div id="_documentations" style="display:block;">
			<ul>
				<li class="evolution_majeure" title="Evolution majeure">Ecriture des "Sp&eacute;cifications fonctionnelles".</li>
				<li class="evolution_majeure" title="Evolution majeure">Ecriture du "Manuel d'installation".</li>
				<li class="evolution_majeure" title="Evolution majeure">Ecriture du "Manuel Utilisateur".</li>
			</ul>
		</div>
		<dt><h3><a href="javascript:showOrHide('_projets');">PROJETS</a></h3></dt>
		<div id="_projets" style="display:block;">
			<ul>
				<li class="evolution_majeure" title="Evolution majeure">Ajout de tests de v&eacute;rifications plus approfondies lors de la saisie de donn&eacute;es pour les divers ajouts.</li>
				<li class="evolution_mineure" title="Evolution mineure">Retravailler la charge graphique de l'ihm.</li>
				<li class="evolution_mineure" title="Evolution mineure">Charte graphique des tableaux (alternance de couleurs par ligne).</li>
			</ul>
		</div>
	</dd>
</dl>

<h2>Versions et modifications</h2>
<dl>
	<dt><h3><a href="javascript:showOrHide('V1');">V1</a></h3></dt>
	<div id="V1">
		<h4><a href="javascript:showIds(new Array('v100','v110','v120','v130'));">Tout montrer</a>
		<a href="javascript:hideIds(new Array('v100','v110','v120','v130'));">Tout cacher</a></h4>
		<dt><a href="javascript:showOrHide('v130');">v1.3.0</a></dt>
		<dd id="v130">
			<ul>
				<li class="evolution_majeure" title="Evolution majeure">Migration vers utf-8 (sources + bdd).</li>
				<li class="evolution_mineure" title="Evolution mineure">Module de connexion du professeur.</li>
			</ul>
		</dt>
		<dt><a href="javascript:showOrHide('v120');">v1.2.0</a></dt>
		<dd id="v120">
			<ul>
				<li class="evolution_mineure" title="Evolution mineure">Ihm de listing des p&eacute;riodes.</li>
				<li class="evolution_mineure" title="Evolution mineure">Ihm de listing des domaines.</li>
				<li class="evolution_mineure" title="Evolution mineure">Ihm de listing des mati&eacute;res.</li>
				<li class="evolution_mineure" title="Evolution mineure">Ihm de listing des comp&eacute;tences.</li>
				<li class="evolution_mineure" title="Evolution mineure">D&eacute;placement des modules d'administration dans le r&eacute;pertoire /admin.</li>
			</ul>
		</dd>
		<dt><a href="javascript:showOrHide('v110');">v1.1.0</a></dt>
		<dd id="v110">
			<ul>
				<li class="evolution_majeure" title="Evolution majeure">Ihm de listing et ajout d'&eacute;l&egrave;ves.</li>
				<li class="evolution_majeure" title="Evolution majeure">Ihm de listing et ajout d'&eacute;coles.</li>
				<li class="evolution_majeure" title="Evolution majeure">Ihm de listing et ajout de classes.</li>
				<li class="evolution_majeure" title="Evolution majeure">Ihm de listing des cycles et niveaux.</li>
			</ul>
		</dd>
		<dt><a href="javascript:showOrHide('v100');">v1.0.0</a></dt>
		<dd id="v100">
			<ul>
				<li class="evolution_majeure" title="Evolution majeure">Mise en place des plateformes (apache, php, mysql, phpmyadmin, subversion, trac, testlink).</li>
				<li class="evolution_majeure" title="Evolution majeure">Cr&eacute;ation du MCD - MLD de la base de donn&eacute;es.</li>
				<li class="evolution_majeure" title="Evolution majeure">Cr&eacute;ation d'un script sql d'installation de la base de donn&eacute;es.</li>
				<li class="evolution_majeure" title="Evolution majeure">Cr&eacute;ation d'un script sql de donn&eacute;es invariables.</li>
				<li class="evolution_majeure" title="Evolution majeure">Mise en place de la base de donn&eacute;es.</li>
			</ul>
		</dd>
	</div>
</dl>

