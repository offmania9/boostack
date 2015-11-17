<?php

/**
 * Boostack: Database_AccessLogger.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
 */
class Database_AccessLogger
{

    private $username;

    private $ip;

    private $useragent;

    private $referrer;

    private $query;
    // private $message;
    // private $date;
    // private $time;
    // private $pdo;
    private static $instance = NULL;

    const TABLENAME = "boostack_log";

    private function __construct($objSession = NULL)
    {
        $this->username = (! is_null($objSession)) ? $objSession->GetUserID() : "Anonymous";
        $this->ip = getIpAddress();
        $this->useragent = sanitizeInput(getenv('HTTP_USER_AGENT'));
        $this->referrer = isset($_SERVER["HTTP_REFERER"]) ? sanitizeInput($_SERVER["HTTP_REFERER"]) : "";
        $this->query = sanitizeInput(getenv('REQUEST_URI'));
    }

    public function Log($message = NULL)
    {
        $message = str_replace(array(
            "\r\n",
            "\n",
            "\r"
        ), "", $message);
        $message = addslashes($message);
        $this->query = str_replace(array(
            "\r\n",
            "\n",
            "\r"
        ), "", $this->query);
        $this->query = addslashes($this->query);
        $sql = "INSERT INTO " . self::TABLENAME . "  (id ,datetime , username, ip ,useragent ,referrer ,query ,message)
				VALUES(NULL,'" . time() . "','" . $this->username . "','" . $this->ip . "','" . $this->useragent . "','" . $this->referrer . "','" . $this->query . "','" . $message . "')";
        Database_PDO::getInstance()->prepare($sql)->execute();
    }

    private function __clone()
    {}

    static function getInstance($objSession = NULL)
    {
        if (self::$instance == NULL)
            self::$instance = new Database_AccessLogger($objSession);
        
        return self::$instance;
    }

    public function get()
    {
        $sql = "SELECT * FROM " . self::TABLENAME . " ORDER BY datetime DESC";
        $q = Database_PDO::getInstance()->prepare($sql)->execute();
        while ($res = $q->fetch(PDO::FETCH_ASSOC))
            $res2[] = $res['datetime'] . " - " . $res['username'] . " - " . $res['message'] . " - " . $res['ip'] . " - " . substr($res['useragent'], 0, 10) . " - " . $res['query'];
        
        return $res2;
    }
}
?>