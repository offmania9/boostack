<?php

class Request
{
    private static $instance = NULL;

    static function getInstance()
    {
        if (self::$instance == NULL)
            self::$instance = new Request();
        return self::$instance;
    }

    public function getPostParam($param)
    {
        return !empty($_POST[$param]) ? Utils::sanitizeInput($_POST[$param]) : null;
    }

    public function getQueryParam($param)
    {
        return !empty($_GET[$param]) ? Utils::sanitizeInput($_GET[$param]) : null;
    }

    public function getCookieParam($param)
    {
        return !empty($_COOKIE[$param]) ? Utils::sanitizeInput($_COOKIE[$param]) : null;
    }

    public function getFileParam($param)
    {
        if ($_FILES[$param]["size"] > Config::get("max_upload_generalfile_size"))
            return null;
        return $_FILES[$param];
    }

    public function getRequestParam($param)
    {
        return !empty($_REQUEST[$param]) ? Utils::sanitizeInput($_REQUEST[$param]) : null;
    }

    public function getServerParam($param)
    {
        return !empty($_SERVER[$param]) ? Utils::sanitizeInput($_SERVER[$param]) : null;
    }

}

?>