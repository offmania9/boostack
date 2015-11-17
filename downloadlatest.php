<?
/**
 * Boostack: downloadlatest.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
require_once ("core/environment_init.php");

$boostack->writeLog("Zip Download");
header("location: https://github.com/offmania9/Boostack/archive/master.zip");
exit();
?>