<?php
$envPath = realpath(__DIR__ . "/../config/env/env.php");
if ($envPath && is_file($envPath)) {
    require_once $envPath;
} else {
    header("Location: setup"); //echo "Rename 'config/env/sample.env.php' into 'env.php'";
    exit();
}
require_once(ROOTPATH . "config/env/global.env.php");
require_once(ROOTPATH . "core/classes/Utils.Class.php");
require_once(ROOTPATH . "core/libs/helpers.php");
spl_autoload_register('Utils::autoloadClass');
if ($config['developmentMode']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if (file_exists(ROOTPATH . "setup/")) {
        $config['setupFolderExists'] = TRUE;
    }
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
Config::initConfig();
Request::init();

// TODO: delete?
if (Config::get('developmentMode')) {
    if (! ini_get('zlib.output_compression') && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && ! in_array('ob_gzhandler', ob_list_handlers()))
        ob_start("ob_gzhandler");
    else
        ob_start();
}

$CURRENTUSER = NULL;
if (Config::get('database_on')) {
    Database_PDO::getInstance($database['host'], $database['name'], $database['username'], $database['password']);
    if (Config::get('session_on')) {
        $objSession = (Config::get('csrf_on')) ? new Session_CSRF(Config::get('session_timeout'), Config::get('session_lifespan')) : new Session_HTTP(Config::get('session_timeout'), Config::get('session_lifespan'));
        if (Config::get('cookie_on') && Request::hasCookieParam(Config::get('cookie_name')) && Request::getCookieParam(Config::get('cookie_name')) != NULL) {
            //user not logged in but remember-me cookie exists then try to perform loginByCookie function
            $c = Request::getCookieParam(Config::get('cookie_name'));
            if (!Auth::isLoggedIn() && $c !== "")
                if (!Auth::loginByCookie($c)) //cookie is set but wrong (manually edited)
                    Auth::logout();
        }
        $CURRENTUSER = Auth::getUserLoggedObject();

    }
}

if (Config::get('language_on')) {
    Language::init();
}
if (Config::get('mobile_on')) {
    $detect = new Mobile_Detect();
    if ($detect->isMobile()) {
        header("location: " . Config::get("mobile_url"));
        exit();
    }
}

require_once(ROOTPATH . "startupCustom.php");

?>