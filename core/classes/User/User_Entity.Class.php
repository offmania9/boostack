<?php
/**
 * Boostack: User_Entity.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class User_Entity extends BaseClass
{
    /**
     * @var
     */
    protected $active;
    /**
     * @var
     */
    protected $privilege;
    /**
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $username;
    /**
     * @var
     */
    protected $pwd;
    /**
     * @var
     */
    protected $email;
    /**
     * @var
     */
    protected $last_access;
    /**
     * @var
     */
    protected $session_cookie;
    /**
     * @var
     */
    protected $pic_square;

    /**
     *
     */
    const TABLENAME = "boostack_user";

    /**
     * @var array
     */
    protected $default_values = [
        "active" => "0",
        "privilege" => 3,
        "name" => "",
        "username" => "",
        "pwd" => "",
        "email" => "",
        "last_access" => 0,
        "session_cookie" => "",
        "pic_square" => "",
    ];

    /**
     * User_Entity constructor.
     * @param null $id
     */
    public function __construct($id = null) {
        parent::init($id);
    }

    /**
     * @param $property_name
     * @param $val
     */
    public function __set($property_name, $val)
    {
        if($property_name == "pwd") {
            $val = $this->passwordToHash($val);
        }
        parent::__set($property_name, $val);
    }

    /**
     * @param array $array
     */
    protected function prepare($array = array())
    {
        if(empty($array["id"]) && !empty($array["pwd"])) {
            $array["pwd"] = $this->passwordToHash($array["pwd"]);
        }
        parent::prepare($array);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize():mixed {
        $vars = parent::jsonSerialize();
        $vars["id"] = $this->id;
        return $vars;
    }

    /**
     * @param $cleanPassword
     * @return bool|string
     */
    public function passwordToHash($cleanPassword)
    {
        if (version_compare(PHP_VERSION, '5.5.0') >= 0)
            return password_hash($cleanPassword,PASSWORD_DEFAULT);
        else
            return hash("sha512", $cleanPassword);
    }

    /**
     * @param $email
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public static function getUserIDByEmail($email, $throwException = true)
    {
        $pdo = Database_PDO::getInstance();
        $sql = "SELECT id FROM " . static::TABLENAME . " WHERE email = :email";
        $q = $pdo->prepare($sql);
        $q->bindValue(':email', $email);
        $q->execute();
        $q2 = $q->fetch();
        if ($q->rowCount() == 0) {
            if ($throwException)
                throw new Exception("Attention! User or Email not found.", 0);
            return false;
        }
        
        return $q2[0];
    }

    /**
     * @param $id
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public static function existById($id, $throwException = true)
    {
        $exist = parent::exist($id);
        if(!$exist && $throwException)
            throw new Exception("User not found.",3);
        return $exist;
    }

    /**
     * @param $email
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public static function existsByEmail($email, $throwException = true)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM ".self::TABLENAME." WHERE email = :email";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->execute();
        if($q->rowCount() == 0) {
            if ($throwException)
                throw new Exception("User exists by email or Username or password not valid.",3);
            return false;
        }
        return true;
    }

    /**
     * @param $username
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
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

    /**
     * @param $cookieValue
     * @return bool
     */
    public static function getCredentialByCookie($cookieValue) {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT username,pwd,email FROM ".self::TABLENAME." WHERE session_cookie = :cookie ";
        $q = $pdo->prepare($query);
        $q->bindParam(":cookie", $cookieValue);
        $q->execute();
        if($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    /**
     * @param $email
     * @return bool
     */
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

    /**
     * @param $username
     * @return bool
     */
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

    /**
     * @param $email
     * @param $username
     * @return bool
     */
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

    /**
     * @param $email
     * @param $password
     * @return bool
     */
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

    /**
     * @param $username
     * @param $password
     * @return bool
     */
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

    /**
     * @param $email
     * @param $username
     * @param $password
     * @return bool
     */
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
