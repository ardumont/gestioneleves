<?php
//==============================================================================
// Chemins divers et variés (pour le serveur).
//==============================================================================

// Chemin absolu jusqu'à la racine du site (pas de "/" à la fin).
define('PATH_ROOT', "/home/tony/public_html/gestion_eleves");

// Les dossiers principaux du site (pas de "/" à la fin).
define('PATH_CONFIG',  PATH_ROOT."/config");
define('PATH_PHP_LIB', PATH_ROOT."/libraries/php");
define('PATH_PAGES',   PATH_ROOT."/pages");
define('PATH_DATA',    PATH_ROOT."/data");
define('PATH_LOGS',    PATH_ROOT."/logs");
define('PATH_XSD',    PATH_ROOT."/xsd");

//==============================================================================
// URL diverses et variées (pour le client web).
//==============================================================================

// URL à ajouter à "http://SERVER_NAME:PORT" pour avoir la racine du site ( pas de "/" à la fin).
define('URL_ROOT', "/~tony/gestion_eleves");

// URL des dossiers principaux du site (pas de "/" à la fin).
define('URL_IMAGES',      URL_ROOT."/images");
define('URL_JAVASCRIPT',  URL_ROOT."/libraries/javascript");
define('URL_ICONS_8X8',   URL_ROOT."/images/icons/8x8");
define('URL_ICONS_16X16', URL_ROOT."/images/icons/16x16");
define('URL_ICONS_32X32', URL_ROOT."/images/icons/32x32");
define('URL_XSD',         URL_ROOT."/xsd");

//==============================================================================
// Autres chemins ou URL.
//==============================================================================

// Librairies PHP spécifiques à cet applicatif.
define('PATH_APP_LIB', PATH_ROOT."/libraries/php_app");

//==============================================================================
// Informations diverses sur le serveur et le site.
//==============================================================================

// Nom et port du serveur.
define('SERVER_NAME', getenv('SERVER_NAME'));
define('SERVER_PORT', getenv('SERVER_PORT'));

// URL complète pour accéder au site.
define('SITE_URL', "http://".SERVER_NAME.":".SERVER_PORT.URL_ROOT);
