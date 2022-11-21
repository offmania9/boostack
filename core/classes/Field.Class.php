<?php
/**
 * Boostack: Field.Class.php
 * ========================================================================
 * Copyright 2014-2023 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.1
 */
class Field implements JsonSerializable {

    private $name;
    private $type;
    private $rules;

    public function __construct($name, $type) {
        if(!FieldType::isValidValue($type))
            throw new Exception("error: wrong field type"); 
        $this->name = $name;
        $this->type = $type;
        $this->rules = array();
    }

    public static function rules($name, $type) {
        if(!FieldType::isValidValue($type))
            throw new Exception("error: wrong field type"); 
        return new FieldRule($name, $type);
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            "name" => $this->name,
            "type" => $this->type,
            "rules" => $this->rules,
        ];
    }

    public function toObject() {
        return (object) array(
            "name" => $this->name,
            "type" => $this->type,
            "rules" => $this->rules,
        );
    }

    /**
     * @return string
     */
    public function toJSON() {
        return json_encode($this->jsonSerialize());
    }

            /**
     * Getter
     *
     * @param $property_name
     * @return mixed
     * @throws Exception
     */
    public function &__get($property_name) {
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

}
?>