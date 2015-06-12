<?
/**
 * Boostack: logout.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

require_once("core/environment_init.php");
if($boostack->database_on) {
    $boostack->renderOpenHtmlHeadTags();

    if ($boostack->session_on && isset($objSession) && $objSession->IsLoggedIn())
        $objSession->LogOut();

    if($boostack->checkcookie) {
        setcookie('' . $cookiename, false, time() - $cookieexpire);
        setcookie('' . $cookiename, false, time() - $cookieexpire, "/");
    }
    $boostack->renderCloseHtmlTag();
}
header("location: ".$boostack->url."");
exit();
?>