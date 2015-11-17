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
define('CURRENT_ENVIRONMENT', "local"); // [local] | [staging] | [production] | {[create custom env]}
                                        
// ====== DO NOT EDIT BELOW THIS LINE
require_once ("env/" . CURRENT_ENVIRONMENT . ".env.php");
require_once ("env/global.env.php"); // import global environment
spl_autoload_register('autoloadClass');
define('ROOTPATH', $config['path']);
$boostack = Boostack::getInstance(); // define('BOOSTACK',$boostack);
if ($config['developmentMode']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require_once (ROOTPATH . "lib/utilities.lib.php");
if ($config['database_on'])
    $pdo = Database_PDO::getInstance($database['host'], $database['name'], $database['username'], $database['password']);
if ($config['session_on'] && $config['database_on']) {
    require_once (ROOTPATH . "lib/session.lib.php");
    define('CURRENTUSER', $objSession->GetUserObject());
}
if ($config['language_on'])
    require_once (ROOTPATH . "lib/check_language.lib.php");
if ($config['checkMobile'])
    require_once (ROOTPATH . "lib/check_mobile.lib.php");
    
    // AUTOLOAD
function autoloadClass($className)
{
    $cn = explode("_", $className);
    $filename = ROOTPATH . "class/";
    $cnt = count($cn);
    if ($cnt == 1)
        $filename .= $className . ".Class.php";
    else {
        $i = 0;
        for ($i; $i < $cnt - 1; $i ++)
            $filename .= $cn[$i] . "/";
        $filename .= $className . ".Class.php";
    }
    if (is_readable($filename))
        require_once ($filename);
}
?>