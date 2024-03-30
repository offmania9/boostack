<?php

/**
 * Boostack: BaseClassTraced.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 * 
 * ALTER TABLE `[tablename]` 
 * ADD `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
 * ADD `last_update` timestamp NOT NULL DEFAULT current_timestamp(),
 * ADD `last_access` timestamp NOT NULL DEFAULT current_timestamp(),
 * ADD `deleted_at` timestamp NULL DEFAULT NULL
 */
abstract class BaseClassTraced extends BaseClass
{
    protected $created_at;
    protected $last_update;
    protected $last_access;
    protected $deleted_at;

    /**
     * Constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->default_values['created_at'] = '';
        $this->default_values['last_update'] = '';
        $this->default_values['last_access'] = '';
        $this->default_values['deleted_at'] = null;
        parent::init($id);
        if ($id !== null) {
            $this->setLastAccess();
        } else {
            $currentTime = date('Y-m-d H:i:s', time());
            $this->created_at = $currentTime;
            $this->last_update = $currentTime;
            $this->last_access = $currentTime;
        }
    }

    /**
     * Fill the object with data from an array.
     * @param $array
     * @return bool
     */
    public function fill($array)
    {
        $this->prepare($array);
        if ($this->id !== null) {
            $this->setLastAccess();
        }
        return true;
    }

    /**
     * Save the object into the database.
     * @param null $forcedID
     * @return bool
     */
    public function save($forcedID = null)
    {
        if ($forcedID === null && $this->id !== null) {
            $this->setUpdateTime();
            $this->setLastAccess();
        }
        return parent::save($forcedID);
    }

    /**
     * Delete the object from the database.
     * @return bool
     */
    public function delete()
    {
        $this->setUpdateTime();
        return parent::delete();
    }

    /**
     * Set the creation time of the object.
     * @return bool
     */
    public function setCreationTime()
    {
        $this->created_at = date('Y-m-d H:i:s', time());
        return parent::save();
    }

    /**
     * Set the update time of the object.
     * @return bool
     */
    public function setUpdateTime()
    {
        $this->last_update = date('Y-m-d H:i:s', time());
        return parent::save();
    }

    /**
     * Set the last access time of the object.
     * @return bool
     */
    public function setLastAccess()
    {
        $this->last_access = date('Y-m-d H:i:s', time());
        return parent::save();
    }
}
