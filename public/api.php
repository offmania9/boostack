<?php
require __DIR__ . '/../vendor/autoload.php';
Core\Environment::init();
/**
 * Boostack: api.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

use Core\Models\Request;
use Core\Models\Rest\Rest_Api;
/*
* JWT TOKEN Usage
*
* $user = [new User or Current user];
* $tokenObj = $user->createJWTToken();
* $tokenObj->revoke();
* $tokenObj->delete();
*
* $token_list = new User_ApiJWTTokenList();
* $my_tokens = $token_list->getMy(); // if logged
* $my_tokens = $token_list->getByUser([id_user]);
* $token_list->revokeAll();
*/

if (!array_key_exists('HTTP_ORIGIN', Request::getServerArray()))
    $_SERVER['HTTP_ORIGIN'] = Request::getServerParam("SERVER_NAME");
try {
    $api = Request::hasRequestParam('request') ? new Rest_Api(Request::getRequestParam('request')) : new Rest_Api("");
    echo $api->processAPI();
} catch (\Exception $e) {
    echo $e->getMessage();
}
