<?php
/**
 * Boostack: Database_PDO.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Database_PDO
{

    private static $instance = null;

    private function __construct()
    {}

    private function __clone()
    {}

    public static function getInstance($host = null, $db = null, $username = null, $password = null)
    {

        try {
            if (self::$instance === null) {
                self::$instance = new PDO('mysql:host=' . $host . ';dbname=' . $db, $username, $password, array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ));
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$instance;
        }
        catch(PDOException $e){
            $boostack = Boostack::getInstance();
            //$boostack->setConfig("database_on",FALSE);
            // WRITE into log file
            if(!$boostack->getConfig("developmentMode")){// go to mantainance page
                Utils::goToMaintenance();
            }
            else {
                echo "An error occurred connection:". $e->getMessage()."<br/";
                exit();
            }
        }

    }
}

?>