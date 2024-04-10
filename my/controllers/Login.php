<?php

namespace My\Controllers;

use Core\Models\Config;
use Core\Models\Request;
use Core\Models\Auth;
use Core\Views\View;
use Core\Models\Language;

class Login extends \My\Controller
{
    public static function init()
    {
        parent::init();
        $errorMessage = "";
        $errorCode = null;
        try {
            Config::constraint("session_on");
            if (Request::hasPostParam("btk_usr") && Request::hasPostParam("btk_pwd")) {
                $user = Request::getPostParam("btk_usr");
                $password = Request::getPostParam("btk_pwd");
                $rememberMe = (Config::get('cookie_on') && Request::hasPostParam("rememberme") && Request::getPostParam("rememberme") == '1') ? true : false;
                $loginResult = Auth::loginByUsernameAndPlainPassword($user, $password, $rememberMe);
                if ($loginResult->hasError()) {
                    $errorMessage = $loginResult->error;
                    $errorCode = $loginResult->code;
                }
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }
        if (Auth::isLoggedIn()) {
            View::render("login_logged.phtml", array(
                "canonical" =>  Request::getFriendlyUrl("home"),
                "pageTitle" => Language::getLabel("navigation.home"),
            ));
        } else {
            View::render("login.phtml", array(
                "canonical" =>  Request::getFriendlyUrl("login"),
                "pageTitle" => Language::getLabel("navigation.login"),
                "errorMessage" => $errorMessage,
                "errorCode" => $errorCode
            ));
        }
    }
}
