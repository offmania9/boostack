<?php
$envPath = realpath(__DIR__."/../config/env/env.php");
if($envPath && is_file($envPath)) {
    require_once $envPath;
} else {
    header("Location: setup");
    echo "Choose an environment configuration file into '/core/env' folder (local.env.php, staging.env.php or production.env.php) and rename it into 'env.php'.";
    exit();
}
require_once (ROOTPATH . "config/env/global.env.php");
require_once (ROOTPATH . "core/classes/Utils.Class.php");
require_once (ROOTPATH . "core/libs/helpers.php");
spl_autoload_register('Utils::autoloadClass');
if ($config['developmentMode']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if (file_exists(ROOTPATH."setup/")) {
        $config['setupFolderExists'] = TRUE;
    }
}
else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
$boostack = Boostack::getInstance();
$CURRENTUSER = NULL;
if ($boostack->getConfig('database_on')){
    Database_PDO::getInstance($database['host'], $database['name'], $database['username'], $database['password']);
    if ($boostack->getConfig('session_on')) {
        $objSession = ($boostack->getConfig('csrf_on')) ? new Session_CSRF(): new Session_HTTP();
        if ($boostack->getConfig('cookie_on') && isset($_COOKIE[''.$boostack->getConfig('cookie_name')])) {
            //user not logged in but remember-me cookie exists then try to perform loginByCookie function
            $c = Utils::sanitizeInput($_COOKIE[''.$boostack->getConfig('cookie_name')]);
            if (!Auth::isLoggedIn() && $c !== "")
                if (!Auth::loginByCookie($c)) //cookie is set but wrong (manually edited)
                    Auth::logout();
        }
        $CURRENTUSER = Auth::getUserLoggedObject();

        /**
         *  Strong password check
         *  (when user log-in for the first time he needs to change password
         */
        if ($boostack->getConfig('forceStrongPassword')) {
            if(isset($CURRENTUSER->has_strong_password) && $CURRENTUSER->has_strong_password == "0" && (!$objSession->TwoFactor_Check || $objSession->TwoFactor_Check == 2)) {
                if ($objSession->password_to_change == 1) {
                    $objSession->password_to_change = 0;
                } else if ($objSession->password_to_change == 0) {
                    $objSession->password_to_change = 1;
                    if (!isset($_POST["passwordChange_POST"]))
                        Utils::goToUrl($boostack->getFriendlyUrl("password_change"));
                }
            }
        }

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