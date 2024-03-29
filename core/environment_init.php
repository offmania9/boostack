<?php
$envRelativePath = __DIR__ . "/../config/env/env.php";
$envPath = realpath($envRelativePath);
if ($envPath && is_file($envPath)) {
    require_once $envPath;
} else {
    if(is_dir(ROOTPATH."setup"))
        header("Location: setup"); 
    else
        echo "Rename 'config/env/sample.env.php' into 'env.php'";
    exit();
}
require_once(ROOTPATH . "../config/env/global.env.php");
require_once(ROOTPATH . "../core/classes/Utils.Class.php");
require_once(ROOTPATH . "../core/libs/helpers.php");
#require_once(ROOTPATH .'../vendor/autoload.php');
spl_autoload_register('Utils::autoloadClass');
Config::init();
if (Config::get('developmentMode')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
Request::init();
$CURRENTUSER = NULL;
if (Config::get('database_on')) {
    Database_PDO::getInstance(Config::get('db_host'), Config::get('db_name'), Config::get('db_username'), Config::get('db_password'), Config::get('db_port'));
    if (Config::get('session_on')) {
        $objSession = new Session_HTTP(Config::get('session_timeout'), Config::get('session_lifespan'));
        #$objSession->loginUser(1);
        if (Config::get('cookie_on') && Request::hasCookieParam(Config::get('cookie_name')) && Request::getCookieParam(Config::get('cookie_name')) != NULL) {
            //user not logged in but remember-me cookie exists then try to perform loginByCookie function
            $c = Request::getCookieParam(Config::get('cookie_name')); 
            if (!Auth::isLoggedIn() && $c !== "")
                if (!Auth::loginByCookie($c)) //cookie is set but wrong (manually edited)
                    Auth::logout();
        }
        $CURRENTUSER = $objSession->GetUserObject();// Auth::getUserLoggedObject();
    }
}

if (Config::get('language_on')) {
    $language = Language::init();
}

require_once(ROOTPATH . "../custom/pre_content.php");
?>