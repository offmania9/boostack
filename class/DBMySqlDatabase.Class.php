<?php

require_once("DBDatabase.Class.php");

class MySqlDatabase extends Database
{
    protected $connection;
    protected $host;
    protected $database;
    protected $user;
    protected $password;

    function __construct($host = "", $database = "", $user = "", $password = "")
    {
        if ($host != "" && $database != "" && $user != "")
            $this->Connect($host, $database, $user, $password);
    }

    function CloneObject()
    {
        return new MySqlDatabase();
    }

    function GetName()
    {
        return "MySqlDatabase";
    }

    function Connect($host, $database, $user, $password)
    {
        $this->connection = @mysql_connect($host, $user, $password) or $this->ThrowException(mysql_error());
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;

        @mysql_select_db($database, $this->connection) or $this->ThrowException(mysql_error());
        $this->database = $database;
    }

    function Close()
    {
        if (isset($this->connection))
        {
            @mysql_close($this->connection) or $this->ThrowException(mysql_error());
            unset($this->connection);
        }
    }

    function IsOpen()
    {
        return isset($this->connection);
    }

    function Execute($sql)
    {
        if (!isset($this->connection)) $this->ThrowException("Database connection is not valid");
        $result = @mysql_query($sql, $this->connection) or $this->ThrowException(mysql_error());

        if (!$result) return;
        if (!@mysql_num_fields($result))
            return;
        else
        {
            $data = array();
            while (($row = mysql_fetch_array($result))) $data[] = $row;
            return new DataTable($data);
        }
    }

    function ExecuteRaw($sql)
    {
        if (!isset($this->connection)) throw new Exception("Database connection is not valid");
        return @mysql_query($sql, $this->connection) or $this->ThrowException(mysql_error());
    }

    function Begin()
    {
        $this->Execute("SET AUTOCOMMIT=0");
        $this->Execute("START TRANSACTION");
    }
    
    function Commit()
    {
        $this->Execute("COMMIT");
        $this->Execute("SET AUTOCOMMIT=1");
    }
    
    function Rollback()
    {
        $this->Execute("ROLLBACK");
        $this->Execute("SET AUTOCOMMIT=1");
    }

    function GetLastInsertID()
    {
        // I tried mysql_insert_id() but it doesn't work.
        // Can anybody tell me why?
        $row = $this->Execute("SELECT LAST_INSERT_ID() AS id")->Row(0);
        return $row["id"];
    }
};

// Register in DBFactory
DBFactory::RegisterDatabase(new MySqlDatabase());

?>