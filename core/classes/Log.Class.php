<?php
/**
 * Boostack: Log.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.4
 */
class Log extends BaseClass implements JsonSerializable{
    protected $id;
    protected $level;
    protected $datetime;
    protected $username;
    protected $ip;
    protected $useragent;
    protected $referrer;
    protected $query;
    protected $message;

    const TABLENAME = "boostack_log";

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

    public function __construct($id = NULL) {
        parent::init($id);
    }

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
}
?>