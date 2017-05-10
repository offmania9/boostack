<?php
/**
 * Boostack: Log_Database_Writer.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Log_Database_Writer
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
     * @param null $objUser
     * @return Log_Database_Writer|null
     */
    static function getInstance($objUser = NULL)
    {
        if (self::$instance == NULL)
            self::$instance = new Log_Database_Writer($objUser);
        return self::$instance;
    }

    /**
     *
     */
    private function __clone()
    {}

    /**
     * Log_Database_Writer constructor.
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
				VALUES(NULL, :time , :level, :username, :ip , :useragent, :referrer, :query, :message)";
        $q = $this->pdo->prepare($sql);
        $q->bindValue(':time', time());
        $q->bindValue(':level', $level);
        $q->bindValue(':username', $this->username);
        $q->bindValue(':ip', $this->ip);
        $q->bindValue(':useragent', $this->useragent);
        $q->bindValue(':referrer', $this->referrer);
        $q->bindValue(':query', $this->query);
        $q->bindValue(':message', $message);
        $q->execute();
    }

}
?>