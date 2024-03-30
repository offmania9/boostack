<?php

/**
 * Boostack: User_Entity.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */
class User_Entity extends BaseClass
{

    protected $active;

    protected $privilege;

    protected $name;

    protected $username;

    protected $pwd;

    protected $email;

    protected $last_access;

    protected $session_cookie;

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
    public function __construct($id = null)
    {
        parent::init($id);
    }

    /**
     * Sets the value of a property, hashing the password property if being set.
     *
     * @param string $property_name The name of the property.
     * @param mixed $val The value of the property.
     */
    public function __set($property_name, $val)
    {
        if ($property_name == "pwd") {
            $val = $this->passwordToHash($val);
        }
        parent::__set($property_name, $val);
    }

    /**
     * Prepares the object for database operations, hashing the password if it is included in the array and is not empty.
     *
     * @param array $array The array containing property values.
     */
    protected function prepare($array = array())
    {
        if (empty($array["id"]) && !empty($array["pwd"])) {
            $array["pwd"] = $this->passwordToHash($array["pwd"]);
        }
        parent::prepare($array);
    }

    /**
     * Serializes the object to JSON, including the 'id' property.
     *
     * @return array The JSON serializable array.
     */
    public function jsonSerialize(): mixed
    {
        $vars = parent::jsonSerialize();
        $vars["id"] = $this->id;
        return $vars;
    }

    /**
     * Hashes a clean password using PHP's built-in password_hash function or sha512 if PHP version is lower than 5.5.0.
     *
     * @param string $cleanPassword The password to hash.
     * @return bool|string The hashed password.
     */
    public function passwordToHash($cleanPassword)
    {
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            return password_hash($cleanPassword, PASSWORD_DEFAULT);
        } else {
            return hash("sha512", $cleanPassword);
        }
    }

    /**
     * Retrieves the user ID associated with a given email address.
     *
     * @param string $email The email address.
     * @param bool $throwException Whether to throw an exception if the email is not found (default: true).
     * @return int|false The user ID if the email is found, false otherwise.
     * @throws Exception If the email is not found and $throwException is true.
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
            if ($throwException) {
                throw new Exception("Attention! User or Email not found.", 0);
            }
            return false;
        }

        return $q2[0];
    }

    /**
     * Checks if a user exists based on their ID.
     *
     * @param int $id The user ID.
     * @param bool $throwException Whether to throw an exception if the user is not found (default: true).
     * @return bool Whether the user exists.
     * @throws Exception If the user is not found and $throwException is true.
     */
    public static function existById($id, $throwException = true)
    {
        $exist = parent::exist($id);
        if (!$exist && $throwException) {
            throw new Exception("User not found.", 3);
        }
        return $exist;
    }

    /**
     * Checks if a user exists based on their email address.
     *
     * @param string $email The email address.
     * @param bool $throwException Whether to throw an exception if the user is not found (default: true).
     * @return bool Whether the user exists.
     * @throws Exception If the user is not found and $throwException is true.
     */
    public static function existsByEmail($email, $throwException = true)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM " . self::TABLENAME . " WHERE email = :email";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->execute();
        if ($q->rowCount() == 0) {
            if ($throwException) {
                throw new Exception("User exists by email or Username or password not valid.", 3);
            }
            return false;
        }
        return true;
    }

    /**
     * Checks if a user exists based on their username.
     *
     * @param string $username The username.
     * @param bool $throwException Whether to throw an exception if the user is not found (default: true).
     * @return bool Whether the user exists.
     * @throws Exception If the user is not found and $throwException is true.
     */
    public static function existsByUsername($username, $throwException = true)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM " . self::TABLENAME . " WHERE username = :username";
        $q = $pdo->prepare($query);
        $q->bindParam(":username", $username);
        $q->execute();
        if ($q->rowCount() == 0) {
            if ($throwException) {
                throw new Exception("Attention! Username or Email not found.", 1);
            }
            return false;
        }
        return true;
    }
    /**
     * Retrieves user credentials (username, password, email) by session cookie value.
     *
     * @param string $cookieValue The session cookie value.
     * @return array|false An array containing user credentials if found, false otherwise.
     */
    public static function getCredentialByCookie($cookieValue)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT username,pwd,email FROM " . self::TABLENAME . " WHERE session_cookie = :cookie ";
        $q = $pdo->prepare($query);
        $q->bindParam(":cookie", $cookieValue);
        $q->execute();
        if ($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    /**
     * Retrieves active user credentials (id, password) by email.
     *
     * @param string $email The email address.
     * @return array|false An array containing active user credentials if found, false otherwise.
     */
    public static function getActiveCredentialByEmail($email)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id,pwd FROM " . self::TABLENAME . " WHERE email = :email AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->execute();
        if ($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    /**
     * Retrieves active user credentials (id, password) by username.
     *
     * @param string $username The username.
     * @return array|false An array containing active user credentials if found, false otherwise.
     */
    public static function getActiveCredentialByUsername($username)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id,pwd FROM " . self::TABLENAME . " WHERE username = :username AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":username", $username);
        $q->execute();
        if ($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    /**
     * Retrieves active user credentials (id, password) by email or username.
     *
     * @param string $email The email address.
     * @param string $username The username.
     * @return array|false An array containing active user credentials if found, false otherwise.
     */
    public static function getActiveCredentialByEmailOrUsername($email, $username)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id,pwd FROM " . self::TABLENAME . " WHERE (username = :username OR email = :email) AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":username", $username);
        $q->bindParam(":email", $email);
        $q->execute();
        if ($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    /**
     * Retrieves the active user ID by email and password.
     *
     * @param string $email The email address.
     * @param string $password The password.
     * @return array|false The active user ID if found, false otherwise.
     */
    public static function getActiveIdByEmailAndPassword($email, $password)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM " . self::TABLENAME . " WHERE email = :email AND pwd = :password AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->bindParam(":password", $password);
        $q->execute();
        if ($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }
    /**
     * Retrieves the active user ID by username and password.
     *
     * @param string $username The username.
     * @param string $password The password.
     * @return array|false The active user ID if found, false otherwise.
     */
    public static function getActiveIdByUsernameAndPassword($username, $password)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM " . self::TABLENAME . " WHERE username = :username AND pwd = :password AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":username", $username);
        $q->bindParam(":password", $password);
        $q->execute();
        if ($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }

    /**
     * Retrieves the active user ID by email or username and password.
     *
     * @param string $email The email address.
     * @param string $username The username.
     * @param string $password The password.
     * @return array|false The active user ID if found, false otherwise.
     */
    public static function getActiveIdByEmailOrUsernameAndPassword($email, $username, $password)
    {
        $pdo = Database_PDO::getInstance();
        $query = "SELECT id FROM " . self::TABLENAME . " WHERE (username = :username OR email = :email) AND pwd = :password AND active = '1' ";
        $q = $pdo->prepare($query);
        $q->bindParam(":email", $email);
        $q->bindParam(":username", $username);
        $q->bindParam(":password", $password);
        $q->execute();
        if ($q->rowCount() == 1) {
            $res = $q->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        }
        return false;
    }
}
