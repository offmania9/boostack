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
    $email = $request->getPostParam('email');
    $psw1 = $request->getPostParam('psw1');
    $psw2 = $request->getPostParam('psw2');
    if ($email != null && $psw1 != null && $psw2 != null) {
        if ($psw1 !== $psw2) $registrationError = "Passwords must be equals";
        if (!Validator::email($email)) $registrationError = "Username format not valid";
        if (!Validator::password($psw1)) $registrationError = "Password format not valid";
        if (User::existsByEmail($email, false) || User::existsByUsername($email, false)) $registrationError = "Email already registered";
        if ($boostack->getConfig('csrf_on')) $objSession->CSRFCheckValidity($request->getPostParam(''));
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
} catch (Exception $e) {
    $registrationError = $e->getMessage();
}

if (Auth::isLoggedIn()) {
    require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");
} else {
    require_once $boostack->registerTemplateFile("boostack/content_registration.phtml");
}

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Registration Page");
// #######################
?>