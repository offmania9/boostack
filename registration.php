<?php
/**
 * Boostack: registration.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */

// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Registration");
// #######################

require_once $boostack->registerTemplateFile("boostack/header.phtml");

try {
    $registrationError = "";
    if(isset($_POST["email"]) && isset($_POST["psw1"]) && isset($_POST["psw2"])) {
        $email = Utils::sanitizeInput($_POST["email"]);
        $psw1 = Utils::sanitizeInput($_POST["psw1"]);
        $psw2 = Utils::sanitizeInput($_POST["psw2"]);
        if($psw1 !== $psw2) $registrationError = "Passwords must be equals";
        if(!Validator::email($email)) $registrationError = "Username format not valid";
        if(!Validator::password($psw1)) $registrationError = "Password format not valid";
        if(User::existsByEmail($email, false) || User::existsByUsername($email, false)) $registrationError = "Email already registered";
        if($boostack->getConfig('csrf_on')) $objSession->CSRFCheckValidity($_POST);
        if(strlen($registrationError) == 0) {
            $user = new User();
            $user->username = $email;
            $user->email = $email;
            $user->active = true;
            $user->pwd = $psw1;
            $user->save();
            Auth::loginByUserID($user->id);
        }
    }
} catch(Exception $e) {
    $registrationError = $e->getMessage();
}

if(Auth::isLoggedIn()) {
    require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");
}
else {
    require_once $boostack->registerTemplateFile("boostack/content_registration.phtml");
}

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Registration Page");
// #######################
?>