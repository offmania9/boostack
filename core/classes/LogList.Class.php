<?php
/**
 * Boostack: LogList.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.3
 */
class LogList extends BaseList {

    private $pdo;

    const TABLENAME = "boostack_log";

    public function __construct() {
        $this->pdo = Database_PDO::getInstance();
        $this->items = [];
    }

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

    public function getItemsArray(){
        return $this->items;
    }
}