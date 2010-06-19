<?php
//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Les fichiers de configuration =====

// Le fichier principal
require_once(PATH_INSTALL_ROOT."/config/main.conf.php");

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

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h2>Fin</h2>

<p>L'installation de l'application est terminée.</p>

<p>Si vous avez déjà des utilisateurs, vous pouvez directement vous rendre sur la <a href="<?php echo(SITE_URL); ?>/">page d'accueil de l'application</a>.</p>
<p>Sinon, avant d'utiliser l'application, vous devrez avant tout créer de nouveaux utilisateurs.<br />
Pour cela rendez-vous sur la <a href="<?php echo(SITE_URL); ?>/admin.php">page d'administration</a>.<br />
<strong>Votre premier utilisateur a comme mot de passe "test".</strong></p>
