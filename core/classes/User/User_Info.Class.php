<?php
/**
 * Boostack: User_Info.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class User_Info extends BaseClass
{

    /**
     * @var
     */
    protected $first_name;
    /**
     * @var
     */
    protected $last_name;
    /**
     * @var
     */
    protected $locale;
    /**
     * @var
     */
    protected $city;
    /**
     * @var
     */
    protected $state;
    /**
     * @var
     */
    protected $country;
    /**
     * @var
     */
    protected $zip;
    /**
     * @var
     */
    protected $about_me;
    /**
     * @var
     */
    protected $tel;
    /**
     * @var
     */
    protected $cell;
    /**
     * @var
     */
    protected $profession;
    /**
     * @var
     */
    protected $company;
    /**
     * @var
     */
    protected $birthday;
    /**
     * @var
     */
    protected $movies;
    /**
     * @var
     */
    protected $music;
    /**
     * @var
     */
    protected $political;
    /**
     * @var
     */
    protected $interests;
    /**
     * @var
     */
    protected $tv;
    /**
     * @var
     */
    protected $religion;
    /**
     * @var
     */
    protected $pic_big;
    /**
     * @var
     */
    protected $sex;
    /**
     * @var
     */
    protected $name;

    /**
     *
     */
    const TABLENAME = "boostack_user_info";

    /**
     * @var array
     */
    protected $default_values = [
        "first_name" => "",
        "last_name" => "",
        "locale" => "",
        "city" => "",
        "state" => "",
        "country" => "",
        "zip" => "",
        "about_me" => "",
        "tel" => "",
        "cell" => "",
        "profession" => "",
        "company" => "",
        "birthday" => "",
        "movies" => "",
        "music" => "",
        "political" => "",
        "interests" => "",
        "tv" => "",
        "religion" => "",
        "pic_big" => "",
        "sex" => "",
        "name" => "",
    ];

    /**
     * User_Info constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::init($id);
    }

}

?>