<?php
/**
 * Boostack: registration.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.1
 */

require_once "core/environment_init.php";

$registrationError = "";
try {
    Config::constraint("session_on");
    if (Request::hasPostParam('email') && Request::hasPostParam('psw1') && Request::hasPostParam('psw2')) {
        $email = Request::getPostParam('email');
        $psw1 = Request::getPostParam('psw1');
        $psw2 = Request::getPostParam('psw2');
        if ($psw1 !== $psw2) $registrationError = "Passwords must be equals";
        if (!Validator::email($email)) $registrationError = "Username format not valid";
        if (!Validator::password($psw1)) $registrationError = "Password format not valid";
        if (User::existsByEmail($email, false) || User::existsByUsername($email, false)) $registrationError = "Email already registered";
        if (Config::get('csrf_on')) Session::CSRFCheckValidity(Request::getPostArray());
        if (strlen($registrationError) == 0) {
            $user = new User();
            $user->username = $email;
            $user->email = $email;
            $user->active = true;
            $user->pwd = $psw1;
            $user->save();
            Auth::loginByUserID($user->id);
        }
    }
} catch (Exception_Misconfiguration $em) {
    dd($em->getMessage());
} catch (Exception $e) {
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