<?php

namespace Core\Models;
use Core\Models\User\User;
use Core\Models\Session\Session;
use Core\Models\Log\Log_Driver;
use Core\Models\Log\Log_Level;
use Core\Models\Log\Logger;
use Core\Models\Utils\Validator;
use Core\Exception\Exception_Registration;

/**
 * Boostack: Auth.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 5.0
 */

class Auth
{

    const LOCK_TIMER = -1;

    const LOCK_RECAPTCHA = -2;

    /**
     * Performs user login using username and clear text password.
     *
     * @param string $username User's username.
     * @param string $password User's password.
     * @param bool $cookieRememberMe Flag to indicate whether to set the "Remember Me" cookie.
     * @return MessageBag Object containing information about the login result.
     */
    public static function loginByUsernameAndPlainPassword($username, $password, $cookieRememberMe = false)
    {
        $result = new MessageBag();
        $isLockStrategyEnabled = Config::get("lockStrategy_on");
        $lockStrategy = Config::get("login_lockStrategy");

        try {
            // Check for maximum request count
            if (!Request::checkAcceptedTimeFromLastRequest(self::getLastTry())) {
                throw new \Exception("Too many requests. Please wait a few seconds");
            }

            // If user is already logged in, return immediately
            if (Auth::isLoggedIn()) {
                return $result;
            }

            // Check lock strategy
            if ($isLockStrategyEnabled) {
                if (!Session::get("failed_login_count")) Session::set("failed_login_count", 0);
                if (Session::get("failed_login_count") >= Config::get("login_maxAttempts")) {
                    if ($lockStrategy == "timer") {
                        if (!self::checkAcceptedTimeFromLastLogin(self::getLastTry())) throw new \Exception("Too many login requests. Please wait a few seconds", self::LOCK_TIMER);
                    } else if ($lockStrategy == "recaptcha") {
                        $recaptchaFormData = Request::hasPostParam("g-recaptcha-response") ? Request::getPostParam("g-recaptcha-response") : null;
                        if (empty($recaptchaFormData)) throw new \Exception("Missing reCAPTCHA data", self::LOCK_RECAPTCHA);
                        $recaptchaResponse = self::reCaptchaVerify(Request::getPostParam("g-recaptcha-response"));
                        if (!$recaptchaResponse) throw new \Exception("Invalid reCAPTCHA", self::LOCK_RECAPTCHA);
                    }
                    Session::set("failed_login_count", 0);
                }
            }

            // Validate username and password format
            if (!Validator::username($username)) throw new \Exception("Invalid username format");
            if (!Validator::password($password)) throw new \Exception("Invalid password format");

            // Update last login attempt
            Auth::impressLastTry();

            // Increment failed login attempts count
            if ($isLockStrategyEnabled || ($isLockStrategyEnabled && Config::get('csrf_on') && Session::CSRFCheckValidity(Request::getPostArray(), false))) Session::set("failed_login_count", Session::get("failed_login_count") + 1);

            // Perform user login
            Auth::checkAndLogin($username, $password, $cookieRememberMe, true);

            // Reset failed login attempts count
            if ($isLockStrategyEnabled) Session::set("failed_login_count", 0);
        } catch (\Exception $e) {
            // Log error
            Logger::write($e, Log_Level::USER);
            // Set error message and code in result object
            $result->error = ($e->getMessage());
            $result->code = ($e->getCode());
        }

        // Return result object
        return $result;
    }


    /**
     * Log in the user by userID.
     *
     * @param int $userID The user ID to log in.
     */
    public static function loginByUserID($userID)
    {
        // Determine the user class to use
        $userClass = Config::get("use_custom_user_class") ? Config::get("custom_user_class") : User::class;

        // If the user is not already logged in, proceed with login
        if (!Auth::isLoggedIn()) {
            // Create an instance of the user class with the given userID
            $user = new $userClass($userID);

            // Perform login with username, empty password, and user's stored password
            self::login($user->username, "", $user->pwd);

            // Update the last access time of the user
            $user->last_access = time();

            // Save the user's updated information
            $user->save();
        }
    }


