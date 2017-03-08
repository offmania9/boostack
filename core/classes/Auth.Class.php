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
class Auth {

    const LOCK_TIMER = -1;
    const LOCK_RECAPTCHA = -2;

    /*
     * Esegue il login dell'utente con username e password in chiaro
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
            if($isLockStrategyEnabled) $objSession->failed_login_count++;
            Auth::checkAndLogin($username, $password, $cookieRememberMe, true);
            if($isLockStrategyEnabled) $objSession->failed_login_count = 0;

        } catch (Exception $e) {
            $boostack->writeLog("Login.php : ".$e->getMessage()." trace:".$e->getTraceAsString(),"user");
            $result->setError($e->getMessage());
            $result->setCode($e->getCode());
        }

        return $result;
    }

    /*
     * Esegue il login dell'utente con userID passato come parametero
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
                    $boostack->writeLog("checkCookieHashValidity(" . $cookieValue . "): false - IP:" . Utils::getIpAddress(),"user");
                }
            }
        } catch (PDOException $e) {
            $boostack->writeLog('Session_HTTP -> loginByCookie -> PDOException: ' . $e->getMessage(), "error");
        } catch (Exception $b) {
            $boostack->writeLog('Session_HTTP -> loginByCookie -> Exception: ' . $b->getMessage(), "error");
        }
        return false;
    }

    public static function isLoggedIn()
    {
        global $objSession;
        return $objSession->IsLoggedIn();
    }

    public static function logout()
    {
        global $objSession, $boostack;
        try {
            if(self::isLoggedIn()) {
                if (Config::get("session_on") && isset($objSession) && self::isLoggedIn()){
                    $boostack->writeLog("[Logout] uid: ".$objSession->GetUserID(),"user");
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
            $boostack->writeLog('Session_HTTP -> LogOut -> PDOException: ' . $e->getMessage(), "error");
        } catch (Exception $b) {
            $boostack->writeLog('Session_HTTP -> LogOut -> Exception: ' . $b->getMessage(), "error");
        }
        return false;
    }

    public static function getLastTry()
    {
        global $objSession;
        return $objSession->LastTryLogin;
    }

    public static function impressLastTry()
    {
        global $objSession;
        $objSession->LastTryLogin = time();
    }

    public static function getUserLoggedObject()
    {
        global $objSession;
        return $objSession->GetUserObject();
    }

    public static function isTimerLocked()
    {
        global $objSession;
        return Config::get("lockStrategy_on") && Config::get("login_lockStrategy") == "timer" && $objSession->failed_login_count >= Config::get("login_maxAttempts") && !self::checkAcceptedTimeFromLastLogin(self::getLastTry());
    }

    public static function haveToShowCaptcha()
    {
        global $objSession;
        return Config::get("lockStrategy_on") && Config::get("login_lockStrategy") == "recaptcha" && $objSession->failed_login_count >= Config::get("login_maxAttempts");

    }

    private static function checkAndLogin($username, $password, $cookieRememberMe, $throwException = true)
    {
        global $objSession, $boostack;
        if (Config::get("userToLogin") == "email") {
            if (!User::existsByEmail($username)) {
                $boostack->writeLog("Auth -> checkAndLogin: User doesn't exist by Email Address", "user");
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        if (Config::get("userToLogin") == "username") {
            if (!User::existsByUsername($username)) {
                $boostack->writeLog("Auth -> checkAndLogin: User doesn't exist by Username", "user");
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        if(Config::get("userToLogin") == "both") {
            if(!User::existsByEmail($username,false) && !User::existsByUsername($username,false)) {
                $boostack->writeLog("Auth -> tryLogin: User doesn't exist by Username and by email", "user");
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        self::logout();
        self::login($username, $password);

        if (!self::isLoggedIn()){
            $boostack->writeLog("Auth -> checkAndLogin: Username or password not valid.","user");
            if ($throwException)
                throw new Exception("Username or password not valid.",5);
            return false;
        }

        if ($cookieRememberMe) {
            $user = $objSession->GetUserObject();
            $user->refreshRememberMeCookie();
        }
        $boostack->writeLog("[Login] uid: ".$objSession->GetUserID(),"user");
        return true;
    }

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
            Boostack::getInstance()->writeLog('LogList -> view -> Caught PDOException: '.$e->getMessage(),"error");
        } catch ( Exception $b ) {
            Boostack::getInstance()->writeLog('LogList -> view -> Caught Exception: '.$b->getMessage(),"error");
        }
        return false;
    }

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

    private static function checkAcceptedTimeFromLastLogin($lastLogin)
    {
        return $lastLogin != 0 && (time() - $lastLogin > Config::get("login_secondsFormBlocked"));
    }

}