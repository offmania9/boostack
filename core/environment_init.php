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

// ====== ENVIRONMENT ======
require_once("env/global.env.php"); // import global environment
define('CURRENT_ENVIRONMENT', "local"); // [local] | [staging] | [production] | [create custom env]
require_once("env/".CURRENT_ENVIRONMENT.".env.php");

define('ROOTPATH', $config['path']);
if($config['developmentMode']) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}
spl_autoload_register('autoloadClass');
$boostack =  Boostack::getInstance();

require_once(ROOTPATH."lib/utilities.lib.php");
if($config['database_on']) $pdo = Database_PDO::getInstance($database['host'], $database['name'], $database['username'], $database['password']);
if($config['session_on'] && $config['database_on']) require_once(ROOTPATH."lib/session.lib.php");
if($config['language_on']) require_once(ROOTPATH."lib/check_language.lib.php");
if($config['checkMobile']){
	require_once (ROOTPATH."class/Mobile_Detect.php");
	$detect = new Mobile_Detect;
	if($detect->isMobile()){
		header("location: ".$boostack->mobileurl);
		exit();
	}
}

// ====== AUTOLOAD ======
/*
function autoload( $class, $dir = null ) {
	if (is_null($dir)) $dir = ROOTPATH."class/";
	foreach (scandir($dir) as $file) {
		// directory?
		if (is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' ) autoload( $class, $dir.$file.'/' );
		// php file?
		if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) ) {
			// filename matches class?
			if ( str_replace( '.php', '', $file ) == $class || str_replace( '.Class.php', '', $file ) == $class ) {
				include $dir . $file;
			}
		}
	}
}
*/
function autoloadClass($className){
		$cn = explode("_",$className);
		$filename = ROOTPATH."class/";
	    $cnt = count($cn);
		if($cnt==1)
			$filename .= $className . ".Class.php";
	    else{
			$i = 0;
			for($i; $i<$cnt-1;$i++)
				$filename .= $cn[$i]."/";
			$filename .= $className.".Class.php";
		}
		if (is_readable($filename))
			require_once($filename);
}
?>