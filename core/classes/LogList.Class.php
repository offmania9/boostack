<?php
/**
 * Boostack: LogList.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class LogList extends BaseList {

    /**
     *
     */
    const BASE_CLASS = Log::class;

    /**
     * LogList constructor.
     */
    public function __construct() {
        parent::init();
    }

    /**
     * @return bool
     */
    public function truncate(){
        global $boostack;
        $res=true;
        $sql = "TRUNCATE " . static::TABLENAME ;
        try {
            $q = $this->pdo->prepare($sql);
            $q->execute();
            $this->items = [];
        }
        catch (Exception $e)
        {
            $boostack->writeLog('LogList -> truncate -> Caught exception: '.$e->getMessage());
            $res = false;
        }
        return $res;
    }

    /**
     * @return mixed
     */
    public function getItemsArray(){
        return $this->items;
    }
}