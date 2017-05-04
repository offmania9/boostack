<?php
/**
 * Boostack: Boostack.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Boostack
{
    /**
     * @param string $logMesg
     * @param string $level
     * @param string $type
     * @throws Exception
     */
    public function writeLog($logMesg = "", $level = LogLevel::Information, $type = LogType::DB) {
        global $CURRENTUSER;
        switch ($type) {
            case LogType::DB:
                if (Config::get('database_on') && Config::get('log_on'))
                    Database_AccessLogger::getInstance($CURRENTUSER)->Log($logMesg, $level);
                break;
            case LogType::File:
                if (Config::get('log_on'))
                    FileLogger::getInstance()->log($logMesg, $level);
                break;
            default:
                throw new Exception("Log type not found");
        }
    }

}
?>