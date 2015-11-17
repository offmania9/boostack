<?php

class Database_PDO
{

    private static $instance = null;

    private function __construct()
    {}

    private function __clone()
    {}

    public static function getInstance($host = null, $db = null, $username = null, $password = null)
    {
        if (self::$instance === null) {
            self::$instance = new PDO('mysql:host=' . $host . ';dbname=' . $db, $username, $password, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}

?>