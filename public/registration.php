<?php
/**
 * Boostack: registration.php
 * ========================================================================
 * Copyright 2014-2023 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.1
 */

require_once "../core/environment_init.php";

$registrationError = "";
try {
    Config::constraint("session_on");
    if (Request::hasPostParam('reg-email') && Request::hasPostParam('reg-pwd1') && Request::hasPostParam('reg-pwd2')) {
        $email = Request::getPostParam('reg-email');
        $psw1 = Request::getPostParam('reg-pwd1');
        $psw2 = Request::getPostParam('reg-pwd2');
        $csrfToken = null;
        if (Config::get('csrf_on')){
            $csrfToken = Request::getPostParam('BCSRFT');
        }
        Auth::registration($email,$email,$psw1,$psw2,$csrfToken);
    }
} catch (Exception_Misconfiguration $em) {
    dd($em->getMessage());
} catch (Exception_Registration $e) {
    $registrationError = $e->getMessage();
}
catch (Exception $e) {
    $registrationError = $e->getMessage();
}

if (Auth::isLoggedIn()) {
    Template::render("login_logged.phtml");
} else {
    Template::render("registration.phtml", array(
        "registrationError" => $registrationError
    ));
}

?>