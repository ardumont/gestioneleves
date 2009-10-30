<?php
// destruction de la session
$_SESSION = array();
session_unregister('user_id');
session_destroy();
// preparation d'un message comme quoi nous sommes déconnecté
Message::addError("Vous &eacirc;tes maintenant d&eacute;connect&eacute;.");
// Redirection vers la page d'accueil du site
header("Location: ".SITE_URL);
?>