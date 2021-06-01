<?php

/**
 * ENVIRONMENT
 */

define('CURRENT_ENVIRONMENT', '[current_environment]');     // 'local' | 'staging' | 'production'
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '[rootpath]');
$config['protocol'] = '[protocol]://';
$defaultDN = '[url]';
$alternativeDN = array();
$thisDN = $_SERVER['HTTP_HOST'];
$config['document_root_subdir'] = '/';
$currentDN = (in_array($thisDN,$alternativeDN)) ? $thisDN.'/' : $defaultDN . $config['document_root_subdir'];
$config['url'] = $config['protocol'].$currentDN;
$config['developmentMode'] = TRUE;
$config['setupFolderExists'] = FALSE;

/**
 * DATABASE
 */
$config['database_on'] = [database_on];      // enable or disable Mysql database
$config['driver_pdo'] = "[driver_pdo]";
$database['host'] = '[db_host]';
$database['name'] = '[db_name]';
$database['username'] = '[db_username]';
$database['password'] = '[db_password]';

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

/**
 * GEOLOCALIZATION
 */
$config['geolocation_on'] = FALSE;  // enable or disable Geolocalization

/**
 * CUSTOM VARIABLES
 */
// TODO

//function isSecureProtocol($forceTrueForReverseProxy = false) {
//    return $forceTrueForReverseProxy || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
//}

?>