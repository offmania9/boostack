<?php

namespace Core\Models;

use Core\Models\Database\Database_PDO;
use Core\Models\Log\Log_Driver;
use Core\Models\Log\Log_Level;

/**
 * Boostack: BaseClass.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */
abstract class BaseClass implements \JsonSerializable
{

    protected $id;

    protected $PDO; // TODO: static? Find a way to automatically inject db instance into this field?
    /**
     * @var array
     */
    protected $default_values = [];
    /**
     * @var array
     */
    protected $system_excluded = ['id', 'default_values', 'system_excluded', 'custom_excluded', 'PDO', 'soft_delete', 'deleted_at'];
    /**
     * @var array
     */
    protected $custom_excluded = [];
    /**
     *
     */
    protected $soft_delete = FALSE;
    /**
     *
     */
    const TABLENAME = "";

    /**
     * Initialize the object by creating the \PDO object.
     * Call this method in your __construct() function with parent::init()
     *
     * @param int|null $id The ID of the object to load. Default is NULL.
     */
    protected function init($id = NULL)
    {
        $this->PDO = Database_PDO::getInstance();
        if ($id !== NULL) {
            $this->load($id);
        } else {
            $this->prepare();
        }
    }

    /**
     * Fill the object with data from an array.
     *
     * @param array $array The array containing data to fill the object.
     * @return bool Always returns true.
     * @throws \Exception If an error occurs during preparation.
     */
    public function fill($array)
    {
        $this->prepare($array);
        return true;
    }

    /**
     * Clear the object and fill it with data from an array.
     *
     * @param array $array The array containing data to fill the object.
     * @return bool Always returns true.
     */
    public function clearAndFill($array)
    {
        $defaultValuesKeys = array_keys($this->default_values);
        $inputKeys = array_keys($array);
        $fieldsNotPresent = array_diff($inputKeys, $defaultValuesKeys, $this->system_excluded, $this->custom_excluded);
        if (count($fieldsNotPresent) > 0) {
            foreach ($fieldsNotPresent as $value)
                unset($array[$value]);
        }
        return $this->fill($array);
    }


