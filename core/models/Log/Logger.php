<?php
namespace Core\Models\Log;
use Core\Models\Log\Database\Log_Database_Writer;
use Core\Models\Log\File\Log_File_Writer;
use Core\Models\Config;
use Core\Models\Auth;

/**
 * Boostack: Logger.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 6.0
 */

class Logger
{
    /**
     * Write a log message to the specified log driver.
     *
     * @param string $message The log message to write (default is an empty string).
     * @param int $level The level of the log message (default is Log_Level::INFORMATION).
     * @param int $type The type of log driver to use (default is Log_Driver::DATABASE).
     * @throws \Exception If the log type is not found.
     */
    public static function write($message = "", $level = Log_Level::INFORMATION, $type = Log_Driver::DATABASE)
    {
        switch ($type) {
            case Log_Driver::DATABASE:
                if (Config::get('log_on')) {
                    try {
                        Config::constraint("database_on");
                        $currentUser = Auth::getUserLoggedObject();
                        Log_Database_Writer::getInstance($currentUser)->Log($message, $level);
                    } catch (\Exception $e) {
                        Log_File_Writer::getInstance()->log($e, $level);
                        Log_File_Writer::getInstance()->log($message, $level);
                    }
                }
                break;
            case Log_Driver::FILE:
                if (Config::get('log_on')) {
                    Log_File_Writer::getInstance()->log($message, $level);
                }
                break;
            default:
                throw new \Exception("Log type not found");
        }
    }
}
