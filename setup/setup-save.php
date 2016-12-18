<?php

$input = $_POST;
$error = FALSE;
$finalSetupMessageError = "";

if(empty($input['rootpath'])) {
    $error = TRUE;
    $finalSetupMessageError .= "Missing rootpath <br/>";
}
if(empty($input['url'])) {
    $error = TRUE;
    $finalSetupMessageError .= "Missing URL <br/>";
}
/*if(empty($input['db-active'])) {
    $error = true;
    $message .= "Missing DB-active <br/>";
}
if(empty($input['db-host'])) {
    $error = true;
    $message .= "Missing DB-host <br/>";
}
if(empty($input['db-name'])) {
    $error = true;
    $message .= "Missing DB-name <br/>";
}
if(empty($input['db-username'])) {
    $error = true;
    $message .= "Missing DB-username <br/>";
}
if(empty($input['session-active'])) {
    $error = true;
    $message .= "Missing Session-active <br/>";
}

if($error) {
    header("Location: ?message=".$message);
    exit();
}
*/
$env_parameters = [
    "current_environment" => "local",
    "rootpath" => $input['rootpath'],
    "url" => rtrim($input['url'],"/").'/',
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
$finalEnvPath = realpath(__DIR__.$envPath)."/".$outputEnvName;

$envContent = @file_get_contents($exampleEnvPath);
if($envContent === FALSE){
    $finalSetupMessageError =  "message: setup/sample.env.php -> failed to open stream: Permission denied. <br/><br/>Solution: add read access to 'setup' folder";
}
else{
    foreach ($env_parameters as $param => $value){
        $value = ($value == "true" || $value == "false")?strtoupper($value):$value;
        $envContent = str_replace("[$param]", $value, $envContent);
    }
    if(@file_put_contents($finalEnvPath, $envContent) === FALSE) {
        $finalSetupMessageError =  "message: env/env.php -> failed to open stream: Permission denied. <br/><br/>Solution: add write access to 'env' folder";
    }
}


// CREAZIONE DB

require_once "content_setup.phtml";
?>