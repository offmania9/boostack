<?php
namespace Core\Models\Log\Database;
/**
 * Boostack: Log_Database_List.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

class Log_Database_List extends \Core\Models\BaseList
{

    const BASE_CLASS = Log_Database_Entity::class;

    /**
     * LogList constructor.
     */
    public function __construct()
    {
        parent::init();
    }
}
