<?php

namespace My\Controllers;

use Boostack\Models\Config;
use Boostack\Models\Request;
use Boostack\Models\Auth;
use Boostack\Views\View;
use Boostack\Models\Language;

class Registration extends \My\Controller
{
    public static function init()
    {
        parent::init();
        $registrationError = "";
        try {
            Config::constraint("session_on");
            if (Request::hasPostParam('reg-email') && Request::hasPostParam('reg-pwd1') && Request::hasPostParam('reg-pwd2')) {
                $email = Request::getPostParam('reg-email');
                $psw1 = Request::getPostParam('reg-pwd1');
                $psw2 = Request::getPostParam('reg-pwd2');
                $csrfToken = null;
                if (Config::get('csrf_on')) {
                    $csrfToken = Request::getPostParam('BCSRFT');
                }
                Auth::registration($email, $email, $psw1, $psw2, $csrfToken);
            }
        } catch (\Boostack\Exception\Exception_Misconfiguration $em) {
            dd($em->getMessage());
        } catch (\Boostack\Exception\Exception_Registration $e) {
            $registrationError = $e->getMessage();
        } catch (\Exception $e) {
            $registrationError = $e->getMessage();
        }

        if (Auth::isLoggedIn()) {
            View::render("login_logged.phtml", array(
                "canonical" =>  Request::getFriendlyUrl("home"),
                "pageTitle" => Language::getLabel("navigation.home"),
            ));
        } else {
            View::render("registration.phtml", array(
                "canonical" =>  Request::getFriendlyUrl("registration"),
                "pageTitle" => Language::getLabel("navigation.registration"),
                "registrationError" => $registrationError
            ));
        }
    }
}
