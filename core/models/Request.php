<?php

namespace Core\Models;
use Core\Models\Session\Session;

/**
 * Boostack: Request.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
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
        return self::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all POST parameters.
     *
     * @return array|string The sanitized POST parameters.
     */
    public static function getPostArray()
    {
        return self::sanitizeInput(self::$post);
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
        return self::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all QUERY parameters.
     *
     * @return array|string The sanitized QUERY parameters.
     */
    public static function getQueryArray()
    {
        return self::sanitizeInput(self::$query);
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
        return self::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all SERVER parameters.
     *
     * @return array|string The sanitized SERVER parameters.
     */
    public static function getServerArray()
    {
        return self::sanitizeInput(self::$server);
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
        return self::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all HEADER parameters.
     *
     * @return array|string The sanitized HEADER parameters.
     */
    public static function getHeaderArray()
    {
        return self::sanitizeInput(self::$headers);
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
        return self::sanitizeInput(self::get($rt, $param));
    }
    /**
     * Retrieves all COOKIE parameters.
     *
     * @return array|string The sanitized COOKIE parameters.
     */
    public static function getCookieArray()
    {
        return self::sanitizeInput(self::$cookie);
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
        return self::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all REQUEST parameters.
     *
     * @return array|string The sanitized REQUEST parameters.
     */
    public static function getRequestArray()
    {
        return self::sanitizeInput(self::$request);
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
        return self::sanitizeInput(self::get($rt, $param));
    }

    /**
     * Retrieves all FILES parameters.
     *
     * @return array|string The sanitized FILES parameters.
     */
    public static function getFilesArray()
    {
        return self::sanitizeInput(self::$files);
    }

    /**
     * Redirects to the maintenance page.
     */
    public static function goToMaintenance()
    {
        header("Location: " . Config::get("url") . Config::get("url_maintenance"));
        exit();
    }

    /**
     * Generates a friendly URL based on the virtual path provided.
     *
     * @param string $virtualPath The virtual path.
     * @return string The friendly URL.
     */
    public static function getFriendlyUrl($virtualPath)
    {
        if (Config::get('session_on')) {
            $langUrl = Session::get("SESS_LANGUAGE") . "/";
            if (!Config::get('show_default_language_in_URL') && Session::get("SESS_LANGUAGE") == Config::get('language_default'))
                $langUrl = "";
            return Config::get('url') . $langUrl . $virtualPath;
        }
        return Config::get('url') . $virtualPath;
    }


    /**
     * Redirects the user to the specified URL.
     *
     * @param string $URL The URL to redirect to.
     */
    public static function goToUrl($URL)
    {
        header("Location: " . $URL);
        exit();
    }

    /**
     * Redirects the user to the home page.
     */
    public static function goToHome()
    {
        header("Location: " . Config::get("url"));
        exit();
    }

    /**
     * Redirects the user to the error page.
     *
     * @param int|null $status_code The HTTP status code to be used for the error page.
     */
    public static function goToError(int $status_code = NULL)
    {
        header("Location: " . Config::get("url") . "error/" . (empty($status_code) ? "" : $status_code));
        exit();
    }

    /**
     * Redirects the user to the logout page.
     */
    public static function goToLogout()
    {
        header("Location: " . Config::get("url") . "logout");
        exit();
    }


    /**
     * Retrieves the User-Agent string from the request headers.
     *
     * @return array|string The User-Agent string.
     */
    public static function getUserAgent()
    {
        return self::getServerParam("HTTP_USER_AGENT");
    }

    /**
     * Retrieves the IP address of the client.
     *
     * @return array|false|string The IP address of the client.
     */
    public static function getIpAddress()
    {
        $ip = getenv('HTTP_CLIENT_IP') ?:
            getenv('HTTP_X_FORWARDED_FOR') ?:
            getenv('HTTP_X_FORWARDED') ?:
            getenv('HTTP_FORWARDED_FOR') ?:
            getenv('HTTP_FORWARDED') ?:
            getenv('REMOTE_ADDR');
        return $ip;
    }

    /**
     * Sanitizes input data to prevent XSS attacks.
     *
     * @param array|string $array The input data to be sanitized.
     * @param string $encoding The character encoding (default is 'UTF-8').
     * @return array|string The sanitized input data.
     */
    public static function sanitizeInput($data, $encoding = 'UTF-8')
    {
        if (is_array($data)) {
            return array_map(function ($value) use ($encoding) {
                return self::sanitizeInput($value, $encoding);
            }, $data);
        } elseif ($data !== null) {
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
        } else {
            return $data;
        }
    }



    /*
 * Generates the value of the remember-me cookie.
 */
    /**
     * Generates a hash for the remember-me cookie based on current time, IP address, and user agent.
     *
     * @return string The generated cookie hash.
     */
    public static function generateCookieHash()
    {
        return  md5(time()) . md5(self::getIpAddress() . self::getUserAgent());
    }

    /**
     * Checks the validity of the remember-me cookie hash.
     *
     * @param string $cookieValue The value of the remember-me cookie.
     * @return bool True if the cookie hash is valid, false otherwise.
     */
    public static function checkCookieHashValidity($cookieValue)
    {
        return substr($cookieValue, 32) == md5(self::getIpAddress() . self::getUserAgent());
    }

        /**
     * Checks if the time since the last request is within the accepted time limit.
     *
     * @param int|string $timeLastRequest The time of the last request.
     * @return bool Returns true if the time since the last request is within the accepted time limit, false otherwise.
     */
    public static function checkAcceptedTimeFromLastRequest($timeLastRequest)
    {
        if (!is_numeric($timeLastRequest))
            return true;
        $secondsAccepted = Config::get("seconds_accepted_between_requests");
        if ((!empty($timeLastRequest) || $timeLastRequest !== null) && (time() - $timeLastRequest >= $secondsAccepted))
            return true;
        return false;
    }
}
