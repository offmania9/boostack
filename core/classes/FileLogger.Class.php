<?php

class FileLogger {

    private static $instance = NULL;

    private $logFile = __DIR__."/../../logs/log.txt";

    private function __construct()
    {
        $path = dirname($this->logFile);
        if (!file_exists($path))
            exit("Error: unable to find log dir");
        if(!is_writable($path))
            exit("Error: log dir must be writable");
    }

    public static function getInstance()
    {
        if (self::$instance == NULL)
            self::$instance = new FileLogger();

        return self::$instance;
    }

    public function log($message = NULL, $level = "information")
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