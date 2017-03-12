<?php

/**
 * Boostack: User_Registration.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class User_Registration extends BaseClass
{
    /**
     * @var
     */
    protected $activation_date;
    /**
     * @var
     */
    protected $access_code;
    /**
     * @var
     */
    protected $ip;
    /**
     * @var
     */
    protected $join_date;
    /**
     * @var
     */
    protected $join_idconfirm;

    /**
     *
     */
    const TABLENAME = "boostack_user_registration";

    /**
     * @var array
     */
    protected $default_values = [
        "activation_date" => 0,
        "access_code" => "",
        "ip" => "",
        "join_date" => 0,
        "join_idconfirm" => "",
    ];

    /**
     * User_Registration constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::init($id);
    }

}
?>