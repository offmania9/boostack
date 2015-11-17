<?
/**
 * Boostack: check_mobile.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
require_once (ROOTPATH . "class/Mobile_Detect.php");
$detect = new Mobile_Detect();
if ($detect->isMobile()) {
    header("location: " . $boostack->mobileurl);
    exit();
}
?>