    /**
     * Load the object from the database by its ID.
     *
     * @param int $id The ID of the object to load.
     * @return bool Returns true if the object was successfully loaded, otherwise throws an \Exception.
     * @throws \Exception If no result found or if a database \Exception occurs.
     */
    public function load($id)
    {
        try {
            $SD_sql = ($this->hasSoftDelete()) ? "AND deleted_at IS NULL" : "";
            $sql = "SELECT * FROM " . static::TABLENAME . " WHERE id = :id " . $SD_sql;
            $q = $this->PDO->prepare($sql);
            $q->bindValue(':id', $id);
            $q->execute();
            $result = $q->fetch(\PDO::FETCH_ASSOC);
            if (empty($result)) {
                throw new \Exception("No result found in table " . static::TABLENAME . " with ID " . $id);
            }
            $this->prepare($result);
            $this->id = $id;
            return true;
        } catch (\PDOException $PDOEx) {
            Log\Logger::write($PDOEx, Log_Level::ERROR, Log_Driver::FILE);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }

    /**
     * Check if an object with the specified ID exists in the database.
     *
     * @param int $id The ID to check.
     * @return bool Returns true if the object exists, otherwise false.
     * @throws \PDOException If a database \Exception occurs.
     */
    public static function exist($id)
    {
        try {
            $current_object = new static();
            $SD_sql = ($current_object->hasSoftDelete()) ? "AND deleted_at IS NULL" : "";
            $PDO = Database_PDO::getInstance();
            $sql = "SELECT id FROM " . static::TABLENAME . " WHERE id = :id " . $SD_sql;
            $q = $PDO->prepare($sql);
            $q->bindValue(':id', $id);
            $q->execute();
            $result = $q->fetch(\PDO::FETCH_ASSOC);
            return !empty($result);
        } catch (\PDOException $PDOEx) {
            Log\Logger::write($PDOEx, Log_Level::ERROR, Log_Driver::FILE);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }

    /**
     * Save the object into the database.
     *
     * @param int|null $forcedID If not null, insert the entity with $forceID as ID.
     * @return bool Returns true if the object was successfully saved, otherwise throws an \Exception.
     * @throws \PDOException If a database \Exception occurs.
     */
    public function save($forcedID = null)
    {
        try {
            if (empty($this->id)) {
                if (empty($forcedID)) {
                    return $this->insert();
                }
                return $this->insertWithID($forcedID);
            } else {
                return $this->update();
            }
        } catch (\PDOException $PDOEx) {
            Log\Logger::write($PDOEx, Log_Level::ERROR, Log_Driver::FILE);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }

    /**
     * Determine if the model is currently using soft delete.
     *
     * @return bool Returns true if soft delete is enabled, otherwise false.
     * @throws \PDOException If soft delete is enabled but the 'deleted_at' field is missing from the database.
     */
    public function hasSoftDelete()
    {
        if ($this->soft_delete && !property_exists($this, "deleted_at")) {
            throw new \PDOException("Soft delete is enabled, but the 'deleted_at' field is missing from the database.");
        }
        return $this->soft_delete;
    }

    /**
     * Set soft delete status.
     *
     * @param bool $value The value to set for soft delete status.
     */
    public function setSoftDelete(bool $value)
    {
        $this->soft_delete = $value;
    }

    /**
     * Enable soft delete.
     */
    public function enableSoftDelete()
    {
        $this->setSoftDelete(true);
    }

    /**
     * Disable soft delete.
     */
    public function disableSoftDelete()
    {
        $this->setSoftDelete(false);
    }

    /**
     * Delete the object from the database (set 'deleted_at' field to non-null).
     *
     * @return bool Returns true if the object was successfully soft deleted, otherwise purges the object.
     */
    public function delete()
    {
        if ($this->hasSoftDelete())
            $this->softDelete();
        else
            $this->purge();
    }
    /**
     * Soft delete the object from the database (set 'deleted_at' field to current timestamp).
     *
     * @return bool Returns true if the object was successfully soft deleted, otherwise false.
     * @throws \PDOException If a database error occurs.
     */
    public function softDelete()
    {
        try {
            $sql = "UPDATE " . static::TABLENAME . " SET deleted_at = NOW() WHERE id = :id";
            $q = $this->PDO->prepare($sql);
            $q->bindValue(':id', $this->id);
            $q->execute();
            return ($q->rowCount() > 0);
        } catch (\PDOException $PDOEx) {
            Log\Logger::write($PDOEx);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }

    /**
     * Restore the object (set 'deleted_at' field to NULL).
     *
     * @return bool Returns true if the object was successfully restored, otherwise false.
     * @throws \PDOException If a database error occurs.
     */
    public function restore()
    {
        try {
            $sql = "UPDATE " . static::TABLENAME . " SET deleted_at = NULL WHERE id = :id";
            $q = $this->PDO->prepare($sql);
            $q->bindValue(':id', $this->id);
            $q->execute();
            return ($q->rowCount() > 0);
        } catch (\PDOException $PDOEx) {
            Log\Logger::write($PDOEx);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }

    /**
     * Purge the object from the database (permanently delete).
     *
     * @return bool Returns true if the object was successfully purged, otherwise false.
     * @throws \PDOException If a database error occurs.
     */
    public function purge()
    {
        try {
            $sql = "DELETE FROM " . static::TABLENAME . " WHERE id = :id";
            $q = $this->PDO->prepare($sql);
            $q->bindValue(':id', $this->id);
            $q->execute();
            return ($q->rowCount() > 0);
        } catch (\PDOException $PDOEx) {
            Log\Logger::write($PDOEx);
            throw new \PDOException("Database \Exception. Please see log file.");
        }
    }

    /**
     * Getter magic method.
     *
     * @param string $property_name The name of the property to get.
     * @return mixed The value of the property.
     * @throws \Exception If the property does not exist.
     */
    public function __get($property_name)
    {
        if (property_exists($this, $property_name)) {
            return $this->$property_name;
        } else {
            throw new \Exception("Field $property_name not found");
        }
    }

    /**
     * Setter magic method.
     *
     * @param string $property_name The name of the property to set.
     * @param mixed $val The value to set.
     * @throws \Exception If the property does not exist.
     */
    public function __set($property_name, $val)
    {
        if (property_exists($this, $property_name)) {
            $this->$property_name = $val;
        } else {
            throw new \Exception("Field $property_name not found");
        }
    }

    /**
     * Magic method used for isset() and empty() methods invoked outside the object on a protected/private field.
     *
     * @param string $property_name The name of the property to check.
     * @return bool Returns true if the property is set, otherwise false.
     */
    public function __isset($property_name)
    {
        return isset($this->$property_name);
    }

    /**
     * Get the object vars used in serialize() method.
     *
     * @return array Returns an array of object variables to serialize.
     */
    public function __sleep()
    {
        $objVars = get_object_vars($this);
        $objVarsExported = array();
        foreach ($objVars as $key => $value) {
            if (in_array($key, $this->system_excluded) || in_array($key, $this->custom_excluded)) continue;
            $objVarsExported[] = $key;
        }
        return $objVarsExported;
    }

    /**
     * Task performed after the call of unserialize() method.
     * e.g. reestablish any database connections, reinitialization tasks, etc.
     */
    public function __wakeup()
    {
        $this->PDO = Database_PDO::getInstance();
    }

    /**
     * This method is used when json_encode() is called.
     * It exposes all the variables of the object to the json_encode() function.
     *
     * @return mixed Returns an array of object variables to serialize.
     */
    public function jsonSerialize(): mixed
    {
        $objVars = get_object_vars($this);
        $objVarsExported = array();
        foreach ($objVars as $key => $value) {
            if (in_array($key, $this->system_excluded) || in_array($key, $this->custom_excluded)) continue;
            $objVarsExported[$key] = $value;
        }
        return $objVarsExported;
    }

    /**
     * Lock the table for read and write operations.
     */
    public function lockTable()
    {
        $sql = "LOCK TABLES " . static::TABLENAME . " WRITE";
        $result = $this->PDO->prepare($sql);
        $result->execute();
    }

    /**
     * Release all the locks for all the tables.
     */
    public function unlockTable()
    {
        $sql = "UNLOCK TABLES";
        $result = $this->PDO->prepare($sql);
        $result->execute();
    }

    /**
     * Return the object database table.
     *
     * @return string The name of the database table.
     */
    public function getTablename()
    {
        return static::TABLENAME;
    }

    /**
     * Return the list of object attributes.
     *
     * @return array An array containing the attributes of the object.
     */
    public function getAttributes()
    {
        $objVars = get_object_vars($this);
        $attributes = array();
        foreach ($objVars as $key => $value) {
            if (in_array($key, $this->system_excluded) || in_array($key, $this->custom_excluded)) continue;
            $attributes[] = $key;
        }
        return $attributes;
    }

    /**
     * Prepare the object for database operations.
     *
     * @param array $array An array containing the data to prepare.
     * @throws \Exception If required fields are missing.
     */
    protected function prepare($array = array())
    {
        $defaultValuesKeys = array_keys($this->default_values);
        $inputKeys = array_keys($array);
        $fieldsNotPresent = array_diff($inputKeys, $defaultValuesKeys, $this->system_excluded, $this->custom_excluded);
        if (count($fieldsNotPresent) > 0) {
            throw new \Exception(implode(",", $fieldsNotPresent) . " are not found in object" . json_encode($inputKeys));
        }
        if (!empty($array['id'])) $this->id = $array['id'];
        foreach ($defaultValuesKeys as $defaultField) {
            if (in_array($defaultField, $inputKeys)) {
                $this->$defaultField = $array[$defaultField];
            } else {
                $this->$defaultField = $this->default_values[$defaultField];
            }
        }
    }

    /**
     * Insert the object into the database.
     *
     * @return bool Returns true on successful insertion, false otherwise.
     */
    private function insert()
    {
        $objVars = get_object_vars($this);

        $firstPartOfQuery = "INSERT INTO " . static::TABLENAME . " (id";
        $secondPartOfQuery = "VALUES(NULL";

        foreach ($objVars as $key => $value) {
            if (in_array($key, $this->system_excluded) || in_array($key, $this->custom_excluded)) continue;
            $firstPartOfQuery .= ",$key";
            $secondPartOfQuery .= ",:$key";
        }

        $firstPartOfQuery .= ") ";
        $secondPartOfQuery .= ")";

        $query = $firstPartOfQuery . $secondPartOfQuery;
        $q = $this->PDO->prepare($query);

        foreach ($objVars as $key => &$value) {
            if (in_array($key, $this->system_excluded) || in_array($key, $this->custom_excluded)) continue;
            $q->bindParam(":" . $key, $value);
        }

        $q->execute();

        $this->id = $this->PDO->lastInsertId();
        return true;
    }

    /**
     * Insert the object with a specific ID into the database.
     *
     * @param $id The ID to insert the object with.
     * @return bool Returns true on successful insertion, false otherwise.
     */
    protected function insertWithID($id)
    {
        $objVars = get_object_vars($this);
        $objVars["id"] = $id;

        $system_excluded_without_id = array_diff($this->system_excluded, ["id"]);
        $firstPartOfQuery = "INSERT INTO " . static::TABLENAME . " (";
        $secondPartOfQuery = "VALUES(";

        foreach ($objVars as $key => $value) {
            if (in_array($key, $system_excluded_without_id) || in_array($key, $this->custom_excluded)) continue;
            $firstPartOfQuery .= "$key,";
            $secondPartOfQuery .= ":$key,";
        }

        $firstPartOfQuery = rtrim($firstPartOfQuery, ",") . ") ";
        $secondPartOfQuery = rtrim($secondPartOfQuery, ",") . ")";

        $query = $firstPartOfQuery . $secondPartOfQuery;

        $q = $this->PDO->prepare($query);

        foreach ($objVars as $key => &$value) {
            if (in_array($key, $system_excluded_without_id) || in_array($key, $this->custom_excluded)) continue;
            $q->bindParam(":" . $key, $value);
        }

        $q->execute();

        $this->id = $id;

        return true;
    }

    /**
     * Update the object in the database.
     *
     * @return bool Returns true on successful update, false otherwise.
     */
    private function update()
    {
        $objVars = get_object_vars($this);

        $query = "UPDATE " . static::TABLENAME . " SET ";
        foreach ($objVars as $key => $value) {
            if (in_array($key, $this->system_excluded) || in_array($key, $this->custom_excluded)) continue;
            $query .= "$key = :$key,";
        }

        $query = substr($query, 0, -1);
        $query .= " WHERE id = :id";

        $q = $this->PDO->prepare($query);

        foreach ($objVars as $key => &$value) {
            if (in_array($key, $this->system_excluded) || in_array($key, $this->custom_excluded)) continue;
            $q->bindParam(":" . $key, $value);
        }

        $q->bindParam(":id", $this->id);
        $q->execute();

        return true;
    }
    /**
     * Retrieve the fields of the object's database table along with their metadata.
     *
     * @return array An array containing information about the fields of the database table.
     */
    public function getFields()
    {
        // Obtain the list of columns and data types from the table using INFORMATION_SCHEMA
        $query = "SELECT COLUMN_NAME, COLUMN_TYPE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :tableName AND TABLE_SCHEMA = :tableSchema";
        $stmt = $this->PDO->prepare($query);
        $stmt->execute(['tableName' => static::TABLENAME, 'tableSchema' => Config::get("db_name")]);
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Query to retrieve all data from the table
        $ids = (!empty($this->id)) ? " WHERE id=" . $this->id : " WHERE id=1";
        $query = "SELECT * FROM " . static::TABLENAME . $ids;
        $stmt = $this->PDO->query($query);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $resultArray = [];

        // Iterate over each record
        foreach ($data as $row) {
            $record = [];
            foreach ($columns as $columnInfo) {
                $columnName = $columnInfo['COLUMN_NAME'];
                $columnType = $columnInfo['COLUMN_TYPE'];
                $datatype = $columnInfo['DATA_TYPE'];
                $maxLength = $columnInfo['CHARACTER_MAXIMUM_LENGTH']; // Maximum length for VARCHAR

                if ((!empty($this->id))) {
                    $record[$columnName] = [
                        'value' => $row[$columnName],
                        'data_type' => $datatype, // Data type
                        'column_type' => $columnType, // Column type
                        'max_length' => $maxLength, // Maximum length for VARCHAR
                        'foreign' => null
                    ];
                } else {
                    $record[$columnName] = [
                        'data_type' => $datatype, // Data type
                        'column_type' => $columnType, // Column type
                        'max_length' => $maxLength, // Maximum length for VARCHAR
                        'foreign' => null
                    ];
                }

                // Check for foreign keys
                if (str_contains($columnName, '_id') || str_contains($columnName, 'id_')) {
                    $foreignKeysQuery = "
                    SELECT 
                        COLUMN_NAME, 
                        CONSTRAINT_NAME, 
                        REFERENCED_TABLE_NAME, 
                        REFERENCED_COLUMN_NAME 
                    FROM 
                        information_schema.KEY_COLUMN_USAGE 
                    WHERE 
                        TABLE_NAME = :tableName 
                        AND COLUMN_NAME = :columnName 
                        AND CONSTRAINT_NAME != 'PRIMARY'
                ";

                    $stmt = $this->PDO->prepare($foreignKeysQuery);
                    $stmt->execute(['tableName' => static::TABLENAME, 'columnName' => $columnName]);
                    $foreignKeys = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    $record[$columnName]["foreign"] = $foreignKeys;
                }
            }
            $resultArray[] = $record;
        }
        return ($resultArray);
    }

    /**
     * Sets object properties from an array, excluding specified keys, and saves the object.
     *
     * @param object $obj The object to set properties for.
     * @param array $array The array containing property values.
     * @param array $arrayExcluded The keys to exclude from setting as properties.
     * @return void
     */
    public static function setObjFromArray(&$obj, $array, $arrayExcluded = array("id"))
    {
        foreach ($array as $key => $value) {
            if (in_array($key, $arrayExcluded)) continue;
            $obj->{$key} = $value;
        }
        $obj->save();
    }
}
