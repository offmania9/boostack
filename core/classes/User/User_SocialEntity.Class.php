<?php

/**
 * Boostack: User_SocialEntity.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.4
 */
class User_SocialEntity extends BaseClass
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
        $this->userInstance = new User_Entity($id); // TODO lazy loading su userInstance
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