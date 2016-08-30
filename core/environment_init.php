<?php

$envPath = realpath(__DIR__."/env/env.php");
if($envPath && is_file($envPath)) {
    require_once $envPath;
} else {
    echo "Environment file not found";
    exit();
}

require_once (ROOTPATH . "core/env/global.env.php");
require_once (ROOTPATH . "core/class/Utils.Class.php");

spl_autoload_register('Utils::autoloadClass');
$boostack = Boostack::getInstance();
if ($config['developmentMode']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

$CURRENTUSER = NULL;
if ($boostack->getConfig('database_on')){
    Database_PDO::getInstance($database['host'], $database['name'], $database['username'], $database['password']);
    if ($boostack->getConfig('session_on')) {
        $objSession = ($boostack->getConfig('csrf_on')) ? new Session_CSRF(): new Session_HTTP();
        $objSession->Impress();
        if ($boostack->getConfig('cookie_on') && isset($_COOKIE[''.$boostack->getConfig('cookie_name')])) {
            $c = Utils::sanitizeInput($_COOKIE[''.$boostack->getConfig('cookie_name')]); //user not logged in but remember-me cookie exists then try to perform loginByCookie function
            if (!$objSession->IsLoggedIn() && $c !== "")
                if (!$objSession->loginByCookie($c)) //cookie is set but wrong (manually edited)
                    Utils::goToLogout();
        }
        $CURRENTUSER = $objSession->GetUserObject();
    }
}

if ($boostack->getConfig('language_on'))
    require_once (ROOTPATH . "core/lib/check_language.lib.php");
if ($boostack->getConfig('mobile_on'))
    require_once (ROOTPATH . "core/lib/check_mobile.lib.php");

?>