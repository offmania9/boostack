<?php

/**
 * ENVIRONMENT
 */

define('CURRENT_ENVIRONMENT', 'local');     // 'local' | 'staging' | 'production'
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/documents/boostack4/public/');
$config['protocol'] = 'http';
$defaultDN = 'localhost:8888/documents/boostack4/public/';
$alternativeDN = array();
$thisDN = $_SERVER['HTTP_HOST'];
$config['document_root_subdir'] = '';
$currentDN = (in_array($thisDN,$alternativeDN)) ? $thisDN.'/' : $defaultDN . $config['document_root_subdir'];
$config['url'] = $config['protocol']."://".$currentDN;
$config['developmentMode'] = TRUE;
$config['setupFolderExists'] = TRUE;

/**
 * DATABASE
 */
$config['database_on'] = TRUE;      // enable or disable Mysql database
$config['driver_pdo'] = "mysql";
$config['db_host'] = '127.0.0.1';
$config['db_port'] = '8889';
$config['db_name'] = 'boostack';
$config['db_username'] = 'root';
$config['db_password'] = 'root';

/**
 * SESSION
 */
$config['session_on'] = TRUE;   // enable or disable Sessions (TRUE need $database_on=TRUE)
$config['csrf_on'] = TRUE;      // enable or disable CSRF validation (TRUE need $database_on=TRUE AND $session_on=TRUE)
$config['csrf_timeout'] = 1000;
$config['session_timeout'] = 7200; # 2h             // session max inactivity time (seconds)
$config['session_lifespan'] = 14400; # 4h    // session max duration (seconds)

/**
 * Rest API
 */
$config['api_on'] = TRUE;       // enable or disable boostack Rest API (#TRUE need $database_on=TRUE)

/**
 * LOG
 */
$config['log_on'] = TRUE;       // enable or disable boostack Log (#TRUE need $database_on=TRUE)
$config['log_file'] = "logs/log.txt";
$config['log_dir'] = "../logs/";
$config['log_enabledTypes'] =
    array('error','failure','information','success','warning','user','cronjob');  //(Enable logging options ['error','failure','information','success','warning','user']

/**
 * LOGIN
 */
$config['userToLogin'] = "both";    // Username field for login process: "username" | "email" | "both"

$config['username_min_length'] = 5;
$config['username_max_length'] = 64;
$config['password_min_length'] = 6;
$config['password_max_length'] = 80;

$config['lockStrategy_on'] = FALSE;
$config['login_lockStrategy'] = ''; // "timer" | "recaptcha" | FALSE (if you set timer remember to set login_secondsFormBlocked)
$config['login_maxAttempts'] = "3";
$config['login_secondsFormBlocked'] = "180";
$config['google_recaptcha-endpoint']= "https://www.google.com/recaptcha/api/siteverify";        //ReCaptcha Google endpoint
$config['reCaptcha_public'] = "";       //recaptcha key
$config['reCaptcha_private'] = "";      //recaptcha key

$config['use_custom_user_class'] = false;
$config['custom_user_class'] = '';

/**
 * COOKIES
 */
$config['cookie_on'] = TRUE;            // enable or disable Cookies (TRUE need $database_on=TRUE AND $session_on=TRUE)
$config['cookie_expire'] = 2505600;    // Cookies expire (60*60*24*29 = 29days)
$config['cookie_name'] = "_85fee28cd";  // This key is used to generate custom cookie names

/**
 * LANGUAGE
 */
$config["language_on"] = TRUE;                  // enable or disable language check for Multilanguage features (see Language documentation)
$config["language_force_default"] = FALSE;
$config["enabled_languages"] = array("en");
$config["language_default"] = "en";             // must exists file: lang/[$defaultlanguage].inc.php es:lang/en.inc.php
$config["show_default_language_in_URL"] = FALSE;

/**
 * MOBILE
 */
$config['mobile_on'] = FALSE;       // enable or disable Mobile devices Checker
$config['mobile_url'] = NULL;

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

?>