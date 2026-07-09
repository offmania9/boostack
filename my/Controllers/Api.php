<?php

declare(strict_types=1);

namespace My\Controllers;

use Boostack\Models\Log\Log_Level;
use Boostack\Models\Log\Logger;
use Boostack\Models\Request;
use Boostack\Models\Rest\Rest_Api;
use My\Controllers\Exceptions\My_Exception;

class Api extends \My\Controller
{
    public static function init(): void
    {
        parent::init();

        Logger::write([
            'message' => 'api_call',
            'context' => [
                'request' => (string)Request::getQueryParam('request', ''),
                'method' => self::resolveRequestMethod(),
            ],
        ], Log_Level::USER);

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

        if (!array_key_exists('HTTP_ORIGIN', Request::getServerArray())) {
            $_SERVER['HTTP_ORIGIN'] = Request::getServerParam("SERVER_NAME");
        }
        try {
            $api = Request::getQueryParam('request') ? new Rest_Api(Request::getQueryParam('request')) : new Rest_Api("");
            echo $api->processAPI();
        } catch (\Exception $e) {
            My_Exception::handle($e);
            echo $e->getMessage();
        }
    }

    private static function resolveRequestMethod(): string
    {
        $requestMethod = Request::getMethod();
        if (is_object($requestMethod) && property_exists($requestMethod, 'value')) {
            return strtoupper((string)$requestMethod->value);
        }

        return strtoupper((string)$requestMethod);
    }
}
