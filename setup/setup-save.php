<?php

$input = $_POST;
$error = false;
$message = "";

if(empty($input['rootpath'])) {
    $error = true;
    $message .= "Missing rootpath <br/>";
}
if(empty($input['url'])) {
    $error = true;
    $message .= "Missing URL <br/>";
}
if(empty($input['db-active'])) {
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
}

$env_parameters = [
    "current_environment" => "local",
    "rootpath" => $input['rootpath'],
    "url" => rtrim($input['url'],"/").'/',
    "database_on" => $input['db-active'],
    "db_host" => $input['db-host'],
    "db_name" => $input['db-name'],
    "db_username" => $input['db-username'],
    "db_password" => $input['db-password'],
    "session_on" => $input['session-active'],
];

$exampleEnvName = "sample.env.php";
$outputEnvName = "env.php";
$envPath = "/../core/env/";
$exampleEnvPath = realpath(__DIR__.$envPath.$exampleEnvName);
$finalEnvPath = realpath(__DIR__.$envPath)."/".$outputEnvName;


$envContent = file_get_contents($exampleEnvPath);


foreach ($env_parameters as $param => $value){
    $envContent = str_replace("[$param]", $value, $envContent);
}

file_put_contents($finalEnvPath, $envContent);

header("Location: http://".$env_parameters['url']);

// CREAZIONE DB