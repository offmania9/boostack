<?php

/**
 * Boostack: ListObjectId.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */
class ListObjectId
{

    /**
     * @var null
     */
    private $oid_list;

    /**
     * Constructor for ListObjectId class.
     *
     * @param array|null $array_objid An array of object IDs (optional).
     */
    public function __construct($array_objid = null)
    {
        $this->oid_list = $array_objid;
    }

    /**
     * Add an object ID to the list.
     *
     * @param mixed $val The object ID to add.
     */
    public function Add($val)
    {
        $this->oid_list[] = $val;
    }

    /**
     * Get the list of object IDs.
     *
     * @return array|null The list of object IDs.
     */
    public function getList()
    {
        return $this->oid_list;
    }

    /**
     * Magic getter method to access object properties.
     *
     * @param string $property_name The name of the property to access.
     * @return mixed|null The value of the property, or null if not found.
     */
    public function __get($property_name)
    {
        return isset($this->$property_name) ? $this->$property_name : null;
    }

    /**
     * Magic setter method to set object properties.
     *
     * @param string $property_name The name of the property to set.
     * @param mixed $val The value to set.
     */
    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }
}
