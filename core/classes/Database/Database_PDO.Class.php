<?php

/**
 * Boostack: Database_PDO.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5
 */

/**
 * Class Database_PDO
 *
 * Represents a PDO connection to the database.
 */
class Database_PDO
{
    /** @var PDO|null The singleton instance of PDO. */
    private static $instance = null;

    /**
     * Prevents direct instantiation of Database_PDO.
     */
    private function __construct()
    {
    }

    /**
     * Prevents cloning of Database_PDO object.
     */
    private function __clone()
    {
    }

    /**
     * Retrieves the singleton instance of PDO.
     *
     * @param string|null $host The database host.
     * @param string|null $dbname The database name.
     * @param string|null $username The database username.
     * @param string|null $password The database password.
     * @param int $port The port number (default is 3306).
     * @return PDO|null The PDO instance.
     */
    public static function getInstance($host = null, $dbname = null, $username = null, $password = null, $port = 3306)
    {
        if (self::$instance === null) {
            self::$instance = self::createInstance($host, $dbname, $username, $password, $port);
        }
        return self::$instance;
    }

    /**
     * Creates a new PDO instance.
     *
     * @param string|null $host The database host.
     * @param string|null $dbname The database name.
     * @param string|null $username The database username.
     * @param string|null $password The database password.
     * @param int $port The port number (default is 3306).
     * @return PDO The PDO instance.
     * @throws PDOException If connection to the database fails.
     */
    private static function createInstance($host, $dbname, $username, $password, $port)
    {
        try {
            Config::constraint("database_on");
            $connection_string = Config::get("driver_pdo") . ':host=' . $host;
            $connection_string .= $port !== null ? ';port=' . $port : '';
            $connection_string .= $dbname !== null ? ';dbname=' . $dbname : '';

            $pdo = new PDO($connection_string, $username, $password, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            $message = "See log file. An error occurred in DB connection:" . $e->getMessage() . $e->getTraceAsString() . "\n";
            Logger::write($message, Log_Level::ERROR, Log_Driver::FILE);
            throw new PDOException($e);
        }
    }
}
