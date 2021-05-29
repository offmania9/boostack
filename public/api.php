<?php 
require_once "../core/environment_init.php";

if (! array_key_exists('HTTP_ORIGIN', Request::getServerArray()))
    $_SERVER['HTTP_ORIGIN'] = Request::getServerParam("SERVER_NAME");

$api = Request::hasRequestParam('request')? new Rest_CustomApi(Request::getRequestParam('request')) : new Rest_Api("");
echo $api->processAPI();
?>