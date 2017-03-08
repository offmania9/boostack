<?php

class Request
{

    public static function getPostParam($param)
    {
        return isset($_POST) && !empty($_POST[$param]) ? Utils::sanitizeInput($_POST[$param]) : null;
    }

    public static function getQueryParam($param)
    {
        return isset($_GET) && !empty($_GET[$param]) ? Utils::sanitizeInput($_GET[$param]) : null;
    }

    public static function getCookieParam($param)
    {
        return isset($_COOKIE) && !empty($_COOKIE[$param]) ? Utils::sanitizeInput($_COOKIE[$param]) : null;
    }

    public static function getFileParam($param)
    {
        if (isset($_FILES) && !empty($_FILES[$param]) && $_FILES[$param]["size"] > Config::get("max_upload_generalfile_size"))
            return null;
        return $_FILES[$param];
    }

    public static function getRequestParam($param)
    {
        return isset($_REQUEST) && !empty($_REQUEST[$param]) ? Utils::sanitizeInput($_REQUEST[$param]) : null;
    }

    public static function getServerParam($param)
    {
        return isset($_SERVER) && !empty($_SERVER[$param]) ? Utils::sanitizeInput($_SERVER[$param]) : null;
    }

}

?>