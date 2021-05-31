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
    protected function login()
    {
        $this->constraints("POST",array("CONTENT_TYPE"=>"application/json"), true);
        $res = array();
        try{
            $input_json = str_replace("'","&#039;",$this->file);
            $content = json_decode($input_json);
            Auth::loginByUsernameAndPlainPassword($content->username,$content->password,$content->rememberme);
            return $res; 
        }
        catch (Exception $e) {throw $e;}
    }

    protected function registrationBasic()
    {
        $this->constraints("POST",array("CONTENT_TYPE"=>"application/json"), true);
        $res = array();
        try{
            $input_json = str_replace("'","&#039;",$this->file);
            $content = json_decode($input_json);
            if(empty($content->email)) throw new Exception("Email format not valid");
            if(empty($content->password)) throw new Exception("Password format not valid");
            if(empty($content->password_confirm) || $content->password_confirm !== $content->password) throw new Exception("Password confirm format not valid");
            if(empty($content->agree) || !$content->agree) throw new Exception("Agree to terms and conditions format not valid");

            if (Config::get('csrf_on')){
                $token_key = Session::getObject()->getCSRFDefaultKey();
                if(empty($content->{$token_key}))
                    throw new Exception ("Attention! CSRF token is required.");
                $token_value = $content->{$token_key};
                if(empty($token_value))
                    throw new Exception ("Attention! CSRF token is required.");
                Auth::registration($content->email,$content->email,$content->password,$content->password_confirm,$token_value);
            }
            else{
                Auth::registration($content->email,$content->email,$content->password,$content->password_confirm);
            }
            return $res; 
        }
        catch (Exception $e) {throw $e;}
    }

    protected function logout()
    {
        $this->constraints("GET");
        $res = array();
        Auth::logout();
        header("location: " . Config::get("url"));
        exit();
    }

    protected function getToken(){
        $this->constraints("GET");
        return Session::getObject()->CSRFTokenGenerator();
    }

    protected function getRegistrationFields()
    {
        $this->constraints("GET");
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
        return $res;
    }

    protected function getLoginFields()
    {
        $this->constraints("GET");
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
        return $res;
    }
 }
?>
 
