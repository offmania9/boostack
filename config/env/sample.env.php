<?php

/**
 * ENVIRONMENT
 */
define('CURRENT_ENVIRONMENT', "[current_environment]");     // 'local' | 'staging' | 'production'
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . "[rootpath]");
$config['protocol'] = isSecureProtocol() ? 'https://' : 'http://';
$config['url'] = $config['protocol']."[url]";
$config['developmentMode'] = TRUE;

/**
 * DATABASE
 */
$config['database_on'] = [database_on];      // enable or disable Mysql database
$database['host'] = '[db_host]';
$database['name'] = '[db_name]';
$database['username'] = '[db_username]';
$database['password'] = '[db_password]';

/**
 * SESSION
 */
$config['session_on'] = [session_on];   // enable or disable Sessions (TRUE need $database_on=TRUE)
$config['csrf_on'] = TRUE;      // enable or disable CSRF validation (TRUE need $database_on=TRUE AND $session_on=TRUE)
$config['session_timeout'] = 3600;
$config['session_lifespan'] = 4600;

/**
 * LOG
 */
$config['log_on'] = [log_on];       // enable or disable boostack Log (#TRUE need $database_on=TRUE)
$config['log_enabledTypes'] =
    array('error','failure','information','success','warning','user','cronjob');  //(Enable logging options ['error','failure','information','success','warning','user']

/**
 * LOGIN
 */
$config['userToLogin'] = "username";    // Username field for login process: "username" | "email" | "both"

$config['username_min_length'] = 5;
$config['username_max_length'] = 18;
$config['password_min_length'] = 6;
$config['password_max_length'] = 12;

$config['lockStrategy_on'] = FALSE;
$config['login_lockStrategy'] = "timer"; // "timer" | "recaptcha" | FALSE (if you set timer remember to set login_secondsFormBlocked)
$config['login_secondsFormBlocked'] = 180;
$config['login_maxAttempts'] = 3;
$config['login_secondsFormBlocked'] = 10;
$config['google_recaptcha-endpoint']= "https://www.google.com/recaptcha/api/siteverify";        //ReCaptcha Google endpoint
$config['reCaptcha_public'] = "6LfCzxQUAAAAAJwvPlEHpsHCMdLxeFsKhwON5Epl";       //recaptcha key
$config['reCaptcha_private'] = "6LfCzxQUAAAAALgBGC2ZHI8GYXq7UDQkvFTf8M2C";      //recaptcha key

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
$config["mail_admin"] = "info@boostack.com";
$config["mail_noreply"] = "no-reply@boostack.com";
$config["mail_maintenance"] = "mntn@boostack.com";

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
date_default_timezone_set('UTC');
$CURRENT_DATETIME_FORMAT = "d-m-Y H:i:s";

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

function isSecureProtocol($forceTrueForReverseProxy = false) {
    return $forceTrueForReverseProxy || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}

?>