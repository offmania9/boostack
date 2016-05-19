<?php

$envPath = realpath(__DIR__."/env/env.php");
if($envPath && is_file($envPath)) {
    require_once $envPath;
} else {
    echo "Environment file not found";
    exit();
}

require_once (ROOTPATH . "core/env/global.env.php");
require_once (ROOTPATH . "core/lib/utilities.lib.php");

spl_autoload_register('autoloadClass');
$boostack = Boostack::getInstance();
if ($config['developmentMode']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
if ($config['database_on'])
    Database_PDO::getInstance($database['host'], $database['name'], $database['username'], $database['password']);
if ($config['session_on'] && $config['database_on']) {
    require_once (ROOTPATH . "core/lib/session.lib.php");
    $CURRENTUSER = $objSession->GetUserObject();
}

if ($config['language_on'])
    require_once (ROOTPATH . "core/lib/check_language.lib.php");
if ($config['mobile_on'])
    require_once (ROOTPATH . "core/lib/check_mobile.lib.php");

?>