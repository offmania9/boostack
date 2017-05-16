<?php
/**
 * Boostack: login.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.1
 */

require_once "core/environment_init.php";

$errorMessage = "";
$errorCode = null;
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
    Template::render("login_logged.phtml");
else
    Template::render("login.phtml", array(
        "errorMessage" => $errorMessage
    ));

?>