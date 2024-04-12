<?php

namespace My;

use Boostack\Models\Config;
use Boostack\Models\Request;
use Boostack\Models\Auth;

class Controller extends \Boostack\Controllers\Controller
{
    public static function init()
    {
        parent::init();
        self::checkCookieForAutoLogin();
    }

    private static function checkCookieForAutoLogin() {
        if (Config::get('cookie_on') && Request::hasCookieParam(Config::get('cookie_name')) && Request::getCookieParam(Config::get('cookie_name')) != NULL) {
            //user not logged in but remember-me cookie exists then try to perform loginByCookie function
            $c = Request::getCookieParam(Config::get('cookie_name'));
            if (!Auth::isLoggedIn() && $c !== "")
                if (!Auth::loginByCookie($c)) //cookie is set but wrong (manually edited)
                    Auth::logout();
        }
    }
}
