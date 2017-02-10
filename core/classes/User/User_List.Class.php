<?php
/**
 * Boostack: User_List.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class User_List extends BaseList {

    const BASE_CLASS = User::class;

    public function __construct() {
        parent::init();
    }

    public function getItemsArray() {
        return $this->items;
    }

}
?>