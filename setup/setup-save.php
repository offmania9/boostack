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
$envPath = "/../core/env/";
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
        $finalSetupMessageError = "message: env/env.php -> failed to open stream: Permission denied. <br/><br/>Solution: add write access to 'env' folder";
    }
}

if ($env_parameters["database_on"]=="true" && $finalSetupMessageError=="") {
    try {
        require_once("../core/classes/Utils.Class.php");
        require_once("../core/classes/Boostack.Class.php");
        require_once("../core/classes/Database/Database_PDO.Class.php");
        require_once("../core/classes/BaseClass.Class.php");
        require_once("../core/classes/User.Class.php");
        require_once("../core/classes/User/User_Info.Class.php");
        require_once("../core/classes/User/User_Registration.Class.php");

        $db0 = new PDO('mysql:host=' . $env_parameters["db_host"] . ';dbname=' . $env_parameters["db_name"], $env_parameters["db_username"], $env_parameters["db_password"], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ));
        $db0->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db = Database_PDO::getInstance($env_parameters["db_host"], $env_parameters["db_name"], $env_parameters["db_username"], $env_parameters["db_password"]);
        $sql = file_get_contents('boostack_dump.sql');
        $qr = $db->exec($sql);

        $u = new User_Registration();
        $u->userInfoInstance->userInstance->username = "boostack";
        $u->userInfoInstance->userInstance->name = "Boostack System";
        $u->userInfoInstance->userInstance->email = "user@boostack.com";
        $u->userInfoInstance->userInstance->pwd = "testing";
        $u->userInfoInstance->userInstance->privilege = "0";
        $u->userInfoInstance->first_name = "Boostack";
        $u->userInfoInstance->company = "Boostack";
        $u->userInfoInstance->last_name = "System";
        $u->save();

        $u = new User_Registration();
        $u->userInfoInstance->userInstance->username = "boostackuser";
        $u->userInfoInstance->userInstance->name = "Boostack User";
        $u->userInfoInstance->userInstance->email = "user@boostack.com";
        $u->userInfoInstance->userInstance->pwd = "testing";
        $u->userInfoInstance->userInstance->privilege = "3";
        $u->userInfoInstance->userInstance->active = "1";
        $u->userInfoInstance->first_name = "Boostack";
        $u->userInfoInstance->company = "Boostack";
        $u->userInfoInstance->last_name = "User";
        $u->save();

        $u = new User_Registration();
        $u->userInfoInstance->userInstance->username = "boostackadmin";
        $u->userInfoInstance->userInstance->name = "Boostack Admin";
        $u->userInfoInstance->userInstance->email = "admin@boostack.com";
        $u->userInfoInstance->userInstance->pwd = "testing";
        $u->userInfoInstance->userInstance->privilege = "2";
        $u->userInfoInstance->userInstance->active = "1";
        $u->userInfoInstance->first_name = "Boostack";
        $u->userInfoInstance->company = "Boostack";
        $u->userInfoInstance->last_name = "Admin";
        $u->save();

        $u = new User_Registration();
        $u->userInfoInstance->userInstance->username = "boostacksuperadmin";
        $u->userInfoInstance->userInstance->name = "Boostack SuperAdmin";
        $u->userInfoInstance->userInstance->email = "superadmin@boostack.com";
        $u->userInfoInstance->userInstance->pwd = "testing";
        $u->userInfoInstance->userInstance->privilege = "1";
        $u->userInfoInstance->userInstance->active = "1";
        $u->userInfoInstance->first_name = "Boostack";
        $u->userInfoInstance->company = "Boostack";
        $u->userInfoInstance->last_name = "SuperAdmin";
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