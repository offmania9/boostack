<?php
$envPath = realpath(__DIR__ . "/../config/env/env.php");
if ($envPath && is_file($envPath)) {
    require_once $envPath;
} else {
    header("Location: setup");
    echo "Choose an environment configuration file into '/core/env' folder (local.env.php, staging.env.php or production.env.php) and rename it into 'env.php'.";
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
$boostack = Boostack::getInstance();
$CURRENTUSER = NULL;
$request = Request::getInstance();
if ($boostack->getConfig('database_on')) {
    Database_PDO::getInstance($database['host'], $database['name'], $database['username'], $database['password']);
    if ($boostack->getConfig('session_on')) {
        $objSession = ($boostack->getConfig('csrf_on')) ? new Session_CSRF() : new Session_HTTP();
        if ($boostack->getConfig('cookie_on') && $request->getCookieParam($boostack->getConfig('cookie_name')) != NULL) {
            //user not logged in but remember-me cookie exists then try to perform loginByCookie function
            $c = $request->getCookieParam($boostack->getConfig('cookie_name'));
            if (!Auth::isLoggedIn() && $c !== "")
                if (!Auth::loginByCookie($c)) //cookie is set but wrong (manually edited)
                    Auth::logout();
        }
        $CURRENTUSER = Auth::getUserLoggedObject();

    }
}

if ($boostack->getConfig('language_on')) {
    $language = Language::getLanguage();
    $languageFile = Language::findLanguageFile($language);
    $translatedLabels = Language::readAndDecodeLanguageFile($languageFile);
    Language::setSessionLanguage($language);
    $boostack->labels = $translatedLabels;
}
if ($boostack->getConfig('mobile_on')) {
    $detect = new Mobile_Detect();
    if ($detect->isMobile()) {
        header("location: " . $boostack->getConfig("mobile_url"));
        exit();
    }
}

?>