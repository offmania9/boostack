<?php

namespace My;

use Core\Models\Config;
use Core\Models\Request;
use Core\Models\Auth;

class Controller extends \Core\Controllers\Controller
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
