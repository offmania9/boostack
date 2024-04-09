<?php

/**
 * ENVIRONMENT
 */
# Setup current environment
define('CURRENT_ENVIRONMENT', [current_environment]);  // 'local' | 'staging' | 'production'
# Setup project subfolder 
$config['document_root_subdir'] = '/'; // / or empty by default
# Setup protocol 
$config['protocol'] = '[protocol]';
# Setup port 
$config['port'] = '[port]';
# Setup Domain Name
$config['DN'] = '[dn]';
# Setup Alternative Domain Name
$config['DN_alternative'] = array();
# Setup Development Mode
$config['developmentMode'] = TRUE;
# Alert if Setup folder is visible
$config['checkIfSetupFolderExists'] = TRUE;

/**
 * DATABASE
 */
$config['database_on'] = [database_on];      // enable or disable Mysql database
$config['driver_pdo'] = "[driver_pdo]";
$config['db_host'] = '[db_host]';
$config['db_port'] = '[db_port]';
$config['db_name'] = '[db_name]';
$config['db_username'] = '[db_username]';
$config['db_password'] = '[db_password]';

/**
 * SESSION
 */
$config['session_on'] = [session_on];   // enable or disable Sessions (TRUE need $database_on=TRUE)
$config['csrf_on'] = [csrf_on];      // enable or disable CSRF validation (TRUE need $database_on=TRUE AND $session_on=TRUE)
$config['csrf_timeout'] = 1000;
$config['session_timeout'] = 7200; # 2h             // session max inactivity time (seconds)
$config['session_lifespan'] = 14400; # 4h    // session max duration (seconds)

/**
 * Rest API
 */
$config['api_on'] = [api_on];       // enable or disable boostack Rest API (#TRUE need $database_on=TRUE)
$config['api_expire'] = 60*60*24*10;    // Cookies expire (60*60*24 = 1day)
$config['api_secret_key'] = "[api_secret_key]";    // Cookies expire (60*60*24 = 1day)
$config['api_my_extended_classes_dir'] = $_SERVER['DOCUMENT_ROOT']."/my/models/Rest/"; 
$config['api_my_extended_namespace'] = '\My\Models\Rest\\'; 

/**
 * LOG
 */
$config['log_on'] = [log_on];       // enable or disable boostack Log (#TRUE need $database_on=TRUE)
$config['log_file'] = "logs/log.txt";
$config['log_dir'] = "../logs/";
$config['log_enabledTypes'] =
    array('error','failure','information','success','warning','user','cronjob');  //(Enable logging options ['error','failure','information','success','warning','user']

/**
 * LOGIN
 */
$config['userToLogin'] = "email";    // Username field for login process: "username" | "email" | "both"

$config['username_min_length'] = 5;
$config['username_max_length'] = 64;
$config['password_min_length'] = 6;
$config['password_max_length'] = 80;

$config['lockStrategy_on'] = [lockStrategy_on];
$config['login_lockStrategy'] = '[lockStrategy_type]'; // "timer" | "recaptcha" | FALSE (if you set timer remember to set login_secondsFormBlocked)
$config['login_maxAttempts'] = "[login_max_attempts]";
$config['login_secondsFormBlocked'] = "[login_seconds_blocked]";
$config['google_recaptcha-endpoint']= "https://www.google.com/recaptcha/api/siteverify";        //ReCaptcha Google endpoint
$config['reCaptcha_public'] = "[recaptcha_public]";       //recaptcha key
$config['reCaptcha_private'] = "[recaptcha_private]";      //recaptcha key

$config['use_custom_user_class'] = false;
$config['custom_user_class'] = '';

/**
 * COOKIES
 */
$config['cookie_on'] = [cookie_on];            // enable or disable Cookies (TRUE need $database_on=TRUE AND $session_on=TRUE)
$config['cookie_expire'] = [cookie_expire];    // Cookies expire (60*60*24*29 = 29days)
$config['cookie_name'] = "[cookie_name]";  // This key is used to generate custom cookie names

/**
 * LANGUAGE
 */
$config["language_on"] = TRUE;                  // enable or disable language check for Multilanguage features (see Language documentation)
$config["language_force_default"] = FALSE;
$config["enabled_languages"] = array("en");
$config["language_default"] = "en";             // must exists file: lang/[$defaultlanguage].inc.php es:lang/en.inc.php
$config["show_default_language_in_URL"] = FALSE;

/**
 * EMAILS
 */
$config['mail_on'] = FALSE;     // enable or disable send mail
$config["mail_admin"] = "info@getboostack.com";
$config["mail_noreply"] = "no-reply@getboostack.com";
$config["mail_maintenance"] = "mntn@getboostack.com";

/**
 * FILES AND IMAGES
 */
$config["max_upload_image_size"] = 2097152; // 2 MB
$config["max_upload_filename_length"] = 100;
$config["max_upload_filesize"] = 4194304; // 4 MB
$config["allowed_file_upload_types"] = array(/* TODO */);
$config["allowed_file_upload_extensions"] = array(/* TODO */);

/**
 * DATES AND TIMES
 */
$config["default_datetime_format"] = "d-m-Y H:i:s";
date_default_timezone_set('UTC');
#date_default_timezone_set('Europe/Rome');
#setlocale(LC_TIME, 'it_IT');

/**
 * SECURITY
 */
$config["seconds_accepted_between_requests"] = 0; // time accepted between each request
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);
// Uses a secure connection (HTTPS) if possible
#ini_set('session.cookie_secure', 1);

/**
 * GEOLOCALIZATION
 */
$config['geolocation_on'] = FALSE;  // enable or disable Geolocalization

/**
 * CUSTOM VARIABLES
 */
// TODO
// ====== MAINGUN CONFIFURATION
$config['useMailgun'] = TRUE;
$config["mail_from"] = "no-reply@getboostack.com";
$config["name_from"] = "";
$config["mail_bcc"] = "";
$config["mailgun_key"] = "";
$config["mailgun_endpoint"] = "https://api.eu.mailgun.net"; // For EU servers
$config["mailgun_domain"] = "";
$config["mail_validTime"] = 7200;

//function isSecureProtocol($forceTrueForReverseProxy = false) {
//    return $forceTrueForReverseProxy || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
//}

/**
 * DO NOT MODIFY
 */
$default_port = empty($config['port'])? '': ':' . $config['port'];
$defaultDN = $config['DN'].$default_port ;
$currentDN = (in_array($_SERVER['HTTP_HOST'], $config['DN_alternative'])) ? $_SERVER['HTTP_HOST'] . '' : $defaultDN . $config['document_root_subdir'];
$config['url'] = $config['protocol'] . "://" . $currentDN;

# Setup main project folder 
define('MAIN_PROJECT_FOLDER', "public");
if(!empty($_SERVER['DOCUMENT_ROOT'] ))
    define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] ."/". MAIN_PROJECT_FOLDER . "/");
else
    define('ROOTPATH',  MAIN_PROJECT_FOLDER . "/");

abstract class Environment
{
    const LOCAL = "local";
    const STAGING = "staging";
    const PRE_PRODUCTION = "pre_production";
    const PRODUCTION = "production";
}
?>