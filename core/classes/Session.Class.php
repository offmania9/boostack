<?php

/**
 * Boostack: Session.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

class Session
{
    /**
     * Retrieves the value of a session key.
     *
     * @param string $key The key of the session.
     * @return mixed The value of the session key.
     */
    public static function get(string $key)
    {
        global $objSession;
        return $objSession->$key;
    }

    /**
     * Sets the value of a session key.
     *
     * @param string $key The key of the session.
     * @param mixed $value The value to set.
     * @return void
     */
    public static function set(string $key, $value): void
    {
        global $objSession;
        $objSession->$key = $value;
    }

    /**
     * Retrieves the session object.
     *
     * @return mixed The session object.
     */
    public static function getObject()
    {
        global $objSession;
        return $objSession;
    }

    /**
     * Retrieves the user object from the session.
     *
     * @return mixed The user object.
     */
    public static function getUserObject()
    {
        global $objSession;
        return $objSession->GetUserObject();
    }

    /**
     * Retrieves the user ID from the session.
     *
     * @return mixed The user ID.
     */
    public static function getUserID()
    {
        global $objSession;
        return $objSession->GetUserID();
    }

    /**
     * Logs in a user.
     *
     * @param mixed $userID The user ID to log in.
     * @return mixed The result of the login operation.
     */
    public static function loginUser($userID)
    {
        global $objSession;
        return $objSession->loginUser($userID);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed The result of the logout operation.
     */
    public static function logoutUser()
    {
        global $objSession;
        return $objSession->logoutUser();
    }

    /**
     * Checks if a user is logged in.
     *
     * @return bool True if a user is logged in, false otherwise.
     */
    public static function isLoggedIn(): bool
    {
        global $objSession;
        return $objSession->IsLoggedIn();
    }

    /**
     * Performs a CSRF validity check on the given POST array.
     *
     * @param array $postArray The POST array to check.
     * @param bool $throwException Whether to throw an exception on failure.
     * @return mixed The result of the CSRF validity check.
     */
    public static function CSRFCheckValidity(array $postArray, bool $throwException = true)
    {
        global $objSession;
        return $objSession->CSRFCheckValidity($postArray, $throwException);
    }

    /**
     * Renders a hidden CSRF field.
     *
     * @return mixed The rendered hidden CSRF field.
     */
    public static function CSRFRenderHiddenField()
    {
        global $objSession;
        return $objSession->CSRFRenderHiddenField();
    }
}
