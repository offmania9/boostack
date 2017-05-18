<?php
/**
 * Boostack: Log_File_Writer.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 3.1
 */

class Log_File_Writer
{

    private static $instance = NULL;

    private $logFile;

    private function __construct()
    {
        $this->logFile = ROOTPATH.Config::get("log_file");
        $path = dirname($this->logFile);
        if (!file_exists($path))
            exit("Error: unable to find log dir");
        if(!is_writable($path))
            exit("Error: log dir must be writable");
    }

    public static function getInstance()
    {
        if (self::$instance == NULL)
            self::$instance = new Log_File_Writer();

        return self::$instance;
    }

    public function log($message = NULL, $level = Log_Level::INFORMATION)
    {
        $logFile = fopen($this->logFile, "a");
        if($logFile == false) {
            exit("Error: Unable to open log file");
        }
        $charRemoved = array("\r\n", "\n", "\r");
        $message = str_replace($charRemoved, "", $message);
//        //$message = addslashes($message);
        $date = new DateTime();
        $formattedDate = $date->format(DateTime::ATOM);
        $message = "[".$formattedDate."] [".$level."] ".$message."\n";
        fwrite($logFile, $message);
        fclose($logFile);
    }
}