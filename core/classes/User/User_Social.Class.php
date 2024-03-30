<?php

/**
 * Boostack: User_Social.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */
class User_Social extends BaseClass
{

    
    protected $type;
    
    protected $uid;
    
    protected $uid_token;
    
    protected $uid_token_secret;
    
    protected $autosharing;
    
    protected $website;
    
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