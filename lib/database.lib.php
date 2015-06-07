<?
/**
 * Boostack: database.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

// DATABASE CONFIGS
$data_name = 'boostack';
$host = 'localhost'; #$host = 'production.remote.com';
$username = 'root'; #$username = 'boostack';
$password = ''; #$password = '[DatabasePassword]';
if($boostack->log_on)
    require_once("class/DatabaseAccessLogger.Class.php");
include_once("class/DBMySqlDatabase.Class.php");
$db = DBFactory::CreateDatabaseObject("MySqlDatabase");
$db->Connect($host, $data_name, $username, $password);
?>