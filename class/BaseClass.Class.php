<?php

abstract class BaseClass {

    protected $id;
    protected $pdo; // TODO: static? Find a way to automatically inject db instance into this field?
    protected $default_values = array();
    protected $system_excluded = array('id','default_values','system_excluded','custom_excluded','pdo');
    protected $custom_excluded = array();
    const TABLENAME = "";

    /**
     * Init method: creates the PDO object.
     * Call this in your __construct() function with parent::init()
     */
    protected function init() {
        $this->pdo = Database_PDO::getInstance();
        self::prepare();
    }

    /**
     * Fill the object
     *
     * @param $array
     * @return bool
     * @throws Exception
     */
    public function fill($array) {
        self::prepare($array);
        return true;
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
        self::prepare($result);
        $this->id = $id;
        return true;
    }

    /**
     * Save the object into the database
     * @return bool
     */
    public function save() {
        if(empty($this->id)) {
            return $this->insert();
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
        return ($q->rowCount() == 0);
    }

    /**
     * Getter
     *
     * @param $property_name
     * @return mixed
     * @throws Exception_FieldNotFound
     */
    public function __get($property_name) {
        if (isset($this->$property_name)) {
            return ($this->$property_name);
        } else {
            //throw new Exception_FieldNotFound("Field $property_name not found");
            return '';
        }
    }
    /**
     * Setter
     *
     * @param $property_name
     * @param $val
     * @throws Exception_FieldNotFound
     */
    public function __set($property_name, $val) {
        if (isset($this->$property_name)) {
            $this->$property_name = $val;
        } else {
            throw new Exception_FieldNotFound("Field $property_name not found");
        }
    }

    private function prepare($array = array()) {
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
        if($objVars !== null && count($objVars)>0){
            $sql = "UPDATE " . static::TABLENAME . " SET ";
            foreach ($objVars as $key => $value) {
                if(in_array($key,$this->system_excluded) || in_array($key,$this->custom_excluded)) continue;
                $sql .= "$key='" . $value . "',";
            }
            $sql = substr($sql, 0, - 1);
            $sql .= " WHERE id='" . $this->id . "'";
            try{
                $this->pdo->query($sql);
            }
            catch (Exception $e){
                Boostack::getInstance()->writeLog("Class -> BaseClass -> update : " + $e->getMessage());
            }
            return true;
        }
        return false;
    }
}
?>