<?php

/**
 * Boostack: Database_AccessLogger.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */
/**
 * Class Database_AccessLogger
 *
 * Provides functionality to log database access.
 */
class Database_AccessLogger
{
    /** @var string The username accessing the database. */
    private $username;

    /** @var string The IP address of the user accessing the database. */
    private $ip;

    /** @var string The user agent of the client accessing the database. */
    private $useragent;

    /** @var string The referrer URL. */
    private $referrer;

    /** @var string The database query. */
    private $query;

    /** @var PDO The PDO object for database connection. */
    private $pdo;

    /** @var Database_AccessLogger|null The singleton instance of Database_AccessLogger. */
    private static $instance = NULL;

    /** @var string The table name for logging database access. */
    const TABLENAME = "boostack_log";

    /**
     * Database_AccessLogger constructor.
     *
     * @param object|null $objUser The user object (if available).
     */
    private function __construct($objUser = NULL)
    {
        $this->pdo = Database_PDO::getInstance();
        $this->username = (!is_null($objUser)) ? $objUser->id : "Anonymous";
        $this->ip = Utils::getIpAddress();
        $this->useragent = Utils::sanitizeInput(getenv('HTTP_USER_AGENT'));
        $this->referrer = isset($_SERVER["HTTP_REFERER"]) ? Utils::sanitizeInput($_SERVER["HTTP_REFERER"]) : "";
        $this->query = Utils::sanitizeInput(getenv('REQUEST_URI'));
    }

    /**
     * Logs a database access event.
     *
     * @param string|null $message The message to be logged.
     * @param string $level The log level.
     */
    public function log($message = null, $level = "information")
    {
        $enabledTypes = Config::get("log_enabledTypes");

        if (!in_array($level, $enabledTypes)) {
            return;
        }
        $message = $message ?? '';
        if (!empty($message)) {
            $message = preg_replace("/[\r\n]+/", "", $message);
            $message = addslashes($message);
        }
        if (!empty($this->query)) {
            $this->query = preg_replace("/[\r\n]+/", "", $this->query);
        }

        if (!mb_detect_encoding($this->query, 'UTF-8', true)) {
            $this->query = htmlspecialchars($this->query, ENT_QUOTES | ENT_HTML401, 'UTF-8');
        }

        $sql = "INSERT INTO " . self::TABLENAME . "  (id, datetime, level, username, ip, useragent, referrer, query, message)
        VALUES(NULL, :time, :level, :username, :ip, :useragent, :referrer, :query, :message)";
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

    /**
     * Prevents cloning of Database_AccessLogger object.
     */
    private function __clone()
    {
    }

    /**
     * Gets the singleton instance of Database_AccessLogger.
     *
     * @param object|null $objUser The user object (if available).
     * @return Database_AccessLogger|null The singleton instance.
     */
    public static function getInstance($objUser = NULL)
    {
        if (self::$instance == NULL)
            self::$instance = new Database_AccessLogger($objUser);

        return self::$instance;
    }
}
