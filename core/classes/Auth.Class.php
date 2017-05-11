<?php
/**
 * Boostack: Auth.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 3.0
 */
class Auth
{

    /**
     *
     */
    const LOCK_TIMER = -1;
    /**
     *
     */
    const LOCK_RECAPTCHA = -2;

    /*
     * Esegue il login dell'utente con username e password in chiaro
     */
    /**
     * @param $username
     * @param $password
     * @param bool $cookieRememberMe
     * @return MessageBag
     */
    public static function loginByUsernameAndPlainPassword($username, $password, $cookieRememberMe = false)
    {
        global $boostack,$objSession;
        $result = new MessageBag();
        $isLockStrategyEnabled = Config::get("lockStrategy_on");
        $lockStrategy = Config::get("login_lockStrategy");

        try {
            if(!Utils::checkAcceptedTimeFromLastRequest(self::getLastTry()))
                throw new Exception("Too much request. Wait some seconds");
            if(Auth::isLoggedIn())
                return $result;

            /** LOCK STRATEGY CHECK **/
            if($isLockStrategyEnabled) {
                if(!$objSession->failed_login_count) $objSession->failed_login_count = 0;
                if($objSession->failed_login_count >= Config::get("login_maxAttempts")) {
                    if($lockStrategy == "timer") {
                        if(!self::checkAcceptedTimeFromLastLogin(self::getLastTry())) throw new Exception("Too much login request. Wait some seconds", self::LOCK_TIMER);
                    } else if($lockStrategy == "recaptcha") {
                        $recaptchaFormData = isset($_POST["g-recaptcha-response"]) ? $_POST["g-recaptcha-response"] : null;
                        if(empty($recaptchaFormData)) throw new Exception("Missing recaptcha data", self::LOCK_RECAPTCHA);
                        $recaptchaResponse = self::reCaptchaVerify($boostack, $_POST["g-recaptcha-response"]);
                        if(!$recaptchaResponse) throw new Exception("Invalid reCaptcha", self::LOCK_RECAPTCHA);
                    }
                    $objSession->failed_login_count = 0;
                }
            }
            if(!Validator::username($username))
                throw new Exception("Username format not valid");
            if(!Validator::password($password))
                throw new Exception("Password format not valid");
            Auth::impressLastTry();
            Utils::checkStringFormat($password);
            if($isLockStrategyEnabled || ($isLockStrategyEnabled && Config::get('csrf_on') && $objSession->CSRFCheckValidity(Request::getPostArray(),false))) $objSession->failed_login_count++;
            Auth::checkAndLogin($username, $password, $cookieRememberMe, true);
            if($isLockStrategyEnabled) $objSession->failed_login_count = 0;

        } catch (Exception $e) {
            Logger::write($e,Logger::LEVEL_USER);
            $result->setError($e->getMessage());
            $result->setCode($e->getCode());
        }

        return $result;
    }

    /*
     * Esegue il login dell'utente con userID passato come parametero
     */
    /**
     * @param $userID
     */
    public static function loginByUserID($userID)
    {
        if(!Auth::isLoggedIn()) {
            $user = new User($userID);
            self::login($user->username, "", $user->pwd);
        }
    }

    /*  Esegue il login dell'utente con il "remember-me cookie"
     *
     *  @param $cookieValue valore del cookie
     */
    /**
     * @param $cookieValue
     * @return bool
     */
    public static function loginByCookie($cookieValue)
    {
        global $objSession, $boostack;
        try {
            $userCredentials = User::getCredentialByCookie($cookieValue);
            if($userCredentials != false) {
                if (Utils::checkCookieHashValidity($cookieValue)) {
                    self::login($userCredentials["username"],"",$userCredentials["pwd"]);
                    $userObject = $objSession->GetUserObject();
                    $userObject->refreshRememberMeCookie();
                    return true;
                } else {
                    Logger::write("checkCookieHashValidity(" . $cookieValue . "): false - IP:" . Utils::getIpAddress(),Logger::LEVEL_USER);
                }
            }
        } catch (PDOException $e) {
            Logger::write($e, Logger::LEVEL_ERROR);
        } catch (Exception $e) {
            Logger::write($e, Logger::LEVEL_ERROR);
        }
        return false;
    }

    /**
     * @return mixed
     */
    public static function isLoggedIn()
    {
        global $objSession;
        return $objSession->IsLoggedIn();
    }

    /**
     * @return bool
     */
    public static function logout()
    {
        global $objSession, $boostack;
        try {
            if(self::isLoggedIn()) {
                if (Config::get("session_on") && isset($objSession) && self::isLoggedIn()){
                    Logger::write("[Logout] uid: ".$objSession->GetUserID(),Logger::LEVEL_USER);
                    $objSession->logoutUser();
                }
                if (Config::get("cookie_on")) {
                    $cookieName = Config::get("cookie_name");
                    $cookieExpire = Config::get("cookie_expire");
                    setcookie('' . $cookieName, false, time() - $cookieExpire);
                    setcookie('' . $cookieName, false, time() - $cookieExpire, "/");
                }
                return true;
            }
        } catch (PDOException $e) {
            Logger::write($e, Logger::LEVEL_ERROR);
        } catch (Exception $e) {
            Logger::write($e, Logger::LEVEL_ERROR);
        }
        return false;
    }

