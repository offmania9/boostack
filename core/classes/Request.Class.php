<?php
/**
 * Boostack: Request.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */

class Request
{
    /**
     * @var
     */
    protected static $query;
    /**
     * @var
     */
    protected static $post;
    /**
     * @var
     */
    protected static $server;
    /**
     * @var
     */
    protected static $request;
    /**
     * @var
     */
    protected static $files;
    /**
     * @var
     */
    protected static $cookie;

    /**
     *
     */
    public static function init()
    {
        self::registerFromGlobals();
    }

    /**
     * @param $type
     * @param $param
     * @return bool
     */
    private static function has($type,$param)
    {
        return isset(self::${$type}[$param]);
    }

    /**
     * @param $type
     * @param $param
     * @return mixed
     */
    private static function get($type, $param)
    {
        return self::${$type}[$param];
    }

    /**
     *
     */
    private static function registerFromGlobals()
    {
        self::$query = $_GET;
        self::$post = $_POST;
        self::$server = $_SERVER;
        self::$request = $_REQUEST;
        self::$files = $_FILES;
        self::$cookie = $_COOKIE;
    }

    /**
     * @param $param
     * @return bool
     */
    public static function hasPostParam($param)
    {
        return self::has(RequestType::POST, $param);
    }

    /**
     * @param $param
     * @return array|null|string
     */
    public static function getPostParam($param)
    {
        $rt = RequestType::POST;
        return Utils::sanitizeInput(self::get($rt,$param));
    }

    /**
     * @return array|string
     */
    public static function getPostArray()
    {
        return Utils::sanitizeInput(self::$post);
    }

    /**
     * @param $param
     * @return bool
     */
    public static function hasQueryParam($param)
    {
        return self::has(RequestType::QUERY,$param);
    }

    /**
     * @param $param
     * @return array|null|string
     */
    public static function getQueryParam($param)
    {
        $rt = RequestType::QUERY;
        return Utils::sanitizeInput(self::get($rt,$param));
    }

    /**
     * @return array|string
     */
    public static function getQueryArray()
    {
        return Utils::sanitizeInput(self::$query);
    }

    /**
     * @param $param
     * @return bool
     */
    public static function hasServerParam($param)
    {
        return self::has(RequestType::SERVER,$param);
    }

    /**
     * @param $param
     * @return array|null|string
     */
    public static function getServerParam($param)
    {
        $rt = RequestType::SERVER;
        return Utils::sanitizeInput(self::get($rt,$param));
    }

    /**
     * @return array|string
     */
    public static function getServerArray()
    {
        return Utils::sanitizeInput(self::$server);
    }

    /**
     * @param $param
     * @return bool
     */
    public static function hasCookieParam($param)
    {
        return self::has(RequestType::COOKIE,$param);
    }

    /**
     * @param $param
     * @return array|null|string
     */
    public static function getCookieParam($param)
    {
        $rt = RequestType::COOKIE;
        return Utils::sanitizeInput(self::get($rt,$param));
    }

    /**
     * @return array|string
     */
    public static function getCookieArray()
    {
        return Utils::sanitizeInput(self::$cookie);
    }

    /**
     * @param $param
     * @return bool
     */
    public static function hasRequestParam($param)
    {
        return self::has(RequestType::REQUEST,$param);
    }

    /**
     * @param $param
     * @return array|null|string
     */
    public static function getRequestParam($param)
    {
        $rt = RequestType::REQUEST;
        return Utils::sanitizeInput(self::get($rt,$param));
    }

    /**
     * @return array|string
     */
    public static function getRequestArray()
    {
        return Utils::sanitizeInput(self::$request);
    }

    /**
     * @param $param
     * @return bool
     */
    public static function hasFilesParam($param)
    {
        return self::has(RequestType::FILES,$param);
    }

    /**
     * @param $param
     * @return array|null|string
     */
    public static function getFilesParam($param)
    {
        $rt = RequestType::FILES;
        return Utils::sanitizeInput(self::get($rt,$param));
    }

    /**
     * @return array|string
     */
    public static function getFilesArray()
    {
        return Utils::sanitizeInput(self::$files);
    }
}
?>