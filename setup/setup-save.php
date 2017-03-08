<?php

$input = $_POST;
$error = FALSE;
$finalSetupMessageError = "";

if (empty($input['rootpath'])) {
    $error = TRUE;
    $finalSetupMessageError .= "Missing rootpath <br/>";
}
if (empty($input['url'])) {
    $error = TRUE;
    $finalSetupMessageError .= "Missing URL <br/>";
}
$env_parameters = [
    "current_environment" => "local",
    "rootpath" => $input['rootpath'],
    "url" => rtrim($input['url'], "/") . '/',
    "database_on" => $input['db-active'],
    "db_host" => $input['db-host'],
    "db_name" => $input['db-name'],
    "db_username" => $input['db-username'],
    "db_password" => $input['db-password'],
    "session_on" => $input['db-session-active'],
    "cookie_on" => $input['db-cookie-active'],
    "cookie_expire" => $input['db-cookie-expired'],
    "cookie_name" => $input['db-cookie-name'],
    "log_on" => $input['db-log-active']
];

$exampleEnvName = "sample.env.php";
$outputEnvName = "env.php";
$envPath = "/../config/env/";
$exampleEnvPath = realpath($exampleEnvName);
$finalEnvPath = realpath(__DIR__ . $envPath) . "/" . $outputEnvName;

$envContent = @file_get_contents($exampleEnvPath);
if ($envContent === FALSE) {
    $finalSetupMessageError = "message: setup/sample.env.php -> failed to open stream: Permission denied. <br/><br/>Solution: add read access to 'setup' folder";
} else {
    foreach ($env_parameters as $param => $value) {
        $value = ($value == "true" || $value == "false") ? strtoupper($value) : $value;
        $envContent = str_replace("[$param]", $value, $envContent);
    }
    $old = umask(0);
    if (@file_put_contents($finalEnvPath, $envContent) === FALSE) {
        $finalSetupMessageError = "message: config/env/env.php -> failed to open stream: Permission denied. <br/><br/>Solution: add write access to 'config/env' folder";
    }
}

if ($env_parameters["database_on"]=="true" && $finalSetupMessageError=="") {
    try {
        require_once("../config/env/env.php");

        require_once("../core/classes/Utils.Class.php");
        require_once("../core/classes/Boostack.Class.php");
        require_once("../core/classes/Exception/Exception_Misconfiguration.Class.php");
        require_once("../core/classes/Config.Class.php");
        require_once("../core/classes/Database/Database_PDO.Class.php");
        require_once("../core/classes/BaseClass.Class.php");
        require_once("../core/classes/User.Class.php");
        require_once("../core/classes/User/User_Entity.Class.php");
        require_once("../core/classes/User/User_Info.Class.php");
        require_once("../core/classes/User/User_Registration.Class.php");
        require_once("../core/classes/User/User_Social.Class.php");
        require_once("../core/classes/LogLevel.Class.php");
        require_once("../core/classes/FileLogger.Class.php");

        Config::initConfig();
        $db0 = new PDO('mysql:host=' . $env_parameters["db_host"] . ';dbname=' . $env_parameters["db_name"], $env_parameters["db_username"], $env_parameters["db_password"], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ));
        $db0->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db = Database_PDO::getInstance($env_parameters["db_host"], $env_parameters["db_name"], $env_parameters["db_username"], $env_parameters["db_password"]);
        $sql = file_get_contents('boostack_dump.sql');
        $qr = $db->exec($sql);

        $u = new User();
        $u->username = "boostack";
        $u->name = "Boostack System";
        $u->full_name = "Boostack System";
        $u->email = "user@boostack.com";
        $u->pwd = "testing";
        $u->privilege = "0";
        $u->first_name = "Boostack";
        $u->company = "Boostack";
        $u->last_name = "System";
        $u->save();

        $u = new User();
        $u->username = "boostackuser";
        $u->name = "Boostack User";
        $u->full_name = "Boostack User";
        $u->email = "user@boostack.com";
        $u->pwd = "testing";
        $u->privilege = "3";
        $u->active = "1";
        $u->first_name = "Boostack";
        $u->company = "Boostack";
        $u->last_name = "User";
        $u->save();

        $u = new User();
        $u->username = "boostackadmin";
        $u->name = "Boostack Admin";
        $u->full_name = "Boostack Admin";
        $u->email = "admin@boostack.com";
        $u->pwd = "testing";
        $u->privilege = "2";
        $u->active = "1";
        $u->first_name = "Boostack";
        $u->company = "Boostack";
        $u->last_name = "Admin";
        $u->save();

        $u = new User();
        $u->username = "boostacksuperadmin";
        $u->name = "Boostack SuperAdmin";
        $u->full_name = "Boostack SuperAdmin";
        $u->email = "superadmin@boostack.com";
        $u->pwd = "testing";
        $u->privilege = "1";
        $u->active = "1";
        $u->first_name = "Boostack";
        $u->company = "Boostack";
        $u->last_name = "SuperAdmin";
        $u->save();

    } catch (PDOException $e) {
        $finalSetupMessageError = "Database Error. Message: " . $e->getMessage();
        unlink($finalEnvPath);
    }
     catch (Exception $e2) {
        $finalSetupMessageError = "Error. Message: " . $e2->getMessage();
        unlink($finalEnvPath);
    }
}

require_once "content_setup.phtml";
?>