<?php

class ExampleConcreteClass extends BaseClass {

    protected $name;
    protected $surname;
    protected $email;
    protected $age;

    const TABLENAME = "boostack_test";

    protected $default_values = [
        "name" => "",
        "surname" => "",
        "email" => "",
        "age" => 0,
    ];

    public function __construct() {
        parent::init();
    }



}