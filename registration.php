<?php
/**
 * Boostack: registration.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.4
 */

// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Registration");
// #######################

require_once $boostack->registerTemplateFile("boostack/header.phtml");

if(isset($_POST["email"]) && isset($_POST["psw1"]) && isset($_POST["psw2"])) {
    $email = Utils::sanitizeInput($_POST["email"]);
    $psw1 = Utils::sanitizeInput($_POST["psw1"]);
    $psw2 = Utils::sanitizeInput($_POST["psw2"]);
    if(Utils::checkEmailFormat($email) && $psw1 === $psw2) {
        $user = new User();
        $user->username = $email;
        $user->email = $email;
        $user->active = true;
        $user->pwd = $psw1;
        $user->save();
        require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");
    }
    else
        require_once $boostack->registerTemplateFile("boostack/content_registration.phtml");
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