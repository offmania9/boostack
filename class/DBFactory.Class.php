<?php

require_once("DBDatabase.Class.php");

class DBFactory
{
    private static $dbObjects = array();

    // Register a database class instance
    static function RegisterDatabase($dbObject)
    {
        if (array_key_exists($dbObject->GetName(), DBFactory::$dbObjects))
            throw new Exception("Database object is already registered.");
        DBFactory::$dbObjects[$dbObject->GetName()] = $dbObject;
    }

    // Unregister a database class instance
    static function UnRegisterDatabase($dbObject)
    {
        if (!array_key_exists($dbObject->GetName(), DBFactory::$dbObjects))
            throw new Exception("Database object is not yet registered.");
        unset(DBFactory::$dbObjects[$dbObject->GetName()]);
        $arr = array($dbObject->GetName() => $dbObject);
        DBFactory::$dbObjects = array_diff_key(DBFactory::$dbObjects, $arr);
    }

    // Unregister all database objects
    static function UnRegisterAllDatabases()
    {
        DBFactory::$dbObjects = array();
    }

    // Create a database class instance from a registered database object.
    static function CreateDatabaseObject($name)
    {
        if (!array_key_exists($name, DBFactory::$dbObjects))
            throw new Exception("Database with name ($name) is not yet registered.");
        return DBFactory::$dbObjects[$name]->CloneObject();
    }
};

?>