    /*
    * Log in the user using the "remember-me cookie".
    *
    * @param string $cookieValue The value of the cookie.
    * @return bool True if the login is successful, false otherwise.
    */
    public static function loginByCookie($cookieValue)
    {
        try {
            // Determine the user class to use
            $userClass = Config::get("use_custom_user_class") ? Config::get("custom_user_class") : User::class;

            // Retrieve user credentials from the cookie value
            $userCredentials = $userClass::getCredentialByCookie($cookieValue);

            // If user credentials are found
            if ($userCredentials !== false) {
                // Check the validity of the cookie hash
                if (Request::checkCookieHashValidity($cookieValue)) {
                    // Determine the username to log in based on the configuration
                    $usernameToLogin = Config::get("userToLogin") == "email" ? $userCredentials["email"] : $userCredentials["username"];

                    // Perform login with the retrieved credentials
                    $loginResult = self::login($usernameToLogin, "", $userCredentials["pwd"]);

                    // If login is successful, refresh the "remember-me" cookie
                    if ($loginResult) {
                        $userObject = Session::getUserObject();
                        $userObject->refreshRememberMeCookie();
                        return true;
                    }
                } else {
                    // Log the invalid cookie hash
                    Logger::write("checkCookieHashValidity(" . $cookieValue . "): false - IP:" . Request::getIpAddress(), Log_Level::USER);
                }
            }
        } catch (\PDOException $e) {
            // Log database-related errors
            Logger::write($e, Log_Level::ERROR, Log_Driver::FILE);
        } catch (\Exception $e) {
            // Log other \Exceptions
            Logger::write($e, Log_Level::ERROR);
        }
        return false;
    }


    /**
     * Register a new user.
     *
     * @param string $username The username of the new user.
     * @param string $email The email of the new user.
     * @param string $psw1 The password of the new user.
     * @param string $psw2 The confirmation password of the new user.
     * @param string|null $CSRFToken The CSRF token for validation (optional).
     * @return bool True if registration is successful, false otherwise.
     * @throws \Exception If registration fails.
     */
    public static function registration($username, $email, $psw1, $psw2, $CSRFToken = NULL)
    {
        $registrationError = "";
        try {
            // Validate passwords match
            if ($psw1 !== $psw2) $registrationError = "Passwords must match";

            // Validate email format
            if (!Validator::email($email)) $registrationError = "Invalid email format";

            // Validate password format
            if (!Validator::password($psw1)) $registrationError = "Invalid password format";

            // Check if email is already registered
            if (User::existsByEmail($email, false) || User::existsByUsername($email, false)) $registrationError = "Email already registered";

            // Validate CSRF token if enabled
            if (Config::get('csrf_on')) {
                if (empty($CSRFToken)) throw new \Exception("CSRF token is required");
                $token_key = Session::getObject()->getCSRFDefaultKey();
                Session::CSRFCheckValidity(array($token_key => $CSRFToken));
            }

            // If no registration errors, proceed with registration
            if (strlen($registrationError) == 0) {
                $user = new User();
                $user->username = $username;
                $user->email = $email;
                $user->active = true;
                $user->pwd = $psw1;
                $user->save();

                // Log in the newly registered user
                Auth::loginByUserID($user->id);

                // Invalidate CSRF token if enabled
                if (Config::get('csrf_on')) {
                    Session::getObject()->CSRFTokenInvalidation();
                }

                return true; // Registration successful
            } else {
                // Log registration error and throw \Exception
                Logger::write($registrationError, Log_Level::ERROR);
                throw new Exception_Registration($registrationError);
            }
        } catch (\PDOException $e) {
            // Log database-related errors
            Logger::write($e, Log_Level::ERROR, Log_Driver::FILE);
        } catch (\Exception $e) {
            // Log other \Exceptions and re-throw
            Logger::write($e, Log_Level::ERROR);
            throw $e;
        }
        return false; // Registration failed
    }

    /**
     * Check if a user is logged in.
     *
     * @return mixed The user's login status.
     */
    public static function isLoggedIn()
    {
        return Session::isLoggedIn();
    }

