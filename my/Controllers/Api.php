<?php

namespace My\Controllers;

use Boostack\Models\Request;
use Boostack\Models\Rest\Rest_Api;

class Api extends \My\Controller
{
    public static function init()
    {
        parent::init();
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
    }
}
