<?php

class FileLogger {

    private static $instance = NULL;

    private $logFile = __DIR__."/../../logs/log.txt";

    private function __construct()
    {
        $path = dirname($this->logFile);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
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
            throw new Exception("Unable to open log file");
        }
        $message = " [".$level."] ".$message."\n";
        fwrite($logFile, $message);
        fclose($logFile);
    }
}