    /**
     * Log out the current user.
     *
     * @return bool True if logout is successful, false otherwise.
     */
    public static function logout()
    {
        try {
            // Check if user is logged in before attempting to logout
            if (self::isLoggedIn()) {
                // Log the logout event
                Logger::write("[Logout] uid: " . Session::getUserID(), Log_Level::USER);

                // Perform logout by clearing session data
                Session::logoutUser();

                // If cookies are enabled, delete the authentication cookie
                if (Config::get("cookie_on")) {
                    $cookieName = Config::get("cookie_name");
                    $cookieExpire = Config::get("cookie_expire");
                    setcookie($cookieName, false, time() - $cookieExpire);
                    setcookie($cookieName, false, time() - $cookieExpire, "/");
                }

                return true; // Logout successful
            }
        } catch (\PDOException $e) {
            // Log database-related errors
            Logger::write($e, Log_Level::ERROR, Log_Driver::FILE);
        } catch (\Exception $e) {
            // Log other \Exceptions
            Logger::write($e, Log_Level::ERROR);
        }
        return false; // Logout failed
    }

    /**
     * Get the timestamp of the last login attempt.
     *
     * @return mixed The timestamp of the last login attempt.
     */
    public static function getLastTry()
    {
        return Session::get("LastTryLogin");
    }

    /**
     * Update the timestamp of the last login attempt.
     */
    public static function impressLastTry()
    {
        Session::set("LastTryLogin", time());
    }
    /**
     * Get the user object of the logged-in user.
     *
     * @return mixed The user object of the logged-in user.
     */
    public static function getUserLoggedObject()
    {
        $ret = null;
        if (Config::get("session_on"))
            $ret = Session::getUserObject();
        return $ret;
    }

    /**
     * Check if the timer lock is enabled for login attempts.
     *
     * @return bool True if the timer lock is enabled, false otherwise.
     */
    public static function isTimerLocked()
    {
        return Config::get("lockStrategy_on") && Config::get("login_lockStrategy") == "timer" && Session::get("failed_login_count") >= Config::get("login_maxAttempts") && !self::checkAcceptedTimeFromLastLogin(self::getLastTry());
    }

    /**
     * Check if a captcha needs to be shown based on login attempts.
     *
     * @return bool True if a captcha needs to be shown, false otherwise.
     */
    public static function haveToShowCaptcha()
    {
        return Config::get("lockStrategy_on") && Config::get("login_lockStrategy") == "recaptcha" && Session::get("failed_login_count") >= Config::get("login_maxAttempts");
    }

