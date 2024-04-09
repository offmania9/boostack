<?php
require __DIR__ . '/../vendor/autoload.php';
Core\Environment::init();
/**
 * Boostack: login.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

use Core\Models\Config;
use Core\Models\Request;
use Core\Models\Auth;

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
            $errorMessage = $loginResult->error;
            $errorCode = $loginResult->code;
        }
    }
} catch (\Exception $e) {
    $errorMessage = $e->getMessage();
}

if (Auth::isLoggedIn()) {
    Core\Models\Template::render("login_logged.phtml", array(
        "canonical" =>  Core\Models\Request::getFriendlyUrl("home"),
        "pageTitle" => Core\Models\Language::getLabel("navigation.home"),
    ));
} else {
    Core\Models\Template::render("login.phtml", array(
        "canonical" =>  Core\Models\Request::getFriendlyUrl("login"),
        "pageTitle" => Core\Models\Language::getLabel("navigation.login"),
        "errorMessage" => $errorMessage
    ));
}
