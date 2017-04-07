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
    if (Request::hasPostParam("btk_usr") && Request::hasPostParam("btk_pwd")) {
        $user = Request::getPostParam("btk_usr");
        $password = Request::getPostParam("btk_pwd");
        $rememberMe = (Config::get('cookie_on') && Request::hasPostParam("rememberme") && Request::getPostParam("rememberme") == '1') ? true : false;
        $loginResult = Auth::loginByUsernameAndPlainPassword($user, $password, $rememberMe);
        if ($loginResult->hasError()) {
            $errorMessage = $loginResult->getErrorMessage();
            $errorCode = $loginResult->getCode();
        }
    }
} catch (Exception_Misconfiguration $em) {
    dd($em->getMessage());
} catch(Exception $e) {
    $errorMessage = $e->getMessage();
}
if (Auth::isLoggedIn())
    require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");
else
    require_once $boostack->registerTemplateFile("boostack/content_login.phtml");

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Login Page");
// #######################
?>