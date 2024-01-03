<?php
/**
 * Boostack: Session.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class Session
{
    public static function get($key)
    {
        global $objSession;
        return $objSession->$key;
    }

    public static function set($key, $value)
    {
        global $objSession;
        $objSession->$key = $value;
    }

    public static function getObject()
    {
        global $objSession;
        return $objSession;
    }

    public static function getUserObject()
    {
        global $objSession;
        return $objSession->GetUserObject();
    }

    public static function getUserID()
    {
        global $objSession;
        return $objSession->GetUserID();
    }

    public static function loginUser($userID)
    {
        global $objSession;
        return $objSession->loginUser($userID);
    }

    public static function logoutUser()
    {
        global $objSession;
        return $objSession->logoutUser();
    }

    public static function isLoggedIn()
    {
        global $objSession;
        return $objSession->IsLoggedIn();
    }

    public static function CSRFCheckValidity($postArray, $throwException = true)
    {
        global $objSession;
        return $objSession->CSRFCheckValidity($postArray, $throwException);
    }

    public static function CSRFRenderHiddenField()
    {
        global $objSession;
        return $objSession->CSRFRenderHiddenField();
    }

}