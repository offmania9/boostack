<?
/**
 * Boostack: environment_init.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
 */

// ====== CHOOSE THE ENVIRONMENT ======
define('CURRENT_ENVIRONMENT', "staging"); // [local] | [staging] | [production] | {[create custom env]}
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . "/boostack/");
                                        
// ====== DO NOT EDIT BELOW THIS LINE
require_once (ROOTPATH . "core/env/" . CURRENT_ENVIRONMENT . ".env.php");
require_once (ROOTPATH . "core/env/global.env.php"); // import global environment
require_once (ROOTPATH . "core/lib/utilities.lib.php");

spl_autoload_register('autoloadClass');
$boostack = Boostack::getInstance(); //define('BOOSTACK',Boostack::getInstance());
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