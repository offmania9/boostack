<?php

// CONFIGURATION

$required_php_version = [
    "5.4.00",
    "50400",
];

$apache_modules_required = [
    "mod_rewrite",
    "mod_filter",

    "mod_deflate",
    "mod_headers",
];
$apache_modules_optional = [
    "mod_setenvif",
    "mod_mime",
    "mod_expires",
    "mod_autoindex",
    "mod_include",
];
$php_extensions_required = [
    "curl",
    "PDO",
    "json",
    "session",
];
$php_configurations_required = [
    "short_open_tag" => true,
];

// END CONFIGURATION






if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
$phpVersion = phpversion();
$apache_modules_loaded = apache_get_modules();
$php_extensions_loaded = get_loaded_extensions();
$php_configurations_loaded = ini_get_all();

$phpVersionResult = false;
if(PHP_VERSION_ID >= $required_php_version[1]) $phpVersionResult = true;

$apacheModulesRequiredTable = array();
foreach($apache_modules_required as $module){
    if(!in_array($module,$apache_modules_loaded)) {
        $apacheModulesRequiredTable[$module] = false;
    } else {
        $apacheModulesRequiredTable[$module] = true;
    }
}

$apacheModulesOptionalTable = array();
foreach($apache_modules_optional as $module){
    if(!in_array($module,$apache_modules_loaded)) {
        $apacheModulesOptionalTable[$module] = false;
    } else {
        $apacheModulesOptionalTable[$module] = true;
    }
}

$phpExtensionsRequiredTable = array();
foreach($php_extensions_required as $ext){
    if(!in_array($ext,$php_extensions_loaded)) {
        $phpExtensionsRequiredTable[$ext] = false;
    } else {
        $phpExtensionsRequiredTable[$ext] = true;
    }
}

$phpConfigurationTable = array();
foreach($php_configurations_required as $name => $value) {
    $systemConfig = ini_get($name);
    if ($systemConfig == $value) {
        $phpConfigurationTable[$name] = true;
    } else {
        $phpConfigurationTable[$name] = false;
    }
}

$errorMessage = !empty($_GET['message']) ? $_GET['message'] : "";

$init_rootpath = urlpath_calculation($_SERVER[REQUEST_URI]);
$init_url = urlpath_calculation($_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]);
$init_ip = ($_SERVER['SERVER_ADDR'] == "::1")?"127.0.0.1":$_SERVER['SERVER_ADDR'];
$isWritebleEnvFolder = is_writable_r(realpath(__DIR__.$envPath));

require_once "content_setup.phtml";

//-- FUNCTIONS
function is_writable_r($dir) {
    if (is_dir($dir)) {
        if(is_writable($dir)){
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (!is_writable_r($dir."/".$object)) return false;
                    else continue;
                }
            }
            return true;
        }else{
            return false;
        }

    }else if(file_exists($dir)){
        return (is_writable($dir));

    }
}

function urlpath_calculation($urlOrPath){
    $r = str_replace("/setup","",$urlOrPath);
    $r = (substr($r,-1)=="/")?$r:$r."/";
    return $r;
}

?>