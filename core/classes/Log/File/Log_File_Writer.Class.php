<?php

/**
 * Boostack: Log_File_Writer.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 5
 */

/**
 * Class Log_File_Writer
 *
 * This class provides functionality to write log messages to a file.
 */
class Log_File_Writer
{
    /** @var Log_File_Writer|null The singleton instance of the class. */
    private static $instance = NULL;

    /** @var string The path to the log file. */
    private $logFile;

    /**
     * Log_File_Writer constructor.
     *
     * Initializes the log file path.
     */
    private function __construct()
    {
        $path = ROOTPATH . Config::get("log_dir");
        if (!file_exists($path))
            exit("Error: unable to find log dir: $path");
        if (!is_writable($path))
            exit("Error: log dir must be writable");
        $filename = "boostack-" . date("Y-m-d") . ".log";
        $this->logFile = $path . $filename;
    }

    /**
     * Retrieves the singleton instance of the class.
     *
     * @return Log_File_Writer The singleton instance of Log_File_Writer.
     */
    public static function getInstance()
    {
        if (self::$instance == NULL)
            self::$instance = new Log_File_Writer();

        return self::$instance;
    }

    /**
     * Writes a log message to the log file.
     *
     * @param string|null $message The log message to write.
     * @param int $level The level of the log message.
     * @throws Exception Throws an exception if unable to open the log file.
     */
    public function log($message = NULL, $level = Log_Level::INFORMATION)
    {
        $logFile = fopen($this->logFile, "a");
        if ($logFile == false) {
            throw new Exception("Error: Unable to open log file");
        }
        $date = new DateTime();
        $formattedDate = $date->format(DateTime::ATOM);
        $message = "[" . $formattedDate . "] [" . $level . "] " . $message . "\n";
        fwrite($logFile, $message);
        fclose($logFile);
    }
}
