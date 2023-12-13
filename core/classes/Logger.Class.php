<?php
/**
 * Boostack: Logger.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 4
 */

class Logger
{

    public static function write($message = "", $level = Log_Level::INFORMATION, $type = Log_Driver::DATABASE)
    {
        switch ($type) {
            case Log_Driver::DATABASE:
                if(Config::get('log_on')) {
                    try {
                        Config::constraint("database_on");
                        $currentUser = Auth::getUserLoggedObject();
                        Log_Database_Writer::getInstance($currentUser)->Log($message, $level);
                    } catch(Exception $e) {
                        Log_File_Writer::getInstance()->log($e, $level);
                        Log_File_Writer::getInstance()->log($message, $level);
                    }
                }
                break;
            case Log_Driver::FILE:
                if (Config::get('log_on'))
                    Log_File_Writer::getInstance()->log($message, $level);
                break;
            default:
                throw new Exception("Log type not found");
        }
    }

}