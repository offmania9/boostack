<?php
/**
 * Boostack: Log.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Log extends BaseClass implements JsonSerializable{
    /**
     * @var
     */
    protected $level;
    /**
     * @var
     */
    protected $datetime;
    /**
     * @var
     */
    protected $username;
    /**
     * @var
     */
    protected $ip;
    /**
     * @var
     */
    protected $useragent;
    /**
     * @var
     */
    protected $referrer;
    /**
     * @var
     */
    protected $query;
    /**
     * @var
     */
    protected $message;

    /**
     *
     */
    const TABLENAME = "boostack_log";

    /**
     * @var array
     */
    protected $default_values = [
        "id" => "",
        "level" => "information",
        "datetime" => null,
        "username" => null,
        "ip" => "",
        "useragent" => "",
        "referrer" => "",
        "query" => "",
        "message" => "",
    ];

    /**
     * Log constructor.
     * @param null $id
     */
    public function __construct($id = NULL) {
        parent::init($id);
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        $data_log = array();
        $data_log["id"] = $this->id;
        $data_log["level"] = $this->level;
        $data_log["datetime"] =  date('Y-m-d H:i:s', $this->datetime);
        $data_log["username"] = $this->username;
        $data_log["ip"] = $this->ip;
        $data_log["useragent"] = $this->useragent;
        $data_log["referrer"] = $this->referrer;
        $data_log["query"] = $this->query;
        $data_log["message"] = $this->message;

        return $data_log;
    }

    /**
     * @return array
     */
    public function getAttrListForSearch()
    {
        $data_log = array();
        $data_log["id"] = $this->id;
        $data_log["level"] = $this->level;
        $data_log["datetime"] =  date('Y-m-d H:i:s', $this->datetime);
        $data_log["username"] = $this->username;
        $data_log["ip"] = $this->ip;
        $data_log["useragent"] = $this->useragent;
        $data_log["referrer"] = $this->referrer;
        $data_log["query"] = $this->query;
        $data_log["message"] = $this->message;

        return $data_log;

    }

    public static function write($logMesg = "", $level = LogLevel::Information, $type = LogType::DB) {
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