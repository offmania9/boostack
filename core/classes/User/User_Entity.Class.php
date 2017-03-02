<?php
/**
 * Boostack: User_Entity.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class User_Entity extends BaseClass
{
    protected $active;
    protected $privilege;
    protected $full_name;
    protected $username;
    protected $pwd;
    protected $email;
    protected $last_access;
    protected $session_cookie;
    protected $pic_square;

    const TABLENAME = "boostack_user";

    protected $default_values = [
        "active" => "0",
        "privilege" => 3,
        "full_name" => "",
        "username" => "",
        "pwd" => "",
        "email" => "",
        "last_access" => 0,
        "session_cookie" => "",
        "pic_square" => "",
    ];

    public function __construct($id = null) {
        parent::init($id);
    }

    public function __set($property_name, $val)
    {
        if($property_name == "pwd") {
            $val = $this->passwordToHash($val);
        }
        parent::__set($property_name, $val);
    }

    protected function prepare($array = array())
    {
        if(empty($array["id"]) && !empty($array["pwd"])) {
            $array["pwd"] = $this->passwordToHash($array["pwd"]);
        }
        parent::prepare($array);
    }

    public function jsonSerialize() {
        $vars = parent::jsonSerialize();
        $vars["id"] = $this->id;
        return $vars;
    }

    public function passwordToHash($cleanPassword)
    {
        if (version_compare(PHP_VERSION, '5.5.0') >= 0)
            return password_hash($cleanPassword,PASSWORD_DEFAULT);
        else
            return hash("sha512", $cleanPassword);
    }

    public function getUserIDByEmail($email, $throwException = true)
    {
        $sql = "SELECT id FROM ".self::TABLENAME." WHERE email ='" . $email . "' ";
        $q = $this->pdo->query($sql);
        $q2 = $q->fetch();
        if ($q->rowCount() == 0)
            if ($throwException)
                throw new Exception("Attention! User or Email not found.",0);
        return false;
        
        return $q2[0];
    }

    public static function existsByEmail($email, $throwException = true)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM ".self::TABLENAME." WHERE email = :email";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->execute();
        if($q->rowCount() == 0) {
            if ($throwException)
                throw new Exception("Username or password not valid.",3);
            return false;
        }
        return true;
    }

    public static function existsByUsername($username, $throwException = true)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM ".self::TABLENAME." WHERE username = :username";
        $q = $pdo->prepare($query);
        $q->bindParam(":username", $username);
        $q->execute();
        if($q->rowCount() == 0) {
            if ($throwException)
                throw new Exception("Attention! Username or Email not found.",1);
            return false;
        }
        return true;
    }

    public static function getCredentialByCookie($cookieValue) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT username,pwd FROM ".self::TABLENAME." WHERE session_cookie = :cookie ";
        $q = $pdo->prepare($query);
        $q->bindParam(":cookie", $cookieValue);
        $q->execute();
        if($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    public static function getActiveCredentialByEmail($email) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id,pwd FROM ".self::TABLENAME." WHERE email = :email AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->execute();
        if($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    public static function getActiveCredentialByUsername($username) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id,pwd FROM ".self::TABLENAME." WHERE username = :username AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":username", $username);
        $q->execute();
        if($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    public static function getActiveCredentialByEmailOrUsername($email, $username) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id,pwd FROM ".self::TABLENAME." WHERE (username = :username OR email = :email) AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":username", $username);
        $q->bindParam(":email", $email);
        $q->execute();
        if($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    public static function getActiveIdByEmailAndPassword($email, $password) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM ".self::TABLENAME." WHERE email = :email AND pwd = :password AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->bindParam(":password", $password);
        $q->execute();
        if($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    public static function getActiveIdByUsernameAndPassword($username, $password) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM ".self::TABLENAME." WHERE username = :username AND pwd = :password AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":username", $username);
        $q->bindParam(":password", $password);
        $q->execute();
        if($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    public static function getActiveIdByEmailOrUsernameAndPassword($email, $username, $password) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM ".self::TABLENAME." WHERE (username = :username OR email = :email) AND pwd = :password AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->bindParam(":username", $username);
        $q->bindParam(":password", $password);
        $q->execute();
        if($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }


}

?>