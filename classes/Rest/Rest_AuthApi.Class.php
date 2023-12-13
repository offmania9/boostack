<?php

/**
 * Boostack: Rest_AuthApi.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */

class Rest_AuthApi extends Rest_Api
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


    protected function registrationFirstStep(){
        $this->constraints("POST",array("CONTENT_TYPE"=>"application/json"), true);
        if(!Config::get("mail_on"))
            throw new Exception("Attention! mail_on config must to be true to send registration mail");
        $res = array();
        try{
            $input_json = str_replace("'","&#039;",$this->file);
            $content = json_decode($input_json);
            if(empty($content->first_name)) throw new Exception("first_name format not valid");
            if(empty($content->last_name)) throw new Exception("last_name format not valid");
            if(!Validator::email($content->email)) throw new Exception("Email format not valid");
            if(User::existsByEmail($content->email, false) || User::existsByUsername($content->email, false)) throw new Exception("Email already registered");
            if(!Validator::password($content->password)) throw new Exception("Password format not valid");
            if(empty($content->agree) || !$content->agree) throw new Exception("Agree to terms and conditions format not valid");

            if (Config::get('csrf_on')){
                $token_key = Session::getObject()->getCSRFDefaultKey();
                if(empty($content->{$token_key}))
                    throw new Exception ("Attention! CSRF token is required.");
                $token_value = $content->{$token_key};
                if(empty($token_value))
                    throw new Exception ("Attention! CSRF token is required.");

                if (Config::get('csrf_on')){
                    if(empty($CSRFToken))
                        throw new Exception ("Attention! CSRF token is required.");
                    $token_key = Session::getObject()->getCSRFDefaultKey();
                    Session::CSRFCheckValidity(array($token_key=>$CSRFToken),true);
                    Session::getObject()->CSRFTokenInvalidation();
                }
            }
            $user = new User();
            $user->username = $content->email;
            $user->email = $content->email;
            $user->first_name = $content->first_name;
            $user->last_name = $content->last_name;
            $user->active = "0";
            $user->join_date = time();
            $user->join_idconfirm = md5(Utils::getRandomString(8));
            $user->pwd = $content->password;;
            //$user->pwd = Utils::passwordGenerator();

                $msg = Template::getMailTemplate('new_pre_user.html',[
                    "help_mail" => Config::get('mail_from'),
                    "fullname" => $user->first_name,
                    "username" => $user->email,
                    "confirm_url" => Config::get('url')."confirm/".$user->join_idconfirm,
                    "logo"=> Config::get('url').Config::get("url_logo"),
                    "hr_mail"=> Config::get('mail_from'),
                    "login_link" => Config::get('url')
                ]);
                if (Config::get('useMailgun')) {
                    $mail = new Email_Mailgun([
                        "from_mail" => Config::get("mail_from"),
                        "from_name" => Config::get("name_from"),
                        "bcc" => Config::get("mail_bcc"),
                        "to" => $user->email,
                        "subject" => Config::get('project_name')." - Confirm your account",
                        "message" => $msg
                    ]); 
                    
                    if (!$mail->send())
                        throw new Exception("Attention! error in sendmail");
                }
            
            $user->save();
            Session::set("id_registered_user",$user->id);

            return array("id"=>$user->id, "token"=>Session::getObject()->CSRFTokenGenerator());
        }
        catch (Exception $e) {
            throw $e;
        }
    }


    protected function registrationBasic()
    {
        $this->constraints("POST",array("CONTENT_TYPE"=>"application/json"), true);
        $res = array();
        try{
            $input_json = str_replace("'","&#039;",$this->file);
            $content = json_decode($input_json);
            #if(empty($content->first_name)) throw new Exception("first_name format not valid");
            #if(empty($content->last_name)) throw new Exception("first_name format not valid");
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
        header("location: " . Utils::getFriendlyUrl("logout"));
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
 
