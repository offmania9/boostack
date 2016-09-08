<?php
/**
 * Boostack: login.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */

// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Home");
// #######################
$error = "";
require_once $boostack->registerTemplateFile("boostack/header.phtml");
if($boostack->getConfig('session_on')) {
    if (Utils::checkAcceptedTimeFromLastRequest($objSession->LastTryLogin)) {
        if (!$objSession->IsLoggedIn()) {
            if (isset($_POST['btk_usr'])) {
                try {
                    if($boostack->getConfig('csrf_on'))
                        $objSession->CSRFCheckValidity($_POST);
                    $user = Utils::sanitizeInput($_POST["btk_usr"]);
                    $password = Utils::sanitizeInput($_POST["btk_pwd"]);
                    $rememberMe = (isset($_POST['rememberme']) && $_POST['rememberme'] == '1' && $boostack->getConfig('cookie_on')) ? true : false;
                    $objSession->LastTryLogin = time();
                    $anonymousUser = new User();
                    Utils::checkStringFormat($password);
                    if ($anonymousUser->tryLogin($user, $password, $rememberMe,false,false)) {
                        header("Location: " . $boostack->getFriendlyUrl("login"));
                        exit();
                    }
                    elseif($anonymousUser->tryLogin($user, $password, $rememberMe,true,false)) {
                        header("Location: " . $boostack->getFriendlyUrl("login"));
                        exit();
                    }
                    $error = "Username or password not valid.";
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    $boostack->writeLog("Login.php : ".$e->getMessage()." trace:".$e->getTraceAsString(),"user");
                }
            }
        }
    } else
        $error = "Too much request. Wait some seconds";
}
if($boostack->getConfig('session_on') && $objSession->IsLoggedIn())
    require_once $boostack->registerTemplateFile("boostack/content_login_logged.phtml");
else
    require_once $boostack->registerTemplateFile("boostack/content_login.phtml");

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Login Page");
// #######################
?>