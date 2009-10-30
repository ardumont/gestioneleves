<?php
//==============================================================================
// Initialisation de la page
//==============================================================================

// ===== Fichier de configuration principal =====
require_once(PATH_CONF_INSTALL."/main.conf.php");

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
<?php // TODO : Supprimer cette note après la release de la v3.0.0 ?>
<h2>Note spéciale pour la version 3.3.0</h2>

<p>Cette version voit apparaître la notion de profil pour les utilisateurs.<br />
Un profil "administrateur" a été créé, avec tous les droits, et pour ne pas géner
 l'utilisation immédiate, tous les utilisateurs existants possèdent ce profil.</p>
<p>Dans la prochaine version, la section d'administration sera plus facilement accessible
 pour le profil "administrateur".<br />
Si vous ne voulez pas que certains utilisateurs accèdent à cette section,
 vous devez créer un profil avec les droits souhaités pour ces personnes.</p>
<p><strong>Dans la prochaine version, toutes les personnes ayant le profil "administrateur"
auront un accès simple et inconditionnel à la section d'administration.</strong></p>

<h2>Fin</h2>

<p>L'installation de ProjectSion est terminée.</p>

<p>Si vous avez déjà des utilisateurs, vous pouvez directement vous rendre sur la <a href="<?php echo(SITE_URL); ?>/">page d'accueil de ProjectSion</a>.</p>
<p>Sinon, avant d'utiliser ProjectSion, vous devrez avant tout créer de nouveaux utilisateurs.<br />
Pour cela rendez-vous sur la <a href="<?php echo(SITE_URL); ?>/admin.php">page d'administration de ProjectSion</a></p>
