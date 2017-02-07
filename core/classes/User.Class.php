<?php

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
            throw $e;
            Boostack::getInstance()->writeLog($e->getMessage(),LogLevel::Error);
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
            "user" => $this->objects[User::class],
            "userinfo" => $this->objects[User_Info::class],
        ];
    }

    public function tryLogin($username, $password, $cookieRememberMe, $throwException = true) {
        return $this->objects[User_Entity::class]->tryLogin($username, $password, $cookieRememberMe, $throwException);
    }

}