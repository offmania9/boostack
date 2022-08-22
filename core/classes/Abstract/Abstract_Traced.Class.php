<?php

abstract class Abstract_Traced extends BaseClass {

    protected $created_at;
    protected $last_update;
    protected $last_access;

    /**
     * Abstract_Traced constructor.
     * @param null $id
     */
    public function __construct($id = NULL) {
        parent::init($id);
        if ($id != NULL) {
            $this->setLastAccess();
        } else {
            $this->created_at = $this->last_update = $this->last_access = time();
        }
    }

    /**
     * @param $array
     * @return bool
     */
    public function fill($array) {
        $this->prepare($array);
        if($this->id != NULL){
            $this->setLastAccess();
        }
        return true;
    }

    /**
     * @param null $forcedID
     * @return bool
     */
    public function save($forcedID = null) {
        if ($forcedID == null && $this->id != null) {
            $this->setCreationTime();
            $this->setUpdateTime();
            $this->setLastAccess();
        }
        return parent::save($forcedID);
    }

    /*
    public function delete()
    {
        if($this->hasSoftDelete()) {
            $this->setUpdateTime();
        }
        return parent::delete();
    } */

    /**
     * @return bool
     */
    public function setCreationTime() {
        $this->created_at = time();
        return parent::save();
    }

    /**
     * @return bool
     */
    public function setUpdateTime() {
        $this->last_update = time();
        return parent::save();
    }

    /**
     * @return bool
     */
    public function setLastAccess() {
        $this->last_access = time();
        return parent::save();
    }

}