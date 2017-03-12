<?php
/**
 * Boostack: Database_AccessLogger.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Database_AccessLogger
{

    /**
     * @var string
     */
    private $username;

    /**
     * @var array|false|string
     */
    private $ip;

    /**
     * @var array|string
     */
    private $useragent;

    /**
     * @var array|string
     */
    private $referrer;

    /**
     * @var array|string
     */
    private $query;

    /**
     * @var null|PDO
     */
    private $pdo;

    /**
     * @var null
     */
    private static $instance = NULL;

    /**
     *
     */
    const TABLENAME = "boostack_log";

    /**
     * Database_AccessLogger constructor.
     * @param null $objUser
     */
    private function __construct($objUser = NULL)
    {
        $this->pdo = Database_PDO::getInstance();
        $this->username = (! is_null($objUser)) ? $objUser->id : "Anonymous";
        $this->ip = Utils::getIpAddress();
        $this->useragent = Utils::sanitizeInput(getenv('HTTP_USER_AGENT'));
        $this->referrer = isset($_SERVER["HTTP_REFERER"]) ? Utils::sanitizeInput($_SERVER["HTTP_REFERER"]) : "";
        $this->query = Utils::sanitizeInput(getenv('REQUEST_URI'));
    }

    /**
     * @param null $message
     * @param string $level
     */
    public function Log($message = NULL, $level = "information")
    {
        if(!in_array($level,Config::get("log_enabledTypes")))
            return;
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

        $this->query = htmlspecialchars($this->query,ENT_QUOTES | ENT_HTML401,'UTF-8');
        $sql = "INSERT INTO " . self::TABLENAME . "  (id ,datetime , level, username, ip ,useragent ,referrer ,query ,message)
				VALUES(NULL,'" . time() . "','".$level."','" . $this->username . "','" . $this->ip . "','" . $this->useragent . "','" . $this->referrer . "','" . $this->query . "','" . $message . "')";
        $this->pdo->prepare($sql)->execute();
    }

    /**
     *
     */
    private function __clone()
    {}

    /**
     * @param null $objUser
     * @return Database_AccessLogger|null
     */
    static function getInstance($objUser = NULL)
    {
        if (self::$instance == NULL)
            self::$instance = new Database_AccessLogger($objUser);
        
        return self::$instance;
    }

    /**
     * @return array
     */
    public function get()
    {
        $sql = "SELECT * FROM " . self::TABLENAME . " ORDER BY datetime DESC";
        $q = $this->pdo->prepare($sql)->execute();
        while ($res = $q->fetch(PDO::FETCH_ASSOC))
            $res2[] = $res['datetime'] . " - " . $res['username'] . " - " . $res['message'] . " - " . $res['ip'] . " - " . substr($res['useragent'], 0, 10) . " - " . $res['query'];
        
        return $res2;
    }
}
?>