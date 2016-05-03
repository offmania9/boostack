<?php

abstract class BaseClass {

    /*** CLASS VARIABLES ***/

    protected $id;
    protected $pdo; // TODO: static? Find a way to automatically inject db instance into this field?
    protected $default_values = [];
    protected $system_excluded = ['id','default_values','system_excluded','custom_excluded','pdo'];
    protected $custom_excluded = [];
    const TABLENAME = "";

    /*** ABSTRACT FUNCTIONS ***/

    //

    /*** CONCRETE FUNCTIONS ***/

    protected function init() {
        $this->pdo = Database_PDO::getInstance();
    }

    public function fill($array) {
        self::prepare($array);
        return true;
    }

    public function load($id) {
        $sql = "SELECT * FROM " . static::TABLENAME . " WHERE id = :id";
        $q = $this->pdo->prepare($sql);
        $q->bindValue(':id', $id);
        $q->execute();
        $result = $q->fetch(PDO::FETCH_ASSOC);
        if(empty($result)) {
            throw new Exception("No result found in table ".static::TABLENAME." with ID ".$id);
        }
        self::prepare($result);
        $this->id = $id;
        return true;
    }

    public function save() {
        if(empty($this->id)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    private function prepare($array) {
        $defaultValuesKeys = array_keys($this->default_values);
        $inputKeys = array_keys($array);
        $fieldsNotPresent = array_diff($inputKeys,$defaultValuesKeys,$this->system_excluded,$this->custom_excluded);
        if(count($fieldsNotPresent) > 0) {
            throw new Exception(implode(",",$fieldsNotPresent)." are not found in object");
        }
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
        // TODO: add prepared statement to insert query
        $sql_1 = "INSERT INTO " . static::TABLENAME . " (id";
        $sql_2 = "VALUES(NULL";
        foreach ($objVars as $key => $value) {
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $sql_1 .= ",$key";
            $sql_2 .= ",'$value'";
        }
        $sql_1 .= ") ";
        $sql_2 .= ")";
        $sql = $sql_1 . $sql_2;
        $this->pdo->query($sql);
        $this->id = $this->pdo->lastInsertId();
        return true;
    }

    private function update() {
        $objVars = get_object_vars($this);
        // TODO: add prepared statement to update query
        $sql = "UPDATE " . static::TABLENAME . " SET ";
        foreach ($objVars as $key => $value) {
            if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
            $sql .= "$key='" . $value . "',";
        }
        $sql = substr($sql, 0, - 1);
        $sql .= " WHERE id='" . $this->id . "'";
        $this->pdo->query($sql);
        return true;
    }

    public function delete() {
        $sql = "DELETE FROM " . static::TABLENAME . " WHERE id = :id";
        $q = $this->pdo->prepare($sql);
        $q->bindValue(':id', $this->id);
        $q->execute();
        return ($q->rowCount() == 0);
    }

    public function __get($property_name) {
        // TODO: throw exception if property is not present?
        if (isset($this->$property_name)) {
            return ($this->$property_name);
        } else {
            return (NULL);
        }
    }

    public function __set($property_name, $val) {
        // TODO: throw exception if property is not present?
        $this->$property_name = $val;
    }


}