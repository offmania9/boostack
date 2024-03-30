<?php
$envRelativePath = __DIR__ . "/../config/env/env.php";
$envPath = realpath($envRelativePath);
if ($envPath && is_file($envPath)) {
    require_once $envPath;
} else {
    if (is_dir("setup"))
        header("Location: setup");
    else
        echo "Rename 'config/env/sample.env.php' into 'env.php'";
    exit();
}
require_once(ROOTPATH . "../core/classes/Utils.Class.php");
spl_autoload_register('Utils::autoloadClass');
require_once(ROOTPATH . "../config/env/global.env.php");
require_once(ROOTPATH . "../core/libs/helpers.php");

Config::init();
if (Config::get('developmentMode')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
try {
    Request::init();
    $CURRENTUSER = NULL;
    if (Config::get('database_on')) {
        Database_PDO::getInstance(Config::get('db_host'), Config::get('db_name'), Config::get('db_username'), Config::get('db_password'), Config::get('db_port'));
        if (Config::get('session_on')) {
            $objSession = new Session_HTTP(Config::get('session_timeout'), Config::get('session_lifespan'));
            if (Config::get('cookie_on') && Request::hasCookieParam(Config::get('cookie_name')) && Request::getCookieParam(Config::get('cookie_name')) != NULL) {
                //user not logged in but remember-me cookie exists then try to perform loginByCookie function
                $c = Request::getCookieParam(Config::get('cookie_name'));
                if (!Auth::isLoggedIn() && $c !== "")
                    if (!Auth::loginByCookie($c)) //cookie is set but wrong (manually edited)
                        Auth::logout();
            }
            $CURRENTUSER = $objSession->GetUserObject(); // Auth::getUserLoggedObject();
        }
    }

    if (Config::get('language_on')) {
        $language = Language::init();
    }
} catch (Exception $e) {
    $short_message = "System error. See log files.";
    $message = $short_message . $e->getMessage() . $e->getTraceAsString() . "\n";
    Logger::write($message, Log_Level::ERROR, Log_Driver::FILE);
    if (Config::get("developmentMode")) {
        echo $message;
    } else {
        echo $short_message;
    }
    exit();
} catch (PDOException $e) {
    $short_message = "Database error. See log files.";
    $message = $short_message . $e->getMessage() . $e->getTraceAsString() . "\n";
    Logger::write($message, Log_Level::ERROR, Log_Driver::FILE);
    if (Config::get("developmentMode")) {
        echo $message;
    } else {
        echo $short_message;
    }
    exit();
}

require_once(ROOTPATH . "../custom/pre_content.php");
