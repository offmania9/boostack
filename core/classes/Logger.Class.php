<?php

class Logger {

    const DRIVER_FILE = "log_driver_file";
    const DRIVER_DATABASE = "log_driver_database";

    const LEVEL_ERROR = "error";
    const LEVEL_WARNING = "warning";
    const LEVEL_FAILURE = "failure";
    const LEVEL_INFORMATION = "information";
    const LEVEL_SUCCESS = "success";
    const LEVEL_USER = "user";
    const LEVEL_CRONJOB = "cronjob";

    public static function write($message = "", $level = self::LEVEL_INFORMATION, $type = self::DRIVER_DATABASE) {
        switch ($type) {
            case self::DRIVER_DATABASE:
                if (Config::get('database_on') && Config::get('log_on')) {
                    $currentUser = Auth::getUserLoggedObject();
                    Log_Database::getInstance($currentUser)->Log($message, $level);
                }
                break;
            case self::DRIVER_FILE:
                if (Config::get('log_on'))
                    Log_File::getInstance()->log($message, $level);
                break;
            default:
                throw new Exception("Log type not found");
        }
    }

}