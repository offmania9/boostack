<?
/**
 * Boostack: session.lib.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
 */
$objSession = ($config['csrf_on']) ? new Session_CSRF(): new Session_HTTP();
$objSession->Impress();
if ($config['cookie_on'] && isset($_COOKIE['' . $config['cookie_name']])) {
    $c = sanitizeInput($_COOKIE['' . $config['cookie_name']]);
    /*  in caso di utente non loggato ma con il remember-me cookie
        cerca di eseguire una loginByCookie */
    if (! $objSession->IsLoggedIn() && $c !== "") {
        if (!$objSession->loginByCookie($c)) { // cookie is set but wrong (manually edited)
            $boostack->logout();
            header("Location: " . $boostack->url);
        }
    }
}
?>