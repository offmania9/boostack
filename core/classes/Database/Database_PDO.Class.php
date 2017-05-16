<?php
/**
 * Boostack: Database_PDO.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.1
 */

class Database_PDO
{

    /**
     * @var null
     */
    private static $instance = null;


    /**
     * Database_PDO constructor.
     */
    private function __construct()
    {}

    /**
     *
     */
    private function __clone()
    {}

    /**
     * @param null $host
     * @param null $db
     * @param null $username
     * @param null $password
     * @return null|PDO
     */
    public static function getInstance($host = null, $db = null, $username = null, $password = null)
    {
        try {
            if (self::$instance === null) {
                Config::constraint("database_on");
                self::$instance = new PDO(Config::get("driver_pdo").':host=' . $host . ';dbname=' . $db, $username, $password, array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$instance;
        } catch(PDOException $e){
            Logger::write($e,Logger::LEVEL_ERROR, Logger::DRIVER_FILE);
            if(!Config::get("developmentMode")){// go to mantainance page
                Utils::goToMaintenance();
            }
            else {
                echo "An error occurred connection:". $e->getMessage()."<br/>";
                exit();
            }
        }
    }
}

?>