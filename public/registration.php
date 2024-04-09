<?php
require __DIR__ . '/../vendor/autoload.php';
Core\Environment::init();
/**
 * Boostack: registration.php
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

$registrationError = "";
try {
    Config::constraint("session_on");
    if (Request::hasPostParam('reg-email') && Request::hasPostParam('reg-pwd1') && Request::hasPostParam('reg-pwd2')) {
        $email = Request::getPostParam('reg-email');
        $psw1 = Request::getPostParam('reg-pwd1');
        $psw2 = Request::getPostParam('reg-pwd2');
        $csrfToken = null;
        if (Config::get('csrf_on')) {
            $csrfToken = Request::getPostParam('BCSRFT');
        }
        Auth::registration($email, $email, $psw1, $psw2, $csrfToken);
    }
} catch (\Core\Exception\Exception_Misconfiguration $em) {
    dd($em->getMessage());
} catch (\Core\Exception\Exception_Registration $e) {
    $registrationError = $e->getMessage();
} catch (\Exception $e) {
    $registrationError = $e->getMessage();
}

if (Auth::isLoggedIn()) {
    Core\Models\Template::render("login_logged.phtml", array(
        "canonical" =>  Core\Models\Request::getFriendlyUrl("home"),
        "pageTitle" => Core\Models\Language::getLabel("navigation.home"),
    ));
} else {
    Core\Models\Template::render("registration.phtml", array(
        "canonical" =>  Core\Models\Request::getFriendlyUrl("registration"),
        "pageTitle" => Core\Models\Language::getLabel("navigation.registration"),
        "registrationError" => $registrationError
    ));
}