    /**
     * Check and log in a user.
     *
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     * @param bool $cookieRememberMe Indicates whether to remember the user with a cookie.
     * @param bool $throwException Indicates whether to throw \Exceptions on failure.
     * @return bool True if login is successful, false otherwise.
     * @throws \Exception If login fails.
     */
    private static function checkAndLogin($username, $password, $cookieRememberMe, $throwException = true)
    {
        $userClass = Config::get("use_custom_user_class") ? Config::get("custom_user_class") : User::class;
        if (Config::get("userToLogin") == "email") {
            if (!$userClass::existsByEmail($username)) {
                Logger::write("Auth -> checkAndLogin: User doesn't exist by Email Address", Log_Level::USER);
                if ($throwException)
                    throw new \Exception("Username or password not valid.", 6);
                return false;
            }
        }

        if (Config::get("userToLogin") == "username") {
            if (!$userClass::existsByUsername($username)) {
                Logger::write("Auth -> checkAndLogin: User doesn't exist by Username", Log_Level::USER);
                if ($throwException)
                    throw new \Exception("Username or password not valid.", 6);
                return false;
            }
        }

        if (Config::get("userToLogin") == "both") {
            if (!$userClass::existsByEmail($username, false) && !$userClass::existsByUsername($username, false)) {
                Logger::write("Auth -> tryLogin: User doesn't exist by Username and by email", Log_Level::USER);
                if ($throwException)
                    throw new \Exception("Username or password not valid.", 6);
                return false;
            }
        }

        self::logout();
        self::login($username, $password);

        if (!self::isLoggedIn()) {
            Logger::write("Auth -> checkAndLogin: Username or password not valid.", Log_Level::USER);
            if ($throwException)
                throw new \Exception("Username or password not valid.", 5);
            return false;
        }

        if ($cookieRememberMe) {
            $user = Session::getUserObject();
            $user->refreshRememberMeCookie();
        }
        //Logger::write("[Login] uid: ".Session::getUserID(),Log_Level::USER);
        return true;
    }
    /**
     * Log in a user with the provided username and password.
     *
     * @param string $strUsername The username of the user.
     * @param string $strPlainPassword The plain password of the user.
     * @param string $hashedPassword The hashed password of the user.
     * @return bool True if login is successful, false otherwise.
     */
    private static function login($strUsername, $strPlainPassword, $hashedPassword = "")
    {
        $userClass = Config::get("use_custom_user_class") ? Config::get("custom_user_class") : User::class;
        try {
            switch (Config::get("userToLogin")) {
                case "email":
                    $userData = $userClass::getActiveCredentialByEmail($strUsername);
                    break;
                case "both":
                    $userData = $userClass::getActiveCredentialByEmailOrUsername($strUsername, $strUsername);
                    break;
                default:
                    $userData = $userClass::getActiveCredentialByUsername($strUsername);
                    break;
            }
            if ($userData != false) {
                $userPwd = $userData["pwd"];
                $userId = $userData["id"];
                if ($hashedPassword == "" && password_verify($strPlainPassword, $userPwd) || $hashedPassword != "" && $hashedPassword == $userPwd) {
                    Session::loginUser($userId);
                    $userObject = new $userClass($userId);
                    $userObject->last_access = time();
                    $userObject->save();
                    Logger::write("[Login] uid: " . $userId, Log_Level::USER);
                    return true;
                }
            }
        } catch (\PDOException $e) {
            Logger::write($e, Log_Level::ERROR, Log_Driver::FILE);
        } catch (\Exception $e) {
            Logger::write($e, Log_Level::ERROR);
        }
        return false;
    }

    /**
     * Verify the reCaptcha response.
     *
     * @param string $response The reCaptcha response.
     * @return bool True if the reCaptcha is valid, false otherwise.
     */
    private static function reCaptchaVerify($response)
    {
        $reCaptcha_private = Config::get("reCaptcha_private");
        $curlRequest = new \Core\Models\Curl\CurlRequest();
        $curlRequest->setEndpoint(Config::get("google_recaptcha-endpoint"));
        $curlRequest->setIsPost(true);
        $curlRequest->setReturnTransfer(true);
        $curlRequest->setPostFields([
            "secret" => $reCaptcha_private,
            "response" => $response
        ]);
        $response = $curlRequest->send();
        $a =  json_decode($response->data, true);
        return (!$response->hasError() && $a["success"]);
    }

    /**
     * Check if enough time has passed since the last login attempt.
     *
     * @param int $lastLogin The timestamp of the last login attempt.
     * @return bool True if enough time has passed, false otherwise.
     */
    private static function checkAcceptedTimeFromLastLogin($lastLogin)
    {
        return $lastLogin != 0 && (time() - $lastLogin > Config::get("login_secondsFormBlocked"));
    }

    /**
     * Checks if the current user has the specified privilege level.
     *
     * @param mixed $currentUser The current user object.
     * @param int $privilegeLevel The privilege level to be checked.
     */
    public static function checkPrivilege($currentUser, $privilegeLevel)
    {
        return (!self::hasPrivilege($currentUser, $privilegeLevel));
    }

    /**
     * Checks if the current user has the specified privilege level.
     *
     * @param mixed $currentUser The current user object.
     * @param int $privilegeLevel The privilege level to be checked.
     * @return bool Returns true if the user has the specified privilege level, false otherwise.
     */
    public static function hasPrivilege($currentUser, $privilegeLevel)
    {
        if ($currentUser == null)
            return false;

        if ($currentUser->privilege > $privilegeLevel)
            return false;

        return true;
    }
}
