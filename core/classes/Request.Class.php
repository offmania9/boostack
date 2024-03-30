<?php

/**
 * Boostack: Request.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

/**
 * Class Request
 * Represents an HTTP request handler.
 */
class Request
{
    protected static $query;
    protected static $post;
    protected static $server;
    protected static $request;
    protected static $files;
    protected static $cookie;
    protected static $headers;

    /**
     * Initializes the request by registering from global variables.
     */
    public static function init()
    {
        self::registerFromGlobals();
    }

    /**
     * Checks if a parameter exists in a specific request type.
     *
     * @param string $type The request type (e.g., 'POST', 'QUERY', 'SERVER', 'HEADERS', 'COOKIE', 'REQUEST', 'FILES').
     * @param mixed $param The parameter to check.
     * @return bool
     */
    private static function has(string $type, $param): bool
    {
        return isset(self::${$type}[$param]);
    }

    /**
     * Retrieves a parameter from a specific request type.
     *
     * @param string $type The request type (e.g., 'POST', 'QUERY', 'SERVER', 'HEADERS', 'COOKIE', 'REQUEST', 'FILES').
     * @param mixed $param The parameter to retrieve.
     * @return mixed
     */
    private static function get(string $type, $param)
    {
        return isset(self::${$type}[$param]) && self::${$type}[$param] !== null ? self::${$type}[$param] : null;
    }

    /**
     * Registers request data from global variables.
     */
    private static function registerFromGlobals()
    {
        self::$query = $_GET;
        self::$post = $_POST;
        self::$server = $_SERVER;
        self::$request = $_REQUEST;
        self::$files = $_FILES;
        self::$cookie = $_COOKIE;
        self::$headers = getallheaders();
    }

    /**
     * Checks if a POST parameter exists.
     *
     * @param string $param The parameter name.
     * @return bool True if the parameter exists, false otherwise.
     */
    public static function hasPostParam(string $param): bool
    {
        return self::has(RequestType::POST, $param);
    }

    /**
     * Retrieves a POST parameter value.
     *
     * @param string $param The parameter name.
     * @return mixed|null|string The sanitized parameter value.
     */
    public static function getPostParam(string $param)
    {
        $rt = RequestType::POST;
        return Utils::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all POST parameters.
     *
     * @return array|string The sanitized POST parameters.
     */
    public static function getPostArray()
    {
        return Utils::sanitizeInput(self::$post);
    }

    /**
     * Checks if a QUERY parameter exists.
     *
     * @param string $param The parameter name.
     * @return bool True if the parameter exists, false otherwise.
     */
    public static function hasQueryParam(string $param): bool
    {
        return self::has(RequestType::QUERY, $param);
    }

    /**
     * Retrieves a QUERY parameter value.
     *
     * @param string $param The parameter name.
     * @return mixed|null|string The sanitized parameter value.
     */
    public static function getQueryParam(string $param)
    {
        $rt = RequestType::QUERY;
        return Utils::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all QUERY parameters.
     *
     * @return array|string The sanitized QUERY parameters.
     */
    public static function getQueryArray()
    {
        return Utils::sanitizeInput(self::$query);
    }

    /**
     * Checks if a SERVER parameter exists.
     *
     * @param string $param The parameter name.
     * @return bool True if the parameter exists, false otherwise.
     */
    public static function hasServerParam(string $param): bool
    {
        return self::has(RequestType::SERVER, $param);
    }


    /**
     * Retrieves a SERVER parameter value.
     *
     * @param string $param The parameter name.
     * @return mixed|null|string The sanitized parameter value.
     */
    public static function getServerParam(string $param)
    {
        $rt = RequestType::SERVER;
        return Utils::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all SERVER parameters.
     *
     * @return array|string The sanitized SERVER parameters.
     */
    public static function getServerArray()
    {
        return Utils::sanitizeInput(self::$server);
    }

    /**
     * Checks if a HEADER parameter exists.
     *
     * @param string $param The parameter name.
     * @return bool True if the parameter exists, false otherwise.
     */
    public static function hasHeaderParam(string $param): bool
    {
        return self::has(RequestType::HEADERS, $param);
    }

    /**
     * Retrieves a HEADER parameter value.
     *
     * @param string $param The parameter name.
     * @return mixed|null|string The sanitized parameter value.
     */
    public static function getHeaderParam(string $param)
    {
        $rt = RequestType::HEADERS;
        return Utils::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all HEADER parameters.
     *
     * @return array|string The sanitized HEADER parameters.
     */
    public static function getHeaderArray()
    {
        return Utils::sanitizeInput(self::$headers);
    }

    /**
     * Checks if a COOKIE parameter exists.
     *
     * @param string $param The parameter name.
     * @return bool True if the parameter exists, false otherwise.
     */
    public static function hasCookieParam(string $param): bool
    {
        return self::has(RequestType::COOKIE, $param);
    }

    /**
     * Retrieves a COOKIE parameter value.
     *
     * @param string $param The parameter name.
     * @return mixed|null|string The sanitized parameter value.
     */
    public static function getCookieParam(string $param)
    {
        $rt = RequestType::COOKIE;
        return Utils::sanitizeInput(self::get($rt, $param));
    }
    /**
     * Retrieves all COOKIE parameters.
     *
     * @return array|string The sanitized COOKIE parameters.
     */
    public static function getCookieArray()
    {
        return Utils::sanitizeInput(self::$cookie);
    }

    /**
     * Checks if a REQUEST parameter exists.
     *
     * @param string $param The parameter name.
     * @return bool True if the parameter exists, false otherwise.
     */
    public static function hasRequestParam(string $param): bool
    {
        return self::has(RequestType::REQUEST, $param);
    }

    /**
     * Retrieves a REQUEST parameter value.
     *
     * @param string $param The parameter name.
     * @return mixed|null|string The sanitized parameter value.
     */
    public static function getRequestParam(string $param)
    {
        $rt = RequestType::REQUEST;
        return Utils::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all REQUEST parameters.
     *
     * @return array|string The sanitized REQUEST parameters.
     */
    public static function getRequestArray()
    {
        return Utils::sanitizeInput(self::$request);
    }

    /**
     * Checks if a FILES parameter exists.
     *
     * @param string $param The parameter name.
     * @return bool True if the parameter exists, false otherwise.
     */
    public static function hasFilesParam(string $param): bool
    {
        return self::has(RequestType::FILES, $param);
    }

    /**
     * Retrieves a FILES parameter value.
     *
     * @param string $param The parameter name.
     * @return mixed|null|string The sanitized parameter value.
     */
    public static function getFilesParam(string $param)
    {
        $rt = RequestType::FILES;
        return Utils::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all FILES parameters.
     *
     * @return array|string The sanitized FILES parameters.
     */
    public static function getFilesArray()
    {
        return Utils::sanitizeInput(self::$files);
    }
}
