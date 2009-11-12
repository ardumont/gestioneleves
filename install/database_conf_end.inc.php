<?php
//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Fichier de configuration principal =====
require_once(PATH_CONF_INSTALL."/main.conf.php");

// ===== Les librairies et les classes =====
require_once(PATH_PHP_LIB."/utils.lib.php");
require_once(PATH_PHP_LIB."/database.class.php");
require_once(PATH_PHP_LIB."/formvalidation.class.php");
require_once(PATH_PHP_LIB."/message.class.php");

//==============================================================================
// Préparation des données
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objFormInstall = new FormValidation();

$bConnectionOk    = $objFormInstall->getValue('connection', $_GET, 'convert_bool', false);
$bConfigFileReady = $objFormInstall->getValue('ready',      $_GET, 'convert_bool', false);

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

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Etape 3 - Fichier de configuration de la base MYSQL</h2>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if($bConfigFileReady == true): ?>
	<p>La connexion a réussi, les données ont été sauvegardées dans le fichier de configuration.</p>
<?php elseif($bConnectionOk == true): ?>
	<p>La connexion a réussi, mais Il y a eu une erreur lors de la création du fichier de configuration.</p>
<?php else: ?>
	<p>La connexion a echoué, veuillez vérifier les paramÃ¨tres de connexion.</p>
<?php endif; ?>

<div>
	<a href="?step=3">Précédent</a>
	<?php if($bConfigFileReady == true): ?>
		<a href="?step=4">Suivant</a>
	<?php endif; ?>
</div>