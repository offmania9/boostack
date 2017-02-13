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

    public static function tryLogin($username, $password, $cookieRememberMe = false)
    {
        global $boostack,$objSession;
        $result = new MessageBag();
        try {
            if(!Utils::checkAcceptedTimeFromLastRequest(self::getLastTry())) throw new Exception("Too much request. Wait some seconds");
            if(Auth::isLoggedIn()) return true;
            if(empty($username)) throw new Exception("Missing username");
            if(empty($password)) throw new Exception("Missing password");

            if($boostack->getConfig('csrf_on')) $objSession->CSRFCheckValidity($_POST);
            Auth::impressLastTry();
            Utils::checkStringFormat($password);

            Auth::checkAndLogin($username, $password, $cookieRememberMe, true);

        } catch (Exception $e) {
            $boostack->writeLog("Login.php : ".$e->getMessage()." trace:".$e->getTraceAsString(),"user");
            $result->setError($e->getMessage());
        }
        return $result;
    }

    private static function checkAndLogin($username, $password, $cookieRememberMe, $throwException = true)
    {
        global $objSession, $boostack;
        if ($boostack->getConfig("userToLogin") == "email") {
            if (!User::existsByEmail($username)) {
                $boostack->writeLog("User -> tryLogin: User doesn't exist by Email Address", "user");
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        if ($boostack->getConfig("userToLogin") == "username") {
            if (!User::existsByUsername($username)) {
                $boostack->writeLog("User -> tryLogin: User doesn't exist by Username", "user");
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        if($boostack->getConfig("userToLogin") == "both") {
            if(!User::existsByEmail($username,false) && !User::existsByUsername($username,false)) {
                $boostack->writeLog("User -> tryLogin: User doesn't exist by Username and by email", "user");
                if ($throwException)
                    throw new Exception("Username or password not valid.", 6);
                return false;
            }
        }

        self::logout();
        self::login($username, $password);

        if (!self::isLoggedIn()){
            $boostack->writeLog("User -> tryLogin: Username or password not valid.","user");
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
        if (version_compare(PHP_VERSION, '5.5.0') >= 0)
            self::LoginWithSalt($strUsername, $strPlainPassword, $hashedPassword);
        else
            self::LoginBasic($strUsername, $strPlainPassword, $hashedPassword);
    }

    /*  Esegue il login
     *
     * @param $strUsername          username
     * @param $strPlainPassword     password in chiaro (utilizzata durante il login da form)
     * @param $hashedPassword       password in hash (utilizzata durante il login da cookie)
     *
     * @need PHP>5.5 per password_verify
     *
     */
    protected static function LoginWithSalt($strUsername, $strPlainPassword, $hashedPassword = "")
    {
        global $objSession;
        try {
            switch (Boostack::getInstance()->getConfig("userToLogin")) {
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

    protected static function LoginBasic($strUsername, $strPlainPassword, $hashed_psw = "")
    {
        global $objSession;
        $strMD5Password = ($hashed_psw !== "") ? $hashed_psw : hash("sha512", $strPlainPassword);
        try {
            switch (Boostack::getInstance()->getConfig("userToLogin")) {
                case "email":
                    $userData = User::getActiveIdByEmailAndPassword($strUsername, $strMD5Password);
                    break;
                case "both":
                    $userData = User::getActiveIdByEmailOrUsernameAndPassword($strUsername, $strUsername, $strMD5Password);
                    break;
                default:
                    $userData = User::getActiveIdByUsernameAndPassword($strUsername,$strMD5Password);
                    break;
            }
            if ($userData != false) {
                $userPwd = $userData["pwd"];
                $userId = $userData["id"];
                $objSession->loginUser($userId);
                $userObject = new User($userId);
                $userObject->last_access = time();
                $userObject->save();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            Boostack::getInstance()->writeLog('LogList -> view -> Caught PDOException: ' . $e->getMessage(), "error");
        } catch (Exception $b) {
            Boostack::getInstance()->writeLog('LogList -> view -> Caught Exception: ' . $b->getMessage(), "error");
        }
    }


    /*  Esegue il login se è presente il "Remember Me" cookie
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
                if ($boostack->getConfig("session_on") && isset($objSession) && self::isLoggedIn()){
                    $boostack->writeLog("[Logout] uid: ".$objSession->GetUserID(),"user");
                    $objSession->logoutUser();
                }
                if ($boostack->getConfig("cookie_on")) {
                    $cookieName = $boostack->getConfig("cookie_name");
                    $cookieExpire = $boostack->getConfig("cookie_expire");
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

}