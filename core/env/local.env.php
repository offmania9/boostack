<?php

define('CURRENT_ENVIRONMENT', "local");
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . "/");
$config['protocol'] = isSecureProtocol() ? 'https://' : 'http://';
$config['url'] = $config['protocol']."localhost/boostack/";
$config['developmentMode'] = TRUE;

// ====== database
// enable or disable Mysql database
$config['database_on'] = FALSE;
$database['host'] = '127.0.0.1';
$database['name'] = 'boostack';
$database['username'] = 'root';
$database['password'] = 'root';

// ====== sessions
// enable or disable Sessions (TRUE need $database_on=TRUE)
$config['session_on'] = FALSE;

// ====== Cross Site Request Forgery validation
// enable or disable CSRF validation (TRUE need $database_on=TRUE AND $session_on=TRUE)
$config['csrf_on'] = FALSE;

// ====== Username field for login process: "username" | "email" | "both"
$config['userToLogin'] = "username"; # "username" | "email" | "both"

// ====== cookie
// enable or disable Cookies (TRUE need $database_on=TRUE AND $session_on=TRUE)
$config['cookie_on'] = FALSE;
// Cookies expire
$config['cookie_expire'] = 2505600; // 60*60*24*29 = 29days
// This key is used to generate custom cookie names
$config['cookie_name'] = "5asmbstk_16";

// ====== geolocalization
// enable or disable Geolocalization
$config['geolocation_on'] = FALSE;

// ====== language
// enable or disable language check for Multilanguage features (see Language documentation)
$config["language_on"] = TRUE;
$config["language_force_default"] = FALSE;
$config["language_default"] = "en"; // must exists file: lang/[$defaultlanguage].inc.php es:lang/en.inc.php

// ====== mobile
// enable or disable Mobile devices Checker
$config['mobile_on'] = FALSE;
$config['mobile_url'] = NULL;

// ====== log
// enable or disable boostack Log (#TRUE need $database_on=TRUE)
$config['log_on'] = FALSE;
//(Enable logging options ['error','failure','information','success','warning','user']
$config['log_enabledTypes'] = array('error','failure','information','success','warning','user','cronjob');


// ====== email
// enable or disable send mail
$config['mail_on'] = FALSE;
$config["mail_admin"] = "info@boostack.com";
$config["mail_noreply"] = "no-reply@boostack.com";
$config["mail_maintenance"] = "mntn@boostack.com";

// ====== image config
$config["default_images_path"] = "img/";
$MAX_UPLOAD_IMAGE_SIZE = 2097152; // 2 MB
$MAX_UPLOAD_NAMEFILE_LENGTH = 100;
$MAX_UPLOAD_GENERALFILE_SIZE = 4194304; // 4 MB

// ====== date/time
date_default_timezone_set('UTC');
$CURRENT_DATETIME_FORMAT = "d-m-Y H:i:s";

// ====== security
$TIME_ELAPSED_ACCEPTED = 0;

function isSecureProtocol($forceTrueForReverseProxy = false) {
    return $forceTrueForReverseProxy || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}
?>