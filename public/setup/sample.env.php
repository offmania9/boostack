<?php

/**
 * Boostack: env.php
 * ========================================================================
 * Copyright 2014-2026 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.2
 */

// In contesti CLI (es. migrator) l'autoload potrebbe non essere ancora caricato.
if (!class_exists(\My\Enums\DataType::class) && file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

use My\Enums\DataType;

/**
 * ENVIRONMENT
 */
# Setup current environment // 'local' | 'staging' | 'production'
define('CURRENT_ENVIRONMENT', [current_environment]);  // 'local' | 'staging' | 'production'
# Setup project subfolder "/" or empty by default
$config['document_root_subdir'] = '/';
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
# Setup Project Name
$config['project_name'] = "";

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
$config['db_charset'] = 'utf8mb4';
$config['db_collation'] = 'utf8mb4_unicode_ci';

# Setup Project Serial Number
$config['project_serial_number'] = md5($config['DN'] . $config['port'] . $config['project_name'] . $config['db_name']);

/**
 * SESSION
 */
$config['session_on'] = [session_on];   // enable or disable Sessions (TRUE need $database_on=TRUE)
$config['csrf_on'] = [csrf_on];      // enable or disable CSRF validation (TRUE need $database_on=TRUE AND $session_on=TRUE)
$config['csrf_timeout'] = 1000;
$config['session_timeout'] = 7200; # 2h    // session max inactivity time (seconds)
$config['session_lifespan'] = 14400; # 4h    // session max duration (seconds)

/**
 * Rest API
 */
$config['api_on'] = [api_on];       // enable or disable boostack Rest API (#TRUE need $database_on=TRUE)
$config['api_expire'] = 60 * 60 * 24 * 10;    // JWT expire (10day)
$config['api_secret_key'] = "[api_secret_key]";    // JWT HS256 secret key (min 32 chars)
$config['api_my_extended_classes_dir'] = $_SERVER['DOCUMENT_ROOT'] . "/my/controllers/Rest/";
$config['api_my_extended_namespace'] = '\My\Controllers\Rest\\';

/**
 * LOG
 */
$config['log_on'] = [log_on];       // enable or disable boostack Log (#TRUE need $database_on=TRUE)
$config['log_file'] = "logs/log.txt";
$config['log_dir'] = "../logs/";
$config['log_enabledTypes'] = array('error', 'failure', 'information', 'success', 'warning', 'user', 'cronjob');  //(Enable logging options ['error','failure','information','success','warning','user']

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

$config['reCaptcha_on'] = FALSE;
$config['reCaptcha_verify_endpoint'] = "https://www.google.com/recaptcha/api/siteverify";   //ReCaptcha Google endpoint
$config['reCaptcha_public_clientside_key'] = "";    //recaptcha public key
$config['reCaptcha_private_serverside_key'] = "";   //recaptcha private key

$config['use_custom_user_class'] = false;
$config['custom_user_class'] = '';

$passkeyHost = $config['DN'] ?? 'localhost';
if (strpos($passkeyHost, ':') !== false) {
    $passkeyHost = explode(':', $passkeyHost, 2)[0];
}
$passkeyOriginPort = !empty($config['port']) ? ':' . $config['port'] : '';
$config['passkey_on'] = TRUE;
$config['passkey_rp_name'] = $config['project_name'] ?? 'Boostack';
$config['passkey_rp_id'] = $passkeyHost;
$config['passkey_origin'] = $config['protocol'] . '://' . $passkeyHost . $passkeyOriginPort;
$config['passkey_cookie_name'] = 'passkey_available';

/**
 * SSO
 */
$config['SSO']["google"]["enabled"] = FALSE;
$config['SSO']["google"]['ID_client'] = '';
$config['SSO']["google"]['client_secret_id'] = '';
$config['SSO']["google"]['ID_directory_tenant'] = 'none';
$config['SSO']["google"]['client_secret_value'] = '';
$config['SSO']["google"]['callback_page'] = '[url]/sso/google'; # e.g. 'http://localhost:8686/sso/google
$config['SSO']["microsoft"]["enabled"] = FALSE;
$config['SSO']["microsoft"]['ID_client'] = '';
$config['SSO']["microsoft"]['ID_directory_tenant'] = '';
$config['SSO']["microsoft"]['client_secret_id'] = '';
$config['SSO']["microsoft"]['client_secret_value'] = '';
$config['SSO']["microsoft"]['callback_page'] = '[url]/sso/microsoft'; # e.g. http://localhost:8686/sso/microsoft

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
$config["language_variant_code"] = "";      // optional tenant/license override: es. it.myproject.inc.json (fallback: $config["language_default"].inc.json)
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
$config["allowed_file_upload_extensions"] = ["jpg", "png", "jpeg", "gif", "pdf", "doc", "docx", "p7m"];  // * = all or array with specific value 
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
$config["seconds_accepted_between_requests"] = 0; // time accepted between each request
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);
// Uses a secure connection (HTTPS) if possible
#ini_set('session.cookie_secure', 1);

/**
 * CACHING
 */
$config['cache_enabled'] = TRUE;  // enable or disable Chaching
$config['cache_routing_enabled'] = FALSE;  // enable or disable Routing Chache
$config['cache_routing_key'] = 'router_routes_list';  // cache key for routing

/**
 * CUSTOM VARIABLES
 * e.g:
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