    /**
     * @return mixed
     */
    public static function getLastTry()
    {
        global $objSession;
        return $objSession->LastTryLogin;
    }

    /**
     *
     */
    public static function impressLastTry()
    {
        global $objSession;
        $objSession->LastTryLogin = time();
    }

    /**
     * @return mixed
     */
    public static function getUserLoggedObject()
    {
        global $objSession;
        return $objSession->GetUserObject();
    }

    /**
     * @return bool
     */
    public static function isTimerLocked()
    {
        global $objSession;
        return Config::get("lockStrategy_on") && Config::get("login_lockStrategy") == "timer" && $objSession->failed_login_count >= Config::get("login_maxAttempts") && !self::checkAcceptedTimeFromLastLogin(self::getLastTry());
    }

    /**
     * @return bool
     */
    public static function haveToShowCaptcha()
    {
        global $objSession;
        return Config::get("lockStrategy_on") && Config::get("login_lockStrategy") == "recaptcha" && $objSession->failed_login_count >= Config::get("login_maxAttempts");

    }

    /**
     * @param $username
     * @param $password
     * @param $cookieRememberMe
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    private static function checkAndLogin($username, $password, $cookieRememberMe, $throwException = true)
    {
        global $objSession, $boostack;
        if (Config::get("userToLogin") == "email") {
            if (!User::existsByEmail($username)) {
                Logger::write("Auth -> checkAndLogin: User doesn't exist by Email Address", Logger::LEVEL_USER);
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        if (Config::get("userToLogin") == "username") {
            if (!User::existsByUsername($username)) {
                Logger::write("Auth -> checkAndLogin: User doesn't exist by Username", Logger::LEVEL_USER);
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        if(Config::get("userToLogin") == "both") {
            if(!User::existsByEmail($username,false) && !User::existsByUsername($username,false)) {
                Logger::write("Auth -> tryLogin: User doesn't exist by Username and by email", Logger::LEVEL_USER);
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        self::logout();
        self::login($username, $password);

        if (!self::isLoggedIn()){
            Logger::write("Auth -> checkAndLogin: Username or password not valid.",Logger::LEVEL_USER);
            if ($throwException)
                throw new Exception("Username or password not valid.",5);
            return false;
        }

        if ($cookieRememberMe) {
            $user = $objSession->GetUserObject();
            $user->refreshRememberMeCookie();
        }
        Logger::write("[Login] uid: ".$objSession->GetUserID(),Logger::LEVEL_USER);
        return true;
    }

    /**
     * @param $strUsername
     * @param $strPlainPassword
     * @param string $hashedPassword
     * @return bool
     */
    private static function login($strUsername, $strPlainPassword, $hashedPassword = "")
    {
        global $objSession;
        try {
            switch (Config::get("userToLogin")) {
                case "email":
                    $userData = User::getActiveCredentialByEmail($strUsername);
                    break;
                case "both":
                    $userData = User::getActiveCredentialByEmailOrUsername($strUsername, $strUsername);
                    break;
                default:
                    $userData = User::getActiveCredentialByUsername($strUsername);
                    break;
            }
            if ($userData != false) {
                $userPwd = $userData["pwd"];
                $userId = $userData["id"];
                if ($hashedPassword == "" && password_verify($strPlainPassword, $userPwd) || $hashedPassword != "" && $hashedPassword == $userPwd) {
                    $objSession->loginUser($userId);
                    $userObject = new User($userId);
                    $userObject->last_access = time();
                    $userObject->save();
                    return true;
                }
            }
        } catch (PDOException $e) {
            Logger::write($e,Logger::LEVEL_ERROR);
        } catch (Exception $e) {
            Logger::write($e,Logger::LEVEL_ERROR);
        }
        return false;
    }

    /**
     * @param $boostack
     * @param $response
     * @return bool
     */
    private static function reCaptchaVerify($boostack, $response)
    {
        $reCaptcha_private = Config::get("reCaptcha_private");
        $curlRequest = new CurlRequest();
        $curlRequest->setEndpoint(Config::get("google_recaptcha-endpoint"));
        $curlRequest->setIsPost(true);
        $curlRequest->setReturnTransfer(true);
        $curlRequest->setPostFields([
            "secret" => $reCaptcha_private,
            "response" => $response
        ]);
        $response = $curlRequest->send();
        $a =  json_decode($response->getData(), true);
        return(!$response->hasError() && $a["success"]);
    }

    /**
     * @param $lastLogin
     * @return bool
     */
    private static function checkAcceptedTimeFromLastLogin($lastLogin)
    {
        return $lastLogin != 0 && (time() - $lastLogin > Config::get("login_secondsFormBlocked"));
    }

}