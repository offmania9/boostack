<?
/**
 * Boostack: session.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
$objSession = ($config['csrf_on']) ? new Session_CSRF(): new Session_HTTP();
$objSession->Impress();
if ($config['cookie_on'] && isset($_COOKIE['' . $config['cookie_name']])) {
    $c = sanitizeInput($_COOKIE['' . $config['cookie_name']]);
    if (! $objSession->IsLoggedIn() && $c !== "") {
        if (!$objSession->loginByCookie($c)) { // cookie is set but wrong (manually edited)
            $boostack->logout();
            header("Location: " . $boostack->url);
        }
    }
}
?>