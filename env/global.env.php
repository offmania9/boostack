<?
/**
 * Boostack: local.env.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
 */
// ====== ENVIRONMEN local ======
$init['url'] = "http://localhost/foodraising/";
$init['path'] = $_SERVER['DOCUMENT_ROOT']."/foodraising/";

//database
$database['host'] = 'localhost';
$database['name'] = 'foodraising';
$database['username'] = 'root';
$database['password'] = '';

// modules config
$config['geolocation_on'] = false;
$config['database_on'] = false;
$config['session_on'] = false;
$config['checklanguage'] = false;
$config['checkMobile'] = false;
$config['checkCookie'] = false;
$config['log_on'] = false;

// cookies
$config['cookieExpire'] = 2505600;  //60*60*24*29 = 29days
$config['cookieName'] = "5asmbstk_16"; //login cookie key

// image config
$MAX_UPLOAD_IMAGE_SIZE = 2097152; // 2 MB
$MAX_UPLOAD_NAMEFILE_LENGTH = 100;
$MAX_UPLOAD_GENERALFILE_SIZE = 4194304; //4 MB

// date/time
date_default_timezone_set('UTC');
$CURRENT_DATETIME_FORMAT = "d-m-Y H:s";
?>