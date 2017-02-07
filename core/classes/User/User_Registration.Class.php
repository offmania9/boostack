<?php

/**
 * Boostack: User_Registration.Class.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */
class User_Registration extends BaseClass
{

    protected $activation_date;
    protected $access_code;
    protected $ip;
    protected $join_date;
    protected $join_idconfirm;

    const TABLENAME = "boostack_user_registration";

    protected $default_values = [
        "activation_date" => 0,
        "access_code" => "",
        "ip" => "",
        "join_date" => 0,
        "join_idconfirm" => "",
    ];

    public function __construct($id = null)
    {
        parent::init($id);
    }

}
?>