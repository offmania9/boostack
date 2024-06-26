<?php

namespace My\Controllers\Rest;

use Boostack\Models\Auth;
use Boostack\Models\Config;
use Boostack\Models\Language;
use Boostack\Models\User\User;
use Boostack\Models\Session\Session;
use Boostack\Models\Log\Database\Log_Database_List;
use Boostack\Models\Field\Field;
use Boostack\Models\Field\FieldType;

/**
 * Boostack: Rest_AuthApi.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */

class Rest_AuthApi extends \Boostack\Models\Rest\Rest_Api
{

    protected function login()
    {
        $this->constraints(
            "POST",
            false,
            array(
                "Content-Type" => "application/json"
            )
        );
        $res = array();
        try {
            $input_json = str_replace("'", "&#039;", $this->file);
            $content = json_decode($input_json);
            Auth::loginByUsernameAndPlainPassword($content->username, $content->password, $content->rememberme);
            return $res;
        } catch (\Exception $e) {
            throw $e;
        }
    }


    protected function registrationFirstStep()
    {
        $this->constraints(
            "POST",
            false,
            array(
                "Content-Type" => "application/json"
            )
        );
        $res = array();
        try {
            $input_json = str_replace("'", "&#039;", $this->file);
            $content = json_decode($input_json);
            if (empty($content->agree) || !$content->agree) throw new \Exception("Agree to terms and conditions format not valid");
            $token_key = Session::getObject()->getCSRFDefaultKey();
            $token_value = !empty($content->{$token_key}) ? $content->{$token_key} : null;
            $user = Auth::registration($content->email, $content->email, $content->password, $content->password_confirm, $token_value);
            if (!empty($user) && $user instanceof User) {
                return array("id" => $user->id, "token" => Session::getObject()->CSRFTokenGenerator());
            } else
                throw new \Exception("Generic Error from Auth registration");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function registrationBasic()
    {
        $this->constraints(
            "POST",
            false,
            array(
                "Content-Type" => "application/json"
            )
        );
        $res = array();
        try {
            $input_json = str_replace("'", "&#039;", $this->file);
            $content = json_decode($input_json);
            #if(empty($content->first_name)) throw new \Exception("first_name format not valid");
            #if(empty($content->last_name)) throw new \Exception("first_name format not valid");
            if (empty($content->email)) throw new \Exception("Email format not valid");
            if (empty($content->password)) throw new \Exception("Password format not valid");
            if (empty($content->password_confirm) || $content->password_confirm !== $content->password) throw new \Exception("Password confirm format not valid");
            if (empty($content->agree) || !$content->agree) throw new \Exception("Agree to terms and conditions format not valid");

            if (Config::get('csrf_on')) {
                $token_key = Session::getObject()->getCSRFDefaultKey();
                if (empty($content->{$token_key}))
                    throw new \Exception("Attention! CSRF token is required.");
                $token_value = $content->{$token_key};
                if (empty($token_value))
                    throw new \Exception("Attention! CSRF token is required.");
                Auth::registration($content->email, $content->email, $content->password, $content->password_confirm, $token_value);
            } else {
                Auth::registration($content->email, $content->email, $content->password, $content->password_confirm);
            }
            return $res;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getLogList()
    {
        $this->constraints(
            "POST",
            true,
            array(
                "Content-Type" => "application/json"
            )
        );
        $res = array();
        try {
            $logList = new Log_Database_List();
            $logList->loadAll("id", "desc");
            return $logList;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    protected function logout()
    {
        $this->constraints("GET");
        Auth::logout();
        return true;
    }

    protected function getToken()
    {
        $this->constraints("GET");
        return Session::getObject()->CSRFTokenGenerator();
    }

    protected function getRegistrationFields()
    {
        $this->constraints("GET");
        $res = array(
            Field::rules("username", FieldType::USERNAME)
                ->required()
                ->title(Language::getLabel("form.registration.username"))
                ->description(Language::getLabel("form.registration.username_description"))
                ->max_length(Config::get("username_max_length"))
                ->max_length(Config::get("username_min_length"))
                ->get(),
            Field::rules("password", FieldType::PASSWORD)
                ->required()
                ->max_length(Config::get("password_max_length"))
                ->min_length(Config::get("password_min_length"))
                ->get(),
            Field::rules("email", FieldType::EMAIL)
                ->required()
                ->max_length(225)
                ->min_length(5)
                ->get(),
            Field::rules("name", FieldType::STRING)
                ->required()
                ->max_length(225)
                ->min_length(2)
                ->get(),
            Field::rules("surname", FieldType::STRING)
                ->required()
                ->max_length(225)
                ->min_length(2)
                ->get(),
            Field::rules("birthday", FieldType::DATE)
                ->required()
                ->from((new \DateTime())->sub(new \DateInterval('P120Y')))
                ->get(),
        );
        return $res;
    }

    protected function getLoginFields()
    {
        $this->constraints("GET");
        $res = array(
            Field::rules("username", FieldType::USERNAME)
                ->required()
                ->title(Language::getLabel("form.registration.username"))
                ->description(Language::getLabel("form.registration.username_description"))
                ->placeholder(Language::getLabel("form.registration.username_description"))
                ->max_length(Config::get("username_max_length"))
                ->max_length(Config::get("username_min_length"))
                ->get(),
            Field::rules("password", FieldType::PASSWORD)
                ->required()
                ->title(Language::getLabel("form.registration.password"))
                ->description(Language::getLabel("form.registration.password_description"))
                ->placeholder(Language::getLabel("form.registration.password_description"))
                ->max_length(Config::get("password_max_length"))
                ->min_length(Config::get("password_min_length"))
                ->get(),
            Field::rules("email", FieldType::EMAIL)
                ->required()
                ->title(Language::getLabel("form.registration.email"))
                ->description(Language::getLabel("form.registration.email_description"))
                ->placeholder(Language::getLabel("form.registration.email_description"))
                ->max_length(225)
                ->min_length(5)
                ->get(),
            Field::rules("rememberme", FieldType::FLAG)
                ->title(Language::getLabel("form.login.rememberme"))
                ->get(),
        );
        return $res;
    }
}
