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

<h2>Release notes</h2>
<dl>
	<dt>
		<img src="<?php echo(URL_ICONS_8X8); ?>/fold_off.gif" alt="[-]" title="Cacher" onclick="showOrHideVersion(this)" />&nbsp;
		Version v1
	</dt>
	<dd>
		<a href="javascript:" onclick="showOrHideAllSubVersion(this);">Tout cacher/montrer</a>
		<dl>
			<dt>
				<img src="<?php echo(URL_ICONS_8X8); ?>/fold_off.gif" alt="[-]" title="Cacher" onclick="showOrHideVersion(this)" />&nbsp;
				v1.4.0
			</dt>
			<dd>
				<ul>
					<li class="evolution_majeure" title="Evolution majeure">Mise en place de l'installeur graphique.</li>
					<li class="evolution_majeure" title="Evolution majeure">Refonte de la charte graphique.</li>
					<li class="evolution_majeure" title="Evolution majeure">Remise en place du projet sous subversion pour faciliter les développements.</li>
					<li class="evolution_mineure" title="Evolution mineure">Mise en place du module de déconnexion d'un professeur.</li>
					<li class="evolution_mineure" title="Evolution mineure">Mise en place d'une page des contributeurs.</li>
					<li class="evolution_mineure" title="Evolution mineure">Mise en place d'un bloc de référencement des outils utilisés.</li>
					<li class="evolution_mineure" title="Evolution mineure">Refonte de la page d'accueil.</li>
				</ul>
			</dd>
			<dt>
				<img src="<?php echo(URL_ICONS_8X8); ?>/fold_off.gif" alt="[-]" title="Cacher" onclick="showOrHideVersion(this)" />&nbsp;
				v1.3.0
			</dt>
			<dd>
				<ul>
					<li class="evolution_majeure" title="Evolution majeure">Migration vers utf-8 (sources + bdd).</li>
					<li class="evolution_mineure" title="Evolution mineure">Module de connexion du professeur.</li>
				</ul>
			</dd>
			<dt>
				<img src="<?php echo(URL_ICONS_8X8); ?>/fold_off.gif" alt="[-]" title="Cacher" onclick="showOrHideVersion(this)" />&nbsp;
				v1.2.0
			</dt>
			<dd>
				<ul>
					<li class="evolution_mineure" title="Evolution mineure">Ihm de listing des p&eacute;riodes.</li>
					<li class="evolution_mineure" title="Evolution mineure">Ihm de listing des domaines.</li>
					<li class="evolution_mineure" title="Evolution mineure">Ihm de listing des mati&eacute;res.</li>
					<li class="evolution_mineure" title="Evolution mineure">Ihm de listing des comp&eacute;tences.</li>
					<li class="evolution_mineure" title="Evolution mineure">D&eacute;placement des modules d'administration dans le r&eacute;pertoire /admin.</li>
				</ul>
			</dd>
			<dt>
				<img src="<?php echo(URL_ICONS_8X8); ?>/fold_off.gif" alt="[-]" title="Cacher" onclick="showOrHideVersion(this)" />&nbsp;
				v1.1.0
			</dt>
			<dd>
				<ul>
					<li class="evolution_majeure" title="Evolution majeure">Ihm de listing et ajout d'&eacute;l&egrave;ves.</li>
					<li class="evolution_majeure" title="Evolution majeure">Ihm de listing et ajout d'&eacute;coles.</li>
					<li class="evolution_majeure" title="Evolution majeure">Ihm de listing et ajout de classes.</li>
					<li class="evolution_majeure" title="Evolution majeure">Ihm de listing des cycles et niveaux.</li>
				</ul>
			</dd>
			<dt>
				<img src="<?php echo(URL_ICONS_8X8); ?>/fold_off.gif" alt="[-]" title="Cacher" onclick="showOrHideVersion(this)" />&nbsp;
				v1.1.0
			</dt>
			<dd>
				<ul>
					<li class="evolution_majeure" title="Evolution majeure">Mise en place des plateformes (apache, php, mysql, phpmyadmin, subversion, trac, testlink).</li>
					<li class="evolution_majeure" title="Evolution majeure">Cr&eacute;ation du MCD - MLD de la base de donn&eacute;es.</li>
					<li class="evolution_majeure" title="Evolution majeure">Cr&eacute;ation d'un script sql d'installation de la base de donn&eacute;es.</li>
					<li class="evolution_majeure" title="Evolution majeure">Cr&eacute;ation d'un script sql de donn&eacute;es invariables.</li>
					<li class="evolution_majeure" title="Evolution majeure">Mise en place de la base de donn&eacute;es.</li>
				</ul>
			</dd>
		</dl>
	</dd>
