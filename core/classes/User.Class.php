<?php
/**
 * Boostack: User.Class.php
 * ========================================================================
 * Copyright 2014-2023 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 4.1
 */
class User implements JsonSerializable {

    /**
     * @var null
     */
    protected $id = null;
    /**
     * @var null|PDO
     */
    protected $pdo = null;
    /**
     * @var array
     */
    protected $objects = [
        User_Entity::class => null,
        User_Social::class => null,
        User_Registration::class => null,
        User_Info::class => null,
    ];

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * Crea una nuova istanza della classe, istanziando i sotto-oggetti.
     */
    public function __construct($id = null) {
        $this->id = $id;
        $this->pdo = Database_PDO::getInstance();
        foreach ($this->objects as $class => &$object) {
            if(empty($object)) {
                $object = new $class();
                foreach($object->getAttributes() as $attribute) {
                    $this->attributes[$attribute] = $class;
                }
            }
        }
    }

    /**
     * Riempie l'oggetto con l'array chiave-valore passato come parametro (invocando __get).
     * Se è presente l'id, lo setta in tutte le sotto-istanze.
     */
    public function fill($array) {
        if(array_key_exists("id",$array)) {
            foreach($this->objects as $object) {
                $object->id = $array["id"];
            }
        }
        foreach($array as $attribute => $value) {
            $this->$attribute = $value;
        }
    }

    /**
     * @param $id
     */
    public function load($id) {
        $this->id = $id;
    }

    /**
     * Salva tutte le istanze sul database, attraverso una transaction.
     * Se è presente l'id, invoca la save delle sotto-istanze.
     * Altrimenti, salva la prima istanza ottenento l'id auto-incrementale e successivamente salva le altre istanze con lo stesso id.
     */
    public function save($forcedID = null) {
        try {
            $this->pdo->beginTransaction();
            if(empty($this->id)) {
                $first = true;
                foreach($this->objects as $object) {
                    if($first) {
                        $object->save($forcedID);
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
            Logger::write($e->getMessage(),Log_Level::ERROR, Log_Driver::FILE);
            throw $e;
        }

    }

    /**
     * Rimuove tutte le sotto-istanze dell'utente dal database.
     */
    public function delete() {
        if(empty($this->id)) throw new Exception("Instance must have 'id' field to be deleted");
        try {
            $this->pdo->beginTransaction();
            foreach($this->objects as $objectInstance) {
                if(empty($objectInstance->id) && $objectInstance->exist($this->id)) {
                    $objectInstance->load($this->id);
                }
                $objectInstance->delete();
            }
            $this->pdo->commit();
        } catch(Exception $e) {
            $this->pdo->rollBack();
            Logger::write($e->getMessage(),Log_Level::ERROR);
        }
    }

    /** Setta un attributo sulla relativa variabile d'istanza.
     * Se è presente l'id ma la relativa istanza non è ancora stata caricata, effettua la load.
     */
    public function __set($property, $value) {
        if(!isset($this->attributes[$property]))
            throw new Exception("Field $property not found");
        $className = $this->attributes[$property];
        $objectInstance = $this->objects[$className];
        if(!empty($this->id) && empty($objectInstance->id)) {
            $objectInstance->load($this->id);
        }
        $objectInstance->$property = $value;
    }

    /**
     * Restituisce il valore di un attributo recuperandolo dalla relativa variabile d'istanza.
     * Se è presente l'id ma la relativa istanza non è ancora stata caricata, effettua la load.
     */
    public function __get($property) {
        if($property == "id") return $this->id;
        if(!isset($this->attributes[$property]))
            throw new Exception("Field $property not found");
        $className = $this->attributes[$property];
        $objectInstance = $this->objects[$className];
        if(!empty($this->id) && empty($objectInstance->id)) {
            $objectInstance->load($this->id);
        }
        return $objectInstance->$property;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(
            $this->objects[User_Entity::class]->jsonSerialize(),
            $this->objects[User_Info::class]->jsonSerialize(),
            $this->objects[User_Social::class]->jsonSerialize(),
            $this->objects[User_Registration::class]->jsonSerialize()
        );

//        return [
//            "user" => $this->objects[User_Entity::class],
//            "user_info" => $this->objects[User_Info::class],
//            "user_social" => $this->objects[User_Social::class],
//            "user_registration" => $this->objects[User_Registration::class],
//        ];
    }

    /**
     * @param $id
     * @param bool $throwException
     * @return bool
     */
    public static function existById($id, $throwException = true) {
        return User_Entity::existById($id, $throwException);
    }

    /**
     * @param $email
     * @param bool $throwException
     * @return bool
     */
    public static function existsByEmail($email, $throwException = true) {
        return User_Entity::existsByEmail($email, $throwException);
    }

    /**
     * @param $username
     * @param bool $throwException
     * @return bool
     */
    public static function existsByUsername($username, $throwException = true) {
        return User_Entity::existsByUsername($username, $throwException);
    }

    /**
     * @param $email
     * @param bool $throwException
     * @return bool
     */
    public static function getUserIDByEmail($email, $throwException = true) {
        return User_Entity::getUserIDByEmail($email, $throwException);
    }

    /**
     * @param $cookieValue
     * @return bool
     */
    public static function getCredentialByCookie($cookieValue) {
        return User_Entity::getCredentialByCookie($cookieValue);
    }

    /**
     * @param $email
     * @return bool
     */
    public static function getActiveCredentialByEmail($email) {
        return User_Entity::getActiveCredentialByEmail($email);
    }

    /**
     * @param $username
     * @return bool
     */
    public static function getActiveCredentialByUsername($username) {
        return User_Entity::getActiveCredentialByUsername($username);
    }

    /**
     * @param $email
     * @param $username
     * @return bool
     */
    public static function getActiveCredentialByEmailOrUsername($email, $username) {
        return User_Entity::getActiveCredentialByEmailOrUsername($email, $username);
    }

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public static function getActiveIdByEmailAndPassword($email, $password) {
        return User_Entity::getActiveIdByEmailAndPassword($email, $password);
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public static function getActiveIdByUsernameAndPassword($username, $password) {
        return User_Entity::getActiveIdByUsernameAndPassword($username, $password);
    }

    /**
     * @param $email
     * @param $username
     * @param $password
     * @return bool
     */
    public static function getActiveIdByEmailOrUsernameAndPassword($email, $username, $password) {
        return User_Entity::getActiveIdByEmailOrUsernameAndPassword($email, $username, $password);
    }

    /**
     *
     */
    public function refreshRememberMeCookie() {
        $cookieHash = Utils::generateCookieHash();
        $this->session_cookie = $cookieHash;
        $this->save();
        setcookie(Config::get("cookie_name"), $cookieHash, time() + Config::get("cookie_expire"), '/');
    }

}