<?php

/**
 * Class FileLogger
 */
class FileLogger {

    /**
     * @var null
     */
    private static $instance = NULL;
    private static $log_file = NULL;

    /**
     * FileLogger constructor.
     */
    private function __construct()
    {
        $file = Config::get("log_file");
        $path = dirname($file);
        if (!file_exists($path)) {
            if(!is_writable($path)) throw new Exception("Failed to create log directory");
            $mkdirRes = mkdir($path, 0777, true);
            if(!$mkdirRes) throw new Exception("Failed to create log directory");
        }
        self::$log_file = $file;
    }

    /**
     * @return FileLogger|null
     */
    public static function getInstance()
    {
        if (self::$instance == NULL)
            self::$instance = new FileLogger();

        return self::$instance;
    }

    /**
     * @param null $message
     * @param string $level
     * @throws Exception
     */
    public function log($message = NULL, $level = "information")
    {
        $logFile = fopen(self::$log_file, "a");
        if($logFile == false) {
            throw new Exception("Unable to open log file");
        }
        $message = str_replace(array(
            "\r\n",
            "\n",
            "\r"
        ), "", $message);
        $message = addslashes($message);
        $message = " [".$level."] ".$message."\n";
        fwrite($logFile, $message);
        fclose($logFile);
    }
}