<?php

/**
 * Boostack: User_Social.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class User_Social extends BaseClass
{

    /**
     * @var
     */
    protected $type;
    /**
     * @var
     */
    protected $uid;
    /**
     * @var
     */
    protected $uid_token;
    /**
     * @var
     */
    protected $uid_token_secret;
    /**
     * @var
     */
    protected $autosharing;
    /**
     * @var
     */
    protected $website;
    /**
     * @var
     */
    protected $extra;

    /**
     *
     */
    const TABLENAME = "boostack_user_social";

    /**
     * @var array
     */
    protected $default_values = [
        "type" => "",
        "uid" => "",
        "uid_token" => "",
        "uid_token_secret" => "",
        "autosharing" => "",
        "website" => "",
        "extra" => "",
    ];

    /**
     * User_Social constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::init($id);
    }

}
?>