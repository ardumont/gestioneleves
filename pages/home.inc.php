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


if(Message::hasError() == true):
	echo h1("Erreur lors de l'authentification", $aObjectsToHide);
?>
	<ul class="form_error">
		<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
		<li><?php echo($sErrorMessage); ?></li>
		<?php endforeach; ?>
	</ul>
	<br />
<?php endif; ?>

<?php echo h1($sGuiTitle, $aObjectsToHide); ?>
<p>Voil&agrave; le gestionnaire d'&eacute;valuations de vos joyeux diablotins.</p>

<?php require_once(PATH_PAGES."/release_notes.inc.php"); ?>
