<?php
/**
 * Boostack: login.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */

// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Home");
// #######################
$error = "";
require_once $boostack->registerTemplateFile("boostack/header.phtml");
if($boostack->getConfig('session_on')) {
    if(isset($_POST["btk_usr"]) && isset($_POST["btk_pwd"])) {
        $user = !empty($_POST["btk_usr"]) ? Utils::sanitizeInput($_POST["btk_usr"]) : null;
        $password = !empty($_POST["btk_pwd"]) ? Utils::sanitizeInput($_POST["btk_pwd"]) : null;
        $rememberMe = (isset($_POST['rememberme']) && $_POST['rememberme'] == '1' && $boostack->getConfig('cookie_on')) ? true : false;
        $loginResult = Auth::tryLogin($user,$password,$rememberMe);
        if($loginResult->hasError()) $error = $loginResult->getErrorMessage();
    }
}
if($boostack->getConfig('session_on') && Auth::isLoggedIn())
    require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");
else
    require_once $boostack->registerTemplateFile("boostack/content_login.phtml");

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

#$loginMessage = $objSession->StartLoginProcess($_POST['btk_usr'], $_POST["btk_pwd"], $_POST['rememberme']);

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Login Page");
// #######################
?>