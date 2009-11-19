<?php
//==============================================================================
// Préparation de l'affichage
//==============================================================================
// Récupère le navigateur pour discriminer l'utilisation d'ie
$sAgent = $_SERVER['HTTP_USER_AGENT'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<title>Gestionnaire de projets</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta content="Lionel SAURON" name="author" />
	<meta content="Antoine Romain DUMONT" name="author" />

	<link rel="stylesheet" type="text/css" href="../../default.css" media="all" />
	<link rel="stylesheet" type="text/css" href="../../main.css" media="all" />
	<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="../../main-ie.css" media="all" />
	<![endif]-->
</head>
<body>
	<div id="struct_left_panel">
		<div id="struct_menu">
			<h1>Menu</h1>
			<h4><a href="../../index.php"><img src="../../images/icons/16x16/home.png" alt="" title="Accueil" />Accueil</a></h4>
			<h5>&nbsp;</h5>
			<h2>Sommaire</h2>
				<h4><a href="#installation_environnement">Installation du serveur</a></h4>
				<h4><a href="#installation_environnement_dev">Installation IDE</a></h4>
				<h4><a href="#parametrage_environnement_dev">Paramétrage IDE</a></h4>
				<h4><a href="#lancement_eclipse">Lancement d'Eclipse</a></h4>
				<h4><a href="#selection_workspace">Sélection workspace</a></h4>
				<h4><a href="#installation_plugin_svn">Installation plugin SVN</a></h4>
				<h4><a href="#vue_repository">Vue SVN Repository</a></h4>
				<h4><a href="#ajout_repository">Ajout du repository</a></h4>
				<h4><a href="#description_contenu_repository">Contenu du repository</a></h4>
				<h4><a href="#premier_checkout">Premier Checkout</a></h4>
				<h4><a href="#vue_php">Vue PHP</a></h4>
				<h4><a href="#description_arbo">Arborescence</a></h4>
				<h4><a href="#mpd">Modèle Physique de données</a></h4>
				<h5>&nbsp;</h5>
			<h2><img src="../../images/icons/16x16/hhelp.png" alt="" title="Documentations" />Documentations</h2>
				<h5>&nbsp;</h5>
		</div>
		<div id="struct_licence">
			<table>
				<tr>
					<td colspan="2">
						<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">
							<img src="../../images/pub/button-cc.gif" alt="Creative Commons License"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://validator.w3.org/check?uri=referer" rel="nofollow">
							<img src="../../images/pub/button-xhtml.png" alt="Valid XHTML 1.0"/>
						</a>
					</td>
					<td>
						<a href="http://jigsaw.w3.org/css-validator/">
							<img src="../../images/pub/button-css.png" alt="Valid CSS"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://httpd.apache.org/">
							<img src="../../images/pub/button-apache.png" alt="Powered By Apache"/>
						</a>
					</td>
					<td>
						<a href="http://www.php.net/">
							<img src="../../images/pub/button-php.png" alt="Powered By PHP"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.mysql.com/">
							<img src="../../images/pub/button-mysql.png" alt="Powered By Mysql"/>
						</a>
					</td>
					<td>
						<a href="http://www.mozilla-europe.org/fr/firefox/">
							<img src="../../images/pub/button-firefox.png" alt="Developped for Firefox"/>
						</a>
					</td>
				</tr>
			</table>
		</div>
		<?php if(preg_match("/microsoft internet explorer/i", $sAgent) || preg_match("/msie/i", $sAgent)): ?>
			<div style="text-align:left;color:red;">
				Ce site est optimisé pour Mozilla Firefox ou tout navigateur respectant <a href="http://www.w3c.org/">les standards web</a> (chromium, chrome, epiphany, icecat, konqueror, opera, seamonkey, etc...).<br />
				Votre navigateur étant Microsoft Internet Explorer ou l'une de ses moutures, vous risquez de perdre en ergonomie d'utilisation avec cette application.<br />
			</div>
		<?php endif; ?>
	</div>
	<div id="struct_main">
		<h1><img src="../../images/icons/16x16/help.png" alt="" title="" /><img src="../../images/icons/16x16/head_sep.png" alt=")" title=")" />Manuel du développeur</h1>

		<h2><a name="sommaire">Sommaire</a></h2>
		<ol>
			<li><a href="#prerequis">Pré-requis</a></li>
			<li><a href="#installation_environnement">Installation du serveur (Apache, PHP, Mysql)</a></li>
			<li><a href="#installation_environnement_dev">Installation de l'IDE eclipse</a>
				<ol>
					<li><a href="#parametrage_environnement_dev">Paramétrage de l'IDE eclipse</a></li>
					<li><a href="#lancement_eclipse">Lancement d'Eclipse</a></li>
					<li><a href="#selection_workspace">Sélection du workspace</a></li>
					<li><a href="#installation_plugin_svn">Installation d'un plugin SVN</a></li>
					<li><a href="#vue_repository">Vue SVN Repository</a></li>
					<li><a href="#ajout_repository">Ajout du repository l'application</a></li>
					<li><a href="#description_contenu_repository">Description du contenu du repository</a></li>
					<li><a href="#premier_checkout">Premier Checkout</a></li>
					<li><a href="#vue_php">Vue PHP</a></li>
					<li><a href="#description_arbo">Description de l'arborescence</a></li>
					<li><a href="#mpd">Modèle physique de données</a></li>
				</ol>
			</li>
		</ol>

		<h2><a name="prerequis">Pré-requis</a></h2>
		<ol>
			<li>Etre inscrit sur sourceforge et rattaché au projet "l'application".</li>
			<li>Installer un navigateur web standard.</li>
			<li>Environnement de développement (IDE) correct (qui sache au moins gérer php, css, sql, svn, javascript).</li>
			<li>Commenter un maximum le code php et ce même si le code est soi-disant auto-suffisant (on ne pense pas tous de la même manière).</li>
			<li>Développer du code KISS - "Keep it simple stupid".</li>
			<li>Développer du code avec les API php.</li>
			<li>Développer au moins du xhtml 1.0.</li>
			<li>RTFM, savoir lire.</li>
		</ol>

		<h2><a name="prerequis">A bannir</a></h2>
		<h4>Techniques</h4>
		<ol>
			<li>Réinventer la roue</li>
			<li>Code générique</li>
			<li>javascript intrusif (fonctionnalité qui dépend entièrement du javascript)</li>
		</ol>

		<h2><a name="installation_environnement">Installation du serveur (Apache, PHP, Mysql)</a></h2>
		Se reporter au <a href="../install/index.php">manuel d'installation de l'application l'application</a>.

		<h2><a name="installation_environnement_dev">Installation de l'IDE Eclipse</a></h2>
		Que les choses soient claires, vous n'êtes pas obligé d'utiliser Eclipse.
		Toutefois, dans la mesure où les développeurs initiaux (nous) utilisont eclipse,
		nous savons comment paramétrer et utiliser cet outil. Nous vous proposons donc cette solution.<br />
		Si celle-ci ne vous convient pas, vous pouvez l'adapter avec l'IDE ou l'éditeur de texte de votre choix.
		Vous pourrez par la suite, améliorer ce manuel avec votre savoir.<br />
		Bref, pour installer eclipse, se rendre sur la page <a href="http://www.eclipse.org">eclipse.org</a> pour télécharger
		la version d'eclipse correspondant à votre système d'exploitation.
		Actuellement, j'utilise "Galileo" (eclipse 3.5) avec PDT (PHP Development Tools) mais avant j'utilisais le plugin
		phpeclipse. Encore une fois, c'est une question de choix.

		<h2><a name="parametrage_environnement_dev">Paramétrage de l'IDE Eclipse</a></h2>

		<h3><a name="lancement_eclipse">Lancement d'Eclipse</a></h3>
		<p>
			Allumer Eclipse.
			<br />
			<br />
			<img src="img/01.allumer_eclipse.png" alt="Allumer Eclipse" title="Allumer Eclipse" width="80%" />
		</p>

		<h3><a name="selection_workspace">Sélection du workspace</a></h3>
		<p>
			Le workspace doit être l'emplacement des sources de l'application, autrement dit le répertoire de travail
			qu'utilise apache. Chez moi, par exemple, cet emplacement est <i>/home/tony/public_html</i>.
			<br />
			<br />
			<img src="img/02.choisir_workspace.png" alt="Choisir le workspace" title="Choisir le workspace" width="80%" /><br />
		</p>

		<h3><a name="installation_plugin_svn">Installation d'un plugin SVN</a></h3>
		<p>
			Il nous faut un plugin svn permettant d'aller se synchroniser sur le repository car nous avons fait le
			choix du <a href="http://subversion.tigris.org/">SVN</a> plutot que <a href="http://www.nongnu.org/cvs/">CVS</a>.<br />

			Commençons par installer un plugin gérant <a href="http://subversion.tigris.org/">SVN</a>.<br />
			Au sein d'eclipse, nous avons le choix entre <a href="http://subclipse.tigris.org/servlets/ProjectProcess?pageID=p4wYuA">subclipse</a>
			et <a href="http://www.eclipse.org/subversive/downloads.php">subversive</a>.<br />

			J'ai fait le choix de <a href="http://subclipse.tigris.org/servlets/ProjectProcess?pageID=p4wYuA">subclipse</a>
			 mais vous faites comme vous préférez. Voici les étapes :<br />
			Clic dans le menu <i>Help</i> &gt; <i>Install new Software</i>.
			Une fenêtre de dialogue s'ouvre, cliquez sur le lien <i>Available Software Sites</i> (en-dessous du champ de saisie et du bouton <i>Add</i>).
			<br />
			<br />
			<img src="img/08.install_svn_plugin.1.png" alt="Install new Software" title="Install new Software" width="80%" />
			<br />
			<br />

			Ajouter le <a href="http://subclipse.tigris.org/update_1.6.x">site d'update</a> du plugin
			<a href="http://subclipse.tigris.org/servlets/ProjectProcess?pageID=p4wYuA">subclipse</a>.<br />
			<br />
			<img src="img/08.install_svn_plugin.2.png" alt="Install new Software" title="Install new Software" width="80%" />
			<br />
			<br />
			Une fois le site d'update ajouté au sein d'eclipse, aller chercher sur ce site le plugin désiré.<br />
			Vous cochez toutes les checkbox du site subclipse puis vous cliquez sur <i>Finish</i>.<br />
			L'installation commence par une vérification que tous les pré-requis du plugin sont satisfaits. Puis, celle-ci
			vous demande de valider la licence (qu'il faut lire :d).<br />
			A la fin de l'installation, eclipse vous propose de redémarrer. Acceptez et voila, au prochain démarrage,
			vous serez en mesure de continuer la lecture du manuel.
			<br />
			<br />
			<img src="img/08.install_svn_plugin.3.png" alt="Install new Software" title="Install new Software" width="80%" />
		</p>

		<h3><a name="vue_repository">Vue SVN Repository</a></h3>
		<p>
			L'étape suivante consiste à aller initier les sources du projet à partir du repository svn mis à votre disposition sur sourceforge.
			Pour cela, sélectionner la vue "SVN Repository" comme dans l'impression d'écran suivant.
			<br />
			<br />
			<img src="img/03.choisir_vue_svn_repository.png" alt="Choisir la vue 'SVN Repository'" title="Choisir la vue 'SVN Repository'" width="80%" />
		</p>

		<h3><a name="ajout_repository">Ajout du repository l'application</a></h3>
		<p>
			Maintenant, on peut se connecter au repository svn de l'application à l'url suivante : <i>https://l'application.svn.sourceforge.net/svnroot/l'application</i>.
			Dans ce processus, il vous sera demandé votre login et mot de passe. Il s'agit de vos identifiant de connexion à sourceforge.
			<br />
			<br />
			<img src="img/04.add_svn_repository.png" alt="Ajouter le repository de l'application" title="Ajouter le repository de l'application" width="80%" />
		</p>

		<h3><a name="description_contenu_repository">Description du contenu du repository</a></h3>
		Une fois la connexion établie, voici le listing des répertoires distants du repository à ce jour.
		<br />
		<br />
		<img src="img/05.listing_repository.png" alt="Listing du repository de l'application" title="Listing du repository de l'application" width="80%" />
		<br />
		<br />
		Il existe 3 répertoires :
		<ul>
			<li>branches : Il s'agit du répertoire des branches de développement (version qui bouge).</li>
			<li>tags : Il s'agit du répertoire des versions de livraison (version figée)</li>
			<li>trunk : Il s'agit du tronc principal. C'est la partie à partir de laquelle nous partons pour commencer des développements.</li>
		</ul>

		<h3><a name="premier_checkout">Premier Checkout</a></h3>
		<p>
			Maintenant que vous êtes connectés au repository, vous allez pouvoir effectuer votre premier checkout. C'est-à-dire, récupérer les sources courantes
			de développement dans votre repository.<br />
			Actuellement, nous travaillons à la finalisation de la v3.3.0.<br />
			Par ailleurs, nous avons commencé en parallèle les développements de la future version v3.4.0.<br />
			Nous travaillons donc avec des branches. L'idée, c'est donc d'aller dans le répertoire de la branche 3.4.0
			donc <i>https://l'application.svn.sourceforge.net/svnroot/l'application/branches/b3.4.0</i>.<br />
			Clic droit sur le répertoire <i>b3.4.0</i> &gt; <i>checkout</i>, la fenêtre de dialogue suivante s'ouvre.<br />
			Renseigner les éléments de la boîte de dialogue comme dans le screenshot et cliquer sur "Finish".<br />
			Voilà, votre premier checkout (récupération des sources) se lance.<br />
			Cela va créer un projet <i>l'application.b3.4.0</i> dans votre workspace.
			<br />
			<br />
			<img src="img/06.checkout.png" alt="Premier checkout" title="Premier checkout" width="80%" />
		</p>

		<h3><a name="vue_php">Vue PHP</a></h3>
		<p>
			Retourner dans la vue PHP d'Eclipse.<br />
			Vous avez désormais un projet nommé <i>l'application.b3.4.0</i>.<br />
			Il ne reste plus qu'un paramétrage, l'encodage. Il faut que le projet soit encodé en UTF-8.<br />
			Pour cela, clic droit sur le projet <i>l'application.b3.4.0</i> &gt; <i>Properties</i>, renseigner l'encodage à <i>UTF-8</i>.
			<b>Attention, sous windows, l'encodage par défaut n'est pas l'<i>UTF-8</i> mais <i>CP-1252</i></b>.
			<br />
			<br />
			<img src="img/07.encodage.png" alt="Paramétrer l'encodage en UTF-8" title="Paramétrer l'encodage en UTF-8" width="80%" />
		</p>

		<h3><a name="description_arbo">Description de l'arborescence</a></h3>
		Voici la description des répertoires de l'application.<br />
		<table border="1" style="border-collapse:collapse;">
			<thead>
				<tr>
					<th>Nom du répertoire</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><i>racine</i></td>
					<td>Racine du project contenant les pages d'index des IHMs de l'application</td>
				</tr>
				<tr>
					<td><i>config</i></td>
					<td>Répertoire de configuration de l'application (mis à jour par l'installeur)</td>
				</tr>
				<tr>
					<td><i>images</i></td>
					<td>Répertoire des images utilisées dans l'application (sauf manuels)</td>
				</tr>
				<tr>
					<td><i>install</i></td>
					<td>L'installeur de l'application</td>
				</tr>
				<tr>
					<td><i>libraries</i></td>
					<td>Les classes <i>métiers</i> ou <i>utils</i> utilisées dans l'application.</td>
				</tr>
				<tr>
					<td><i>logs</i></td>
					<td><b>Non utilisé à ce jour</b></td>
				</tr>
				<tr>
					<td><i>manuals</i></td>
					<td>Le répertoire des manuels de l'application (dont celui-ci)</td>
				</tr>
				<tr>
					<td><i>pages</i></td>
					<td>Les IHMs de l'application</td>
				</tr>
			</tbody>
		</table>

		<h3><a name="mpd">Modèle physique de données</a></h3>
		<img src="img/mpd.gestion_eleves.png" alt="MPD" title="MPD" width="90%" />

	</div>
</body>
</html>