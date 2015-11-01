<?
/**
 * Boostack: environment_init.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
 */


$init['current_env'] = "local";
$path = $_SERVER['DOCUMENT_ROOT']."/foodraising/";

// ====== ENVIRONMENT ======

// local
$init['url']['local'] = "http://localhost/foodraising/";
$init['path']['local'] = $_SERVER['DOCUMENT_ROOT']."/foodraising/";
$database['host']['local'] = 'localhost';
$database['name']['local'] = 'foodraising';
$database['username']['local'] = 'root';
$database['password']['local'] = '';

// staging
$init['url']['staging'] = "http://dev.foodraising.netatlas.it/";
$init['path']['staging'] = $_SERVER['DOCUMENT_ROOT']."/";
$database['host']['staging'] = 'localhost';
$database['name']['staging'] = 'fr_prod_8_10_15';
$database['username']['staging'] = 'root';
$database['password']['staging'] = 'admin';

// production
$init['url']['production'] = "";
$init['path']['production'] = $_SERVER['DOCUMENT_ROOT']."/foodraising/";
$database['host']['production'] = 'localhost';
$database['name']['production'] = '';
$database['username']['production'] = 'root';
$database['password']['production'] = 'admin';

// image config
$MAX_UPLOAD_IMAGE_SIZE = 2097152; // 2 MB
$MAX_UPLOAD_NAMEFILE_LENGTH = 100;
$MAX_UPLOAD_GENERALFILE_SIZE = 4194304; //4 MB

// modules config
$config['geolocation_on'] = false;
$config['database_on'] = false;
$config['session_on'] = false;
$config['checklanguage'] = false;
$config['checkMobile'] = false;
$config['checkCookie'] = false;
$config['log_on'] = true;

// cookies
$config['cookieExpire'] = 2505600;  //60*60*24*29 = 29days
$config['cookieName'] = "5asmbstk_16"; //md5 key

$config['datetimeFormatString'] = "d-m-Y H:s"; //md5 key

CONST PRIVILEGE_SYSTEM = 0;
CONST PRIVILEGE_SUPERADMIN = 1;
CONST PRIVILEGE_ADMIN = 2;
CONST PRIVILEGE_USER = 3;
date_default_timezone_set('UTC');

// ====== END CONFIGURATION ======

$config['current_url'] = $init['url'][$init['current_env']];
$config['developmentMode'] = $developmentMode;
define('ROOTPATH', $init['path'][$init['current_env']]);

if($developmentMode) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

spl_autoload_register( 'autoload' );
function autoload( $class, $dir = null ) {
	if ( is_null( $dir ) ) $dir = ROOTPATH."class/";
	foreach ( scandir( $dir ) as $file ) {
		// directory?
		if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' ) autoload( $class, $dir.$file.'/' );
		// php file?
		if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) ) {
			// filename matches class?
			if ( str_replace( '.php', '', $file ) == $class || str_replace( '.Class.php', '', $file ) == $class ) {
				include $dir . $file;
			}
		}
	}
}

//require_once(ROOTPATH."class/Boostack.Class.php");
$boostack = new Boostack($config);

require_once(ROOTPATH."lib/utilities.lib.php");
if($boostack->database_on) require_once(ROOTPATH."lib/database.lib.php");
if($boostack->session_on && $boostack->database_on) require_once(ROOTPATH."lib/session.lib.php");
if($boostack->checklanguage) require_once(ROOTPATH."lib/check_language.lib.php");
if($boostack->checkMobile){
	require_once (ROOTPATH."class/Mobile_Detect.php");
	$detect = new Mobile_Detect;
	if($detect->isMobile()){
		header("location: ".$boostack->mobileurl);
		exit();
	}
}
?>