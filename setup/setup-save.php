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

if ($env_parameters["database_on"] && $finalSetupMessageError=="") {
    try {
        require_once("../core/class/Utils.Class.php");
        require_once("../core/class/Boostack.Class.php");
        require_once("../core/class/Database/Database_PDO.Class.php");
        require_once("../core/class/User.Class.php");
        require_once("../core/class/User/User_Info.Class.php");
        require_once("../core/class/User/User_Registration.Class.php");

        $db0 = new PDO('mysql:host=' . $env_parameters["db_host"] . ';dbname=' . $env_parameters["db_name"], $env_parameters["db_username"], $env_parameters["db_password"], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ));
        $db0->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $db = Database_PDO::getInstance($env_parameters["db_host"], $env_parameters["db_name"], $env_parameters["db_username"], $env_parameters["db_password"]);
        $sql = file_get_contents('boostack_dump.sql');
        $qr = $db->exec($sql);

        $u = new User_Registration();
        $arr["username"] = "boostack";
        $arr["name"] = "Boostack System";
        $arr["first_name"] = "Boostack";
        $arr["company"] = "Boostack";
        $arr["last_name"] = "System";
        $arr["email"] = "user@boostack.com";
        $arr["pwd"] = "testing";
        $arr["privilege"] = "0";
        $u->insert($arr);

        $u = new User_Registration();
        $arr["username"] = "boostackuser";
        $arr["name"] = "Boostack User";
        $arr["first_name"] = "Boostack";
        $arr["company"] = "Boostack";
        $arr["last_name"] = "User";
        $arr["email"] = "user@boostack.com";
        $arr["pwd"] = "testing";
        $arr["privilege"] = "3";
        $arr["active"] = "1";
        $u->insert($arr);

        $u = new User_Registration();
        $arr["username"] = "boostackadmin";
        $arr["name"] = "Boostack Admin";
        $arr["first_name"] = "Boostack";
        $arr["company"] = "Boostack";
        $arr["last_name"] = "Admin";
        $arr["email"] = "admin@boostack.com";
        $arr["pwd"] = "testing";
        $arr["privilege"] = "2";
        $arr["active"] = "1";
        $u->insert($arr);

        $u = new User_Registration();
        $arr["username"] = "boostacksuperadmin";
        $arr["name"] = "Boostack SuperAdmin";
        $arr["first_name"] = "Boostack";
        $arr["company"] = "Boostack";
        $arr["last_name"] = "SuperAdmin";
        $arr["email"] = "superadmin@boostack.com";
        $arr["pwd"] = "testing";
        $arr["privilege"] = "1";
        $arr["active"] = "1";
        $u->insert($arr);

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