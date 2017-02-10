<?php
/**
 * Boostack: BaseClass.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
abstract class BaseClass implements JsonSerializable {

    protected $id;
    protected $pdo; // TODO: static? Find a way to automatically inject db instance into this field?
    protected $default_values = [];
    protected $system_excluded = ['id','default_values','system_excluded','custom_excluded','pdo'];
    protected $custom_excluded = [];
    const TABLENAME = "";


    /**
     * Init method: creates the PDO object.
     * Call this in your __construct() function with parent::init()
     */
    protected function init($id = NULL) {
        $this->pdo = Database_PDO::getInstance();
        if($id !== NULL) {
            $this->load($id);
        } else {
            $this->prepare();
        }
    }

    /**
     * Fill the object
     *
     * @param $array
     * @return bool
     * @throws Exception
     */
    public function fill($array) {
        $this->prepare($array);
        return true;
    }

    public function clearAndFill($array){
        $defaultValuesKeys = array_keys($this->default_values);
        $inputKeys = array_keys($array);
        $fieldsNotPresent = array_diff($inputKeys,$defaultValuesKeys,$this->system_excluded,$this->custom_excluded);
        if(count($fieldsNotPresent) > 0) {
            foreach ($fieldsNotPresent as $value)
                unset($array[$value]);
        }
        return $this->fill($array);
    }

    /**
     * Load the object from the database
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function load($id) {
        $sql = "SELECT * FROM " . static::TABLENAME . " WHERE id = :id";
        $q = $this->pdo->prepare($sql);
        $q->bindValue(':id', $id);
        $q->execute();
        $result = $q->fetch(PDO::FETCH_ASSOC);
        if(empty($result)) {
            throw new Exception("No result found in table ".static::TABLENAME." with ID ".$id);
        }
        $this->prepare($result);
        $this->id = $id;
        return true;
    }

    /**
     * Save the object into the database
     *
     * @param $forcedID: if not null, insert the entity with $forceID as id
     *
     * @return bool
     */
    public function save($forcedID = null) {
        if(empty($this->id)) {
            if(empty($forcedID)) {
                return $this->insert();
            }
            return $this->insertWithID($forcedID);
        } else {
            return $this->update();
        }
    }

    /**
     * Delete the object from the database
     *
     * @return bool
     */
    public function delete() {
        $sql = "DELETE FROM " . static::TABLENAME . " WHERE id = :id";
        $q = $this->pdo->prepare($sql);
        $q->bindValue(':id', $this->id);
        $q->execute();
        return ($q->rowCount() > 0);
    }

    /**
     * Getter
     *
     * @param $property_name
     * @return mixed
     * @throws Exception
     */
    public function __get($property_name) {
        if (property_exists($this, $property_name)) {
            return $this->$property_name;
        } else {
            throw new Exception("Field $property_name not found");
        }
    }

    /**
     * Setter
     *
     * @param $property_name
     * @param $val
     * @throws Exception
     */
    public function __set($property_name, $val) {
        if (property_exists($this, $property_name)) {
            $this->$property_name = $val;
        } else {
            throw new Exception("Field $property_name not found");
        }
    }

    /**
     * Magic method used for isset() and empty() methods invoked outside the object on a protected/private field
     */
    public function __isset($property_name) {
        return isset($this->$property_name);
    }

    /**
     * Get the object vars used in serialize() method
     *
     * @return array
     */
    public function __sleep() {
        $objVars = get_object_vars($this);
        $objVarsExported = array();
        foreach($objVars as $key => $value) {
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $objVarsExported[] = $key;
        }
        return $objVarsExported;
    }

    /**
     * Task performed after call of unserialize() method
     * e.g. reestablish any database connections, reinitialization tasks..
     */
    public function __wakeup() {
        $this->pdo = Database_PDO::getInstance();
    }

    /**
     * This method is used when json_encode() is called
     * It expose all the variable of the object to the json_encode() function
     */
    public function jsonSerialize() {
        $objVars = get_object_vars($this);
        $objVarsExported = array();
        foreach($objVars as $key => $value) {
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $objVarsExported[$key] = $value;
        }
        return $objVarsExported;
    }

    /**
     * Return the object database table
     */
    public function getTablename() {
        return static::TABLENAME;
    }

    /**
     * Return the list of object attributes
     */
    public function getAttributes() {
        $objVars = get_object_vars($this);
        $attributes = array();
        foreach($objVars as $key => $value) {
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $attributes[] = $key;
        }
        return $attributes;
    }

    protected function prepare($array = array()) {
        $defaultValuesKeys = array_keys($this->default_values);
        $inputKeys = array_keys($array);
        $fieldsNotPresent = array_diff($inputKeys,$defaultValuesKeys,$this->system_excluded,$this->custom_excluded);
        if(count($fieldsNotPresent) > 0) {
            throw new Exception(implode(",",$fieldsNotPresent)." are not found in object");
        }
        if(!empty($array['id'])) $this->id = $array['id'];
        foreach($defaultValuesKeys as $defaultField) {
            if(in_array($defaultField,$inputKeys)) {
                $this->$defaultField = $array[$defaultField];
            }
            else {
                $this->$defaultField = $this->default_values[$defaultField];
            }
        }
    }

    private function insert() {

        $objVars = get_object_vars($this);
        //var_dump($objVars);

        $firstPartOfQuery = "INSERT INTO ".static::TABLENAME." (id";
        $secondPartOfQuery = "VALUES(NULL";

        foreach($objVars as $key => $value) {
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $firstPartOfQuery .= ",$key";
            $secondPartOfQuery .= ",:$key";
        }

        $firstPartOfQuery .= ") ";
        $secondPartOfQuery .= ")";

        $query = $firstPartOfQuery.$secondPartOfQuery;
        $q = $this->pdo->prepare($query);

        foreach($objVars as $key => &$value) {
            // if($value === "") $value = NULL; // TODO verificare se bindare la stringa vuota a NULL è cosa buona e giusta
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $q->bindParam(":".$key, $value);
        }

        $q->execute();

        $this->id = $this->pdo->lastInsertId();
        return true;
    }

    protected function insertWithID($id) {
        $objVars = get_object_vars($this);
        $objVars["id"] = $id;

        $system_excluded_without_id = array_diff($this->system_excluded,["id"]);
        $firstPartOfQuery = "INSERT INTO ".static::TABLENAME." (";
        $secondPartOfQuery = "VALUES(";

        foreach($objVars as $key => $value) {
            if(in_array($key,$system_excluded_without_id) || in_array($key,$this->custom_excluded)) continue;
            $firstPartOfQuery .= "$key,";
            $secondPartOfQuery .= ":$key,";
        }

        $firstPartOfQuery = rtrim($firstPartOfQuery, ",").") ";
        $secondPartOfQuery = rtrim($secondPartOfQuery, ",").")";

        $query = $firstPartOfQuery.$secondPartOfQuery;

        $q = $this->pdo->prepare($query);

        foreach($objVars as $key => &$value) {
            // if($value === "") $value = NULL; // TODO verificare se bindare la stringa vuota a NULL è cosa buona e giusta
            if(in_array($key,$system_excluded_without_id) || in_array($key,$this->custom_excluded)) continue;
            $q->bindParam(":".$key, $value);
        }

        $q->execute();

        $this->id = $id;

        return true;
    }

    private function update() {
        $objVars = get_object_vars($this);

        $query = "UPDATE ".static::TABLENAME." SET ";
        foreach($objVars as $key => $value) {
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $query .= "$key = :$key,";
        }

        $query = substr($query, 0, -1);
        $query .= " WHERE id = :id";

        $q = $this->pdo->prepare($query);

        foreach($objVars as $key => &$value) {
            // if($value === "") $value = NULL; // TODO verificare se bindare la stringa vuota a NULL è cosa buona e giusta
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $q->bindParam(":".$key, $value);
        }

        $q->bindParam(":id",$this->id);
        $q->execute();

        return true;
    }

}
?>
