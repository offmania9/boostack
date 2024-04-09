<?php
namespace Core\Models\Log\Database;
/**
 * Boostack: Log_Database_Entity.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

/**
 * Class Log_Database_Entity
 *
 * Represents a log entry stored in the database.
 */
class Log_Database_Entity extends \Core\Models\BaseClass
{
    /** @var string The log level. */
    protected $level;

    /** @var string The date and time of the log entry. */
    protected $datetime;

    /** @var string|null The username associated with the log entry. */
    protected $username;

    /** @var string The IP address associated with the log entry. */
    protected $ip;

    /** @var string The user agent associated with the log entry. */
    protected $useragent;

    /** @var string|null The referrer associated with the log entry. */
    protected $referrer;

    /** @var string|null The query associated with the log entry. */
    protected $query;

    /** @var string The log message. */
    protected $message;

    /** @var string The table name for the log entity. */
    const TABLENAME = "boostack_log";

    /** @var array The default values for the log entity attributes. */
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
     * Log_Database_Entity constructor.
     * @param null $id
     */
    public function __construct($id = NULL)
    {
        parent::init($id);
    }

    /**
     * Serializes the object to a JSON array.
     *
     * @return array The serialized JSON array.
     */
    public function jsonSerialize(): mixed
    {
        $data_log = array();
        $data_log["id"] = $this->id;
        $data_log["level"] = $this->level;
        $data_log["datetime"] = $this->datetime;
        $data_log["username"] = $this->username;
        $data_log["ip"] = $this->ip;
        $data_log["useragent"] = $this->useragent;
        $data_log["referrer"] = $this->referrer;
        $data_log["query"] = $this->query;
        $data_log["message"] = $this->message;

        return $data_log;
    }

    /**
     * Retrieves the attribute list for search.
     *
     * @return array The attribute list for search.
     */
    public function getAttrListForSearch()
    {
        $data_log = array();
        $data_log["id"] = $this->id;
        $data_log["level"] = $this->level;
        $data_log["datetime"] = $this->datetime;
        $data_log["username"] = $this->username;
        $data_log["ip"] = $this->ip;
        $data_log["useragent"] = $this->useragent;
        $data_log["referrer"] = $this->referrer;
        $data_log["query"] = $this->query;
        $data_log["message"] = $this->message;

        return $data_log;
    }
}
