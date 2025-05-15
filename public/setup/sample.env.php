<?php

/**
 * Boostack: env.php
 * ========================================================================
 * Copyright 2014-2025 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */

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
$config['DN_alternative'] = array(); // / or empty by default
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
$config['api_expire'] = 60 * 60 * 24 * 10;    // Cookies expire (60*60*24 = 1day)
$config['api_secret_key'] = "[api_secret_key]";    // Cookies expire (60*60*24 = 1day)
$config['api_my_extended_classes_dir'] = $_SERVER['DOCUMENT_ROOT'] . "/my/controllers/Rest/";
$config['api_my_extended_namespace'] = '\My\Controllers\Rest\\';

/**
 * LOG
 */
$config['log_on'] = [log_on];       // enable or disable boostack Log (#TRUE need $database_on=TRUE)
$config['log_file'] = "logs/log.txt";
$config['log_dir'] = "../logs/";
$config['log_enabledTypes'] =
    array('error', 'failure', 'information', 'success', 'warning', 'user', 'cronjob');  //(Enable logging options ['error','failure','information','success','warning','user']

/**
 * LOGIN
 */
$config['userToLogin'] = "email";    // Username field for login process: "username" | "email" | "both"

$config['username_min_length'] = 5;
$config['username_max_length'] = 64;
$config['password_min_length'] = 6;
$config['password_max_length'] = 80;

$config['lockStrategy_on'] = [lockStrategy_on];
$config['login_maxAttempts'] = "5";
$config['login_secondsFormBlocked'] = "3";

$config['reCaptcha_on'] = FALSE;
$config['reCaptcha_verify_endpoint'] = "https://www.google.com/recaptcha/api/siteverify";   //ReCaptcha Google endpoint
$config['reCaptcha_public_clientside_key'] = "";    //recaptcha public key
$config['reCaptcha_private_serverside_key'] = "";   //recaptcha private key

$config['use_custom_user_class'] = false;
$config['custom_user_class'] = '';

/**
 * SSO
 */
$config['SSO']["google"]["enabled"] = FALSE;
$config['SSO']["google"]['ID_client'] = '';
$config['SSO']["google"]['client_secret_id'] = '';
$config['SSO']["google"]['ID_directory_tenant'] = 'none';
$config['SSO']["google"]['client_secret_value'] = '';
$config['SSO']["google"]['callback_page'] = 'http://localhost:8686/sso/google';
$config['SSO']["microsoft"]["enabled"] = FALSE;
$config['SSO']["microsoft"]['ID_client'] = '';
$config['SSO']["microsoft"]['ID_directory_tenant'] = '';
$config['SSO']["microsoft"]['client_secret_id'] = '';
$config['SSO']["microsoft"]['client_secret_value'] = '';
$config['SSO']["microsoft"]['callback_page'] = 'http://localhost:8686/sso/microsoft';

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
$config["max_upload_image_size"] = 16777216; // 16 MB
$config["max_upload_filename_length"] = 150;
$config["max_upload_filesize"] = 16777216; // 16 MB
$config["allowed_file_upload_types"] = "*"; // * = all or array with specific value 
$config["allowed_file_upload_extensions"] = ["jpg", "png", "jpeg", "gif", "pdf", "doc", "docx"];  // * = all or array with specific value 
$config["uploaded_documents_path"] = $_SERVER['DOCUMENT_ROOT'] . "/uploads/temp/";

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
$config["seconds_accepted_between_requests"] = 0; // seconds accepted between each request (0 = all request will be accepted)
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);
// Uses a secure connection (HTTPS) if possible
#ini_set('session.cookie_secure', 1);

/**
 * CACHING
 */
$config['cache_enabled'] = FALSE;  // enable or disable Chaching

/**
 * CUSTOM VARIABLES
 */
$config["notification_email_max_retries"] = -1; // default -1 = no limit
$config["upload_documents_path"] = $_SERVER['DOCUMENT_ROOT'] . "/uploads/temp/";
$config["uploaded_images_path"] = $_SERVER['DOCUMENT_ROOT'] . "/uploads/temp/profile_pics/";
$config["uploaded_profile_pics_path"] = $_SERVER['DOCUMENT_ROOT'] . "/public/assets/img/user/";

/**
 * DO NOT MODIFY
 */
$default_port = empty($config['port']) ? '' : ':' . $config['port'];
$defaultDN = $config['DN'] . $default_port;
if (php_sapi_name() == 'cli' || empty($_SERVER['REQUEST_METHOD'])) {
    define('ROOTPATH',  __DIR__ . "/../");
    $currentDN = $defaultDN . $config['document_root_subdir'];
} else {
    $currentDN = (in_array($_SERVER['HTTP_HOST'], $config['DN_alternative'])) ? $_SERVER['HTTP_HOST'] . '' : $defaultDN . $config['document_root_subdir'];

    # Setup main project folder 
    define('MAIN_PROJECT_FOLDER', "public");
    if (!empty($_SERVER['DOCUMENT_ROOT']))
        define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . "/" . MAIN_PROJECT_FOLDER . "/");
    else
        define('ROOTPATH',  MAIN_PROJECT_FOLDER . "/");
}
$config['url'] = $config['protocol'] . "://" . $currentDN;

abstract class Environment
{
    const LOCAL = "local";
    const STAGING = "staging";
    const PRE_PRODUCTION = "pre_production";
    const PRODUCTION = "production";
}
