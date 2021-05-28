<?php
/**
 * Boostack: Log_Database_List.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */

class Log_Database_List extends BaseList
{

    /**
     *
     */
    const BASE_CLASS = Log_Database_Entity::class;

    /**
     * LogList constructor.
     */
    public function __construct()
    {
        parent::init();
    }

}