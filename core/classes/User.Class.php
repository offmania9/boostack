<?php
/**
 * Boostack: User.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 3.0
 */
class User implements JsonSerializable {

    protected $id = null;
    protected $pdo = null;
    protected $objects = [
        User_Entity::class => null,
        User_Social::class => null,
        User_Registration::class => null,
        User_Info::class => null,
    ];

    protected $attributes = [];

    public function __construct($id = null) {
        $this->id = $id;
        $this->pdo = Database_PDO::getInstance();
        foreach ($this->objects as $class => &$object) {
            if(empty($object)) {
                $object = new $class();
                foreach($object->getAttributes() as $attribute) {
                    $this->attributes[$attribute] = $object;
                }
            }
        }
    }

    public function fill($array) {
        foreach($array as $attribute => $value) {
            $this->$attribute = $value;
        }
    }

    public function load($id) {
        $this->id = $id;
    }

    public function save() {
        try {
            $this->pdo->beginTransaction();
            if(empty($this->id)) {
                $first = true;
                foreach($this->objects as $object) {
                    if($first) {
                        $object->save();
                        $first = false;
                        $this->id = $object->id;
                    } else {
                        $object->save($this->id);
                    }
                }
            } else {
                foreach($this->objects as $object) {
                    if(!empty($object->id)) {
                        $object->save();
                    }
                }
            }
            $this->pdo->commit();
        } catch(Exception $e) {
            $this->pdo->rollBack();
            Boostack::getInstance()->writeLog($e->getMessage(),LogLevel::Error);
            throw $e;
        }

    }

    public function delete() {
        try {
            $this->pdo->beginTransaction();
            foreach($this->objects as $object) {
                $object->delete();
            }
            $this->pdo->commit();
        } catch(Exception $e) {
            $this->pdo->rollBack();
            Boostack::getInstance()->writeLog($e->getMessage(),LogLevel::Error);
        }
    }

    public function __set($property, $value) {
        if(!isset($this->attributes[$property]))
            throw new Exception("Field $property not found");
        $objectInstance = $this->attributes[$property];
        if(!empty($this->id) && empty($objectInstance->id)) {
            $objectInstance->load($this->id);
        }
        $objectInstance->$property = $value;
    }

    public function __get($property) {
        if($property == "id") return $this->id;
        if(!isset($this->attributes[$property]))
            throw new Exception("Field $property not found");
        $objectInstance = $this->attributes[$property];
        if(!empty($this->id) && empty($objectInstance->id)) {
            $objectInstance->load($this->id);
        }
        return $objectInstance->$property;
    }

    public function jsonSerialize()
    {
        return [
            "user" => $this->objects[User_Entity::class],
            "user_info" => $this->objects[User_Info::class],
            "user_social" => $this->objects[User_Social::class],
            "user_registration" => $this->objects[User_Registration::class],
        ];
    }

    public static function existsByEmail($email, $throwException = true) {
        return User_Entity::existsByEmail($email, $throwException);
    }

    public static function existsByUsername($username, $throwException = true) {
        return User_Entity::existsByUsername($username, $throwException);
    }

    public static function getCredentialByCookie($cookieValue) {
        return User_Entity::getCredentialByCookie($cookieValue);
    }

    public static function getActiveCredentialByEmail($email) {
        return User_Entity::getActiveCredentialByEmail($email);
    }

    public static function getActiveCredentialByUsername($username) {
        return User_Entity::getActiveCredentialByUsername($username);
    }

    public static function getActiveCredentialByEmailOrUsername($email, $username) {
        return User_Entity::getActiveCredentialByEmailOrUsername($email, $username);
    }

    public static function getActiveIdByEmailAndPassword($email, $password) {
        return User_Entity::getActiveIdByEmailAndPassword($email, $password);
    }

    public static function getActiveIdByUsernameAndPassword($username, $password) {
        return User_Entity::getActiveIdByUsernameAndPassword($username, $password);
    }

    public static function getActiveIdByEmailOrUsernameAndPassword($email, $username, $password) {
        return User_Entity::getActiveIdByEmailOrUsernameAndPassword($email, $username, $password);
    }

    public function refreshRememberMeCookie() {
        global $boostack;
        $cookieHash = Utils::generateCookieHash();
        $this->session_cookie = $cookieHash;
        $this->save();
        setcookie($boostack->getConfig("cookie_name"), $cookieHash, time() + $boostack->getConfig("cookie_expire"), '/');
    }

}