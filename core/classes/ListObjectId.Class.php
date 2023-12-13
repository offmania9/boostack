<?php
/**
 * Boostack: ListObjectId.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class ListObjectId
{

    /**
     * @var null
     */
    private $oid_list;

    /**
     * ListObjectId constructor.
     * @param null $array_objid
     */
    public function __construct($array_objid = null)
    {
        $this->oid_list = $array_objid;
    }

    /**
     * @param $val
     */
    public function Add($val)
    {
        $this->oid_list[] = $val;
    }

    /**
     * @return null
     */
    public function getList()
    {
        return $this->oid_list;
    }

    /**
     * @param $property_name
     * @return null
     */
    public function __get($property_name)
    {
        if (isset($this->$property_name)) {
            return ($this->$property_name);
        } else {
            return (NULL);
        }
    }

    /**
     * @param $property_name
     * @param $val
     */
    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }
}
?>