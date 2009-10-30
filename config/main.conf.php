<?php
//==============================================================================
// Chemins divers et varies (pour le serveur).
//==============================================================================

// Chemin absolu jusqu'a la racine du site (pas de "/" a la fin).
//define('PATH_ROOT',    "XXXXXXXXXX");
// Pour le dev :
define('PATH_ROOT',    str_replace("\\", "/", realpath(dirname(__FILE__)."/..")));

// Les dossiers principaux du site (pas de "/" a la fin).
define('PATH_CONFIG',  PATH_ROOT."/config");
define('PATH_PHP_LIB', PATH_ROOT."/libraries/php");
define('PATH_PAGES',   PATH_ROOT."/pages");
define('PATH_DATA',    PATH_ROOT."/data");
define('PATH_LOGS',    PATH_ROOT."/logs");

//==============================================================================
// Informations diverses sur le serveur et le site.
//==============================================================================

// Nom et port du serveur.
define('SERVER_NAME', getenv('SERVER_NAME'));
define('SERVER_PORT', getenv('SERVER_PORT'));

//==============================================================================
// URL diverses et variees (pour le client web).
//==============================================================================

// URL a ajouter a "http://SERVER_NAME:PORT" pour avoir la racine du site ( pas de "/" a la fin).
define('URL_ROOT', "/~tony/gestion_eleves");
// Pour le dev :
//define('URL_ROOT',        str_replace($_SERVER['DOCUMENT_ROOT'], "", PATH_ROOT));

// URL complete pour acceder au site.
define('SITE_URL', "http://".SERVER_NAME.":".SERVER_PORT.URL_ROOT);

// URL des dossiers principaux du site (pas de "/" a la fin).
define('URL_IMAGES',      SITE_URL."/images");
define('URL_JAVASCRIPT',  SITE_URL."/libraries/javascript");
define('URL_ICONS_16X16', SITE_URL."/images/icons/16x16");
define('URL_ICONS_32X32', SITE_URL."/images/icons/32x32");

//==============================================================================
// Autres chemins ou URL.
//==============================================================================
