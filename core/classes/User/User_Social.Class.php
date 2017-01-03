<?php

/**
 * Boostack: User_Social.Class.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
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

    const TABLENAME = "boostack_user_social";

    protected $default_values = [
        "type" => "",
        "uid" => "",
        "uid_token" => "",
        "uid_token_secret" => "",
        "autosharing" => "",
        "website" => "",
        "extra" => "",
    ];

    protected $userInstance;
    protected $custom_excluded = ['userInstance'];

    public function __construct($id = null)
    {
        parent::init($id);
        $this->userInstance = new User($id); // TODO lazy loading su userInstance
    }

    public function save()
    {
        if (empty($this->userInstance->id)) {
            $this->userInstance->save();
            parent::insertWithID($this->userInstance->id);
        } else {
            $this->userInstance->save();
            parent::save();
        }
    }

}
?>