</dl>

<h2>LEGENDE</h2>
<ul>
	<li class="bug_majeur">Bug majeur</li>
	<li class="bug_mineur">Bug mineur</li>
	<li class="bug_faible">Bug faible</li>
	<li class="evolution_majeure">Evolution majeure</li>
	<li class="evolution_mineure">Evolution mineure</li>
	<li class="evolution_faible">Evolution faible</li>
</ul>

<script type="text/javascript">
// <![CDATA[
	/**
	 * Cache ou affiche la description d'une version
	 */
	function showOrHideVersion(objThis, bShow)
	{
		// Le titre de la version est dans un "dt"
		var objVersionHead = null;
		if(objThis.tagName == 'DT')
		{
			objVersionHead = objThis;
		}
		else
		{
			// Si objThis n'est un "dt" alors on est appelé par l'image, il faut remonter au parent
			objVersionHead = objThis.parentNode;
		}

		// La description de la version est dans le "dd" juste à côté (noeud frère)
		var objVersionBody = objVersionHead.nextSibling;

		// Mais il faut faire attention au texte entre les 2 balises (le retour à la ligne)
		while(objVersionBody.nodeType != Node.ELEMENT_NODE)
		{
			objVersionBody = objVersionBody.nextSibling;
		}

		// On affiche ou on cache
		if(typeof(bShow) == 'undefined')
		{
			bShow = (objVersionBody.style.display == 'none') ? true : false;
		}

		objVersionBody.style.display = (bShow == false) ? 'none' : '';

		// On change la source de l'image
		var objImg = objVersionHead.firstChild;

		// Mais il faut faire attention au texte entre les 2 balises (le retour à la ligne)
		while(objImg.nodeType != Node.ELEMENT_NODE)
		{
			objImg = objImg.nextSibling;
		}

		if(bShow == true) // Attention on a déjà fait le changement d'affichage
		{
			objImg.src   = objImg.src.replace(/_on\./, "_off.");
			objImg.alt   = "[-]";
			objImg.title = "Cacher";
		}
		else
		{
			objImg.src   = objImg.src.replace(/_off\./, "_on.");
			objImg.alt   = "[+]";
			objImg.title = "Afficher";
		}

		return bShow;
	}

	/**
	 * Cache ou affiche toutes les sous-versions d'une version
	 */
	function showOrHideAllSubVersion(objThis, bShow)
	{
		// La description de la version principale est dans un "dd" parent au "a" ou l'on est
		var objVersionBody = objThis.parentNode;

		var aVersionHeadList = objVersionBody.getElementsByTagName('dt');

		for(i=0; i<aVersionHeadList.length; i++)
		{
			objVersionHead = aVersionHeadList[i];

			// Cache ou affiche la version
			// On recupére l'opération fait à la première version pour appliquer la même autre version
			bShow = showOrHideVersion(objVersionHead, bShow);
		}
	}

	/**
	 * Cache toutes les versions sauf la dernière
	 */
	function hideAllVersionsExceptLast()
	{
		var objVersionBlock = document.getElementById('version_block');

		var aVersionHeadList = document.getElementsByTagName('dt', objVersionBlock);

		showOrHideVersion(aVersionHeadList[0], true);
		showOrHideVersion(aVersionHeadList[0], true);

		for(i=2 ;i<aVersionHeadList.length; i++)
		{
			objVersionHead = aVersionHeadList[i];

			showOrHideVersion(objVersionHead, false);
		}
	}
// ]]>
</script>

<script type="text/javascript">
// <![CDATA[
	// ===== Main =====

	// Cache toutes les versions sauf la dernière
	hideAllVersionsExceptLast();
// ]]>
</script>