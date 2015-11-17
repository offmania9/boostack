<?

/**
 * Boostack: ListObjectId.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
class ListObjectId
{

    private $oid_list;

    public function __construct($array_objid = null)
    {
        $this->oid_list = $array_objid;
    }

    public function Add($val)
    {
        $this->oid_list[] = $val;
    }

    public function getList()
    {
        return $this->oid_list;
    }

    public function __get($property_name)
    {
        if (isset($this->$property_name)) {
            return ($this->$property_name);
        } else {
            return (NULL);
        }
    }

    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }
}
?>