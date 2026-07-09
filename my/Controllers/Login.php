<?php

declare(strict_types=1);

namespace My\Controllers;


use Boostack\Models\Config;
use Boostack\Models\Request;
use Boostack\Models\Auth;
use Boostack\Views\View;
use Boostack\Models\Language;
use My\Controllers\Exceptions\My_Exception;
#use My\Utils\PasskeyPromptHelper;

class Login extends \My\Controller
{
    public static function init(): void
    {
        try {
            parent::init();

            Config::constraint("session_on");

            $loginResult = self::handleLoginAttempt();

            if (Auth::isLoggedIn()) {
                self::renderLoggedInView();
            } else {
                self::renderLoginFormView($loginResult['error'], $loginResult['code']);
            }
        } catch (\Throwable $e) {
            My_Exception::handle($e);
        }
    }

    /**
     * Gestisce il tentativo di login e restituisce eventuali errori.
     */
    private static function handleLoginAttempt(): array
    {
        $errorMessage = '';
        $errorCode = null;

        if (Request::hasPostParam("btk_usr") && Request::hasPostParam("btk_pwd")) {
            $user = Request::getPostParam("btk_usr");
            $password = Request::getPostParam("btk_pwd");
            $rememberMe = Config::get('cookie_on') &&
                Request::hasPostParam("rememberme") &&
                Request::getPostParam("rememberme") === '1';

            $result = Auth::loginByUsernameAndPlainPassword($user, $password, $rememberMe);

            if ($result->hasError()) {
                $errorMessage = $result->error;
                $errorCode = $result->code;
            } elseif (Auth::isLoggedIn()) {
                $userObject = Auth::getUserLoggedObject();
                #PasskeyPromptHelper::syncForUser($userObject);
            }
        }
        return [
            'error' => $errorMessage,
            'code' => $errorCode
        ];
    }

    private static function renderLoggedInView(): void
    {
        View::render("login_logged.phtml", [
            "canonical" => Request::getFriendlyUrl("home"),
            "pageTitle" => Language::getLabel("navigation.home"),
        ]);
    }

    private static function renderLoginFormView(?string $errorMessage, ?int $errorCode): void
    {
        View::render("login.phtml", [
            "canonical" => Request::getFriendlyUrl("login"),
            "pageTitle" => Language::getLabel("navigation.login"),
            "errorMessage" => $errorMessage,
            "errorCode" => $errorCode,
        ]);
    }
}
