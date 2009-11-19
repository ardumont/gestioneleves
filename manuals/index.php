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

	<link rel="stylesheet" type="text/css" href="../default.css" media="all" />
	<link rel="stylesheet" type="text/css" href="../main.css" media="all" />
	<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="../main-ie.css" media="all" />
	<![endif]-->
</head>
<body>
	<div id="struct_left_panel">
		<div id="struct_menu">
			<h1>Menu</h1>
			<h4><a href="../index.php"><img src="../images/icons/16x16/home.png" alt="" title="Accueil" />Accueil</a></h4>
			<h5>&nbsp;</h5>
			<h2><img src="../images/icons/16x16/hhelp.png" alt="" title="Documentations" />Documentations</h2>
				<h4><a href="developer/index.php"><img src="../images/icons/16x16/help.png" alt="" title="Manuel du développeur" />Manuel du développeur</a></h4>
		</div>
		<div id="struct_licence">
			<table>
				<tr>
					<td colspan="2">
						<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">
							<img src="../images/pub/button-cc.gif" alt="Creative Commons License"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://validator.w3.org/check?uri=referer" rel="nofollow">
							<img src="../images/pub/button-xhtml.png" alt="Valid XHTML 1.0"/>
						</a>
					</td>
					<td>
						<a href="http://jigsaw.w3.org/css-validator/">
							<img src="../images/pub/button-css.png" alt="Valid CSS"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://httpd.apache.org/">
							<img src="../images/pub/button-apache.png" alt="Powered By Apache"/>
						</a>
					</td>
					<td>
						<a href="http://www.php.net/">
							<img src="../images/pub/button-php.png" alt="Powered By PHP"/>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.mysql.com/">
							<img src="../images/pub/button-mysql.png" alt="Powered By Mysql"/>
						</a>
					</td>
					<td>
						<a href="http://www.mozilla-europe.org/fr/firefox/">
							<img src="../images/pub/button-firefox.png" alt="Developped for Firefox"/>
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
		<h1><img src="../images/icons/16x16/hhelp.png" alt="" title="" /><img src="../images/icons/16x16/head_sep.png" alt=")" title=")" />Documentations</h1>
		<ol>
			<li><a href="developer/index.php">Manuel du développeur</a></li>
		</ol>
	</div>
</body>
</html>