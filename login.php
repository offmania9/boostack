<?php
/**
 * Boostack: login.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */

// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Home");
// #######################
$error = "";
require_once $boostack->registerTemplateFile("boostack/header.phtml");

$loginMessage = $objSession->StartLoginProcess($_POST['btk_usr'],$_POST["btk_pwd"],$_POST['rememberme']);

if($boostack->getConfig('session_on') && $objSession->IsLoggedIn())
    require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");
else
    require_once $boostack->registerTemplateFile("boostack/content_login.phtml");



require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Login Page");
// #######################
?>