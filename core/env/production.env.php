<?
/**
 * Boostack: production.env.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
 */
// ====== ENVIRONMEN production ======
$config['url'] = "http://localhost/boostack/";
$config['path'] = $_SERVER['DOCUMENT_ROOT'] . "/boostack/";
$config['developmentMode'] = TRUE;

// ====== database
// enable or disable Mysql database
$config['database_on'] = FALSE;
$database['host'] = 'localhost';
$database['name'] = 'boostack';
$database['username'] = 'root';
$database['password'] = 'boostack';

// ====== sessions
// enable or disable Sessions (TRUE need $database_on=TRUE)
$config['session_on'] = FALSE;

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
// allows the import of files of labels in this language (It must contain a value of language)
$config["language_default"] = "en"; // must exists file: lang/[$defaultlanguage].inc.php es:lang/en.inc.php
                                    
// ====== mobile
                                    // enable or disable Mobile devices Checker
$config['checkMobile'] = FALSE;

// ====== log
// enable or disable boostack Log (#TRUE need $database_on=TRUE)
$config['log_on'] = FALSE;

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
$CURRENT_DATETIME_FORMAT = "d-m-Y H:s";

// ====== security
$TIME_ELAPSED_ACCEPTED = 3000;
?>