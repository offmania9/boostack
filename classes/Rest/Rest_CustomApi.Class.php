<?php

/**
 * Boostack: Rest_Api.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */

class Rest_CustomApi extends Rest_Api
{
    protected function getRegistrationFields()
    {
        $res = array();
        if ($this->method == 'GET') { 
            $res = array(
                        Field::rules("username",FieldType::USERNAME)
                        ->required()
                        ->title(Language::getLabel("form.registration.username"))
                        ->description(Language::getLabel("form.registration.username_description"))
                        ->max_length(Config::get("username_max_length"))
                        ->max_length(Config::get("username_min_length"))
                        ->get(),
                        Field::rules("password",FieldType::PASSWORD)
                        ->required()
                        ->max_length(Config::get("password_max_length"))
                        ->min_length(Config::get("password_min_length"))
                        ->get(),
                        Field::rules("email",FieldType::EMAIL)
                        ->required()
                        ->max_length(225)
                        ->min_length(5)
                        ->get(),
                        Field::rules("name",FieldType::STRING)
                        ->required()
                        ->max_length(225)
                        ->min_length(2)
                        ->get(),
                        Field::rules("surname",FieldType::STRING)
                        ->required()
                        ->max_length(225)
                        ->min_length(2)
                        ->get(),
                        Field::rules("birthday",FieldType::DATE)
                        ->required()
                        ->from((new DateTime())->sub(new DateInterval('P120Y')))
                        ->get(),
            );           
        } else {
            return "Only accepts GET requests";
        }
        return $res;
    }
/*
    protected function getOCR(){
        $ocr = new TesseractOCR();
        $ocr->image('/path/to/image.png');
        $ocr->run();
    }
*/
    protected function getLoginFields()
    {
        $res = array();
        if ($this->method == 'GET') { 
            $res = array(
                        Field::rules("username",FieldType::USERNAME)
                        ->required()
                        ->title(Language::getLabel("form.registration.username"))
                        ->description(Language::getLabel("form.registration.username_description"))
                        ->placeholder(Language::getLabel("form.registration.username_description"))
                        ->max_length(Config::get("username_max_length"))
                        ->max_length(Config::get("username_min_length"))
                        ->get(),
                        Field::rules("password",FieldType::PASSWORD)
                        ->required()
                        ->title(Language::getLabel("form.registration.password"))
                        ->description(Language::getLabel("form.registration.password_description"))
                        ->placeholder(Language::getLabel("form.registration.password_description"))
                        ->max_length(Config::get("password_max_length"))
                        ->min_length(Config::get("password_min_length"))
                        ->get(),
                        Field::rules("email",FieldType::EMAIL)
                        ->required()
                        ->title(Language::getLabel("form.registration.email"))
                        ->description(Language::getLabel("form.registration.email_description"))
                        ->placeholder(Language::getLabel("form.registration.email_description"))
                        ->max_length(225)
                        ->min_length(5)
                        ->get(),
                        Field::rules("rememberme",FieldType::FLAG)
                        ->title(Language::getLabel("form.login.rememberme"))
                        ->get(),
            );           
        } else {
            return "Only accepts GET requests";
        }
        return $res;
    }
 }
?>
 
