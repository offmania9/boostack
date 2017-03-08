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
$errorMessage = "";
$errorCode = null;
require_once $boostack->registerTemplateFile("boostack/header.phtml");

try {
    Config::constraint("session_on");
    $user = Request::getPostParam("btk_usr");
    $password = Request::getPostParam("btk_pwd");
    if ($user != null && $password != null) {
        $rememberMe = (Config::get('cookie_on') && Request::getPostParam("rememberme") == '1') ? true : false;
        if (Config::get('csrf_on')) $objSession->CSRFCheckValidity($_POST);
        $loginResult = Auth::loginByUsernameAndPlainPassword($user, $password, $rememberMe);
        if ($loginResult->hasError()) {
            $errorMessage = $loginResult->getErrorMessage();
            $errorCode = $loginResult->getCode();
        }
    }
    if (Auth::isLoggedIn())
        require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");
    else
        require_once $boostack->registerTemplateFile("boostack/content_login.phtml");
} catch (Exception_Misconfiguration $em) {
    dd($em->getMessage());
} catch(Exception $e) {
    $loginError = $e->getMessage();
}

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Login Page");
// #######################
?>