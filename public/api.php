<?php 
require_once "../core/environment_init.php";

if (! array_key_exists('HTTP_ORIGIN', Request::getServerArray()))
    $_SERVER['HTTP_ORIGIN'] = Request::getServerParam("SERVER_NAME");

try{
    $api = Request::hasRequestParam('request')? new Rest_Api(Request::getRequestParam('request')) : new Rest_Api("");
    echo $api->processAPI();
}
catch (Exception $e) {
    echo $e->getMessage();
}

?>
