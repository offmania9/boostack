<?
/**
 * Boostack: environment_init.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
require_once("class/Boostack.Class.php");
$boostack = new Boostack(true);
require_once("lib/utilities.lib.php");
require_once("class/User.Class.php");
if($boostack->database_on) require_once("lib/database.lib.php");
if($boostack->session_on && $boostack->database_on) require_once("lib/session.lib.php");
if($boostack->checklanguage) require_once("lib/check_language.lib.php");

if($boostack->checkMobile){
	require_once 'class/Mobile_Detect.php';
	$detect = new Mobile_Detect;
	if($detect->isMobile()){
		header("location: ".$boostack->mobileurl);
        exit();
	}
}
?>