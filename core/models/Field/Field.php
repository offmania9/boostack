<?php
namespace Core\Models\Field;
/**
 * Boostack: Field.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */
class Field implements \JsonSerializable
{

    private $name;
    private $type;
    private $rules;

    public function __construct($name, $type)
    {
        // Check if the provided field type is valid
        if (!FieldType::isValidValue($type)) {
            throw new \Exception("Error: Wrong field type");
        }
        $this->name = $name;
        $this->type = $type;
        $this->rules = array();
    }

    public static function rules($name, $type)
    {
        // Check if the provided field type is valid
        if (!FieldType::isValidValue($type)) {
            throw new \Exception("Error: Wrong field type");
        }
        return new FieldRule($name, $type);
    }

    /**
     * Serialize the object to JSON.
     *
     * @return mixed The serialized object.
     */
    public function jsonSerialize(): mixed
    {
        return [
            "name" => $this->name,
            "type" => $this->type,
            "rules" => $this->rules,
        ];
    }

    /**
     * Convert the object to a stdClass object.
     *
     * @return object The object as stdClass.
     */
    public function toObject()
    {
        return (object) [
            "name" => $this->name,
            "type" => $this->type,
            "rules" => $this->rules,
        ];
    }

    /**
     * Convert the object to JSON.
     *
     * @return string The object as JSON.
     */
    public function toJSON()
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * Magic method to get a property value by name.
     *
     * @param string $property_name The name of the property.
     * @return mixed The value of the property.
     * @throws \Exception If the property does not exist.
     */
    public function &__get($property_name)
    {
        if (property_exists($this, $property_name)) {
            return $this->$property_name;
        } else {
            throw new \Exception("Field $property_name not found");
        }
    }

    /**
     * Magic method to set a property value by name.
     *
     * @param string $property_name The name of the property.
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
}
