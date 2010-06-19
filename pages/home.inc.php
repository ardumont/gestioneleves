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
	$sQuery = <<< ____EOQ
		SELECT
			PROFESSEUR_NOM
		FROM PROFESSEURS
		WHERE PROFESSEUR_ID = {$_SESSION['PROFESSEUR_ID']}
____EOQ;
	// recupere le nom du professeur directement dans la variable
	$sUserName = Database::fetchOneValue($sQuery);
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

$sGuiTitle = "Bienvenue" . ( ($sUserName != "") ? " {$sUserName}," : "," );

//==============================================================================
// Affichage de la page
//==============================================================================
if(Message::hasError() == true): ?>
	<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Erreur lors de l'authentification</h1>
	<ul class="form_error">
		<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
		<li><?php echo($sErrorMessage); ?></li>
		<?php endforeach; ?>
	</ul>
	<br />
<?php endif; ?>

<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a><?php echo($sGuiTitle); ?></h1>
<p>Voil&agrave; le gestionnaire d'&eacute;valuations de vos joyeux diablotins.</p>

<?php require_once(PATH_PAGES."/release_notes.inc.php"); ?>
