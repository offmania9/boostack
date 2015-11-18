<?
/**
 * Boostack: index.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Home");
// #######################
$error = "";
if (! $objSession->IsLoggedIn()) {
    if (isset($_POST['btk_usr'])) {
        try {
            $objSession->CSRFCheckTokenValidity($_POST);
            $email = sanitizeInput($_POST["btk_usr"]);
            $password = sanitizeInput($_POST["btk_pwd"]);
            $anonymousUser = new User();
            $anonymousUser->checkEmailFormat($email);
            $anonymousUser->checkPasswordFormat($password);
            $anonymousUser->checkEmailIntoDB($email);
            if ($anonymousUser->tryLogin($email, $password, $config['cookie_on'] && isset($_POST['rememberme']))) {
                header("Location: " . $boostack->getFriendlyUrl("login"));
                exit();
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    require_once $boostack->registerTemplateFile("boostack/header.phtml");
    require_once $boostack->registerTemplateFile("boostack/content_login.phtml");
}
else {
    require_once $boostack->registerTemplateFile("boostack/header.phtml");
    require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");    
}

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Login Page");
// #######################
?>