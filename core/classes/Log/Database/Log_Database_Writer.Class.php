<?php

/**
 * Boostack: Log_Database_Writer.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

/**
 * Class Log_Database_Writer
 *
 * Responsible for writing log entries to the database.
 */
class Log_Database_Writer
{
    /** @var string|null The username associated with the log entry. */
    private $username;

    /** @var string The IP address associated with the log entry. */
    private $ip;

    /** @var string The user agent associated with the log entry. */
    private $useragent;

    /** @var string|null The referrer associated with the log entry. */
    private $referrer;

    /** @var string|null The query associated with the log entry. */
    private $query;

    /** @var PDO The PDO instance for interacting with the database. */
    private $pdo;

    /** @var Log_Database_Writer|null The singleton instance of the Log_Database_Writer class. */
    private static $instance = NULL;

    /** @var string The table name for the log entries. */
    const TABLENAME = "boostack_log";

    /**
     * Retrieves the singleton instance of the Log_Database_Writer class.
     *
     * @param null $objUser The user associated with the log entry.
     * @return Log_Database_Writer|null The singleton instance of the Log_Database_Writer class.
     */
    static function getInstance($objUser = NULL)
    {
        if (self::$instance == NULL)
            self::$instance = new Log_Database_Writer($objUser);
        return self::$instance;
    }

    /**
     * Prevents cloning of the Log_Database_Writer instance.
     */
    private function __clone()
    {
    }

    /**
     * Log_Database_Writer constructor.
     *
     * @param null $objUser The user associated with the log entry.
     */
    private function __construct($objUser = NULL)
    {
        $this->pdo = Database_PDO::getInstance();
        $this->username = (!is_null($objUser)) ? $objUser->id : "Anonymous";
        $this->ip = Utils::getIpAddress();
        $this->useragent = Utils::sanitizeInput(getenv('HTTP_USER_AGENT'));
        $this->referrer = Request::hasServerParam("HTTP_REFERER") ? Request::getServerParam("HTTP_REFERER") : "";
        $this->query = Utils::sanitizeInput(getenv('REQUEST_URI'));
    }

    /**
     * Logs a message with the specified level.
     *
     * @param null $message The log message.
     * @param string $level The log level.
     */
    public function Log($message = NULL, $level = "information")
    {
        if (!in_array($level, Config::get("log_enabledTypes")))
            return;
        $this->query = substr(htmlspecialchars($this->query, ENT_QUOTES | ENT_HTML401, 'UTF-8'), 0, 2048);
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
