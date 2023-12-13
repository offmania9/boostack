<?php
/**
 * Boostack: MessageBag.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class MessageBag implements JsonSerializable {

    /**
     * @var bool
     */
    private $error;
    /**
     * @var
     */
    private $code;
    /**
     * @var null
     */
    private $message;
    /**
     * @var null
     */
    private $data;

    /**
     * MessageBag constructor.
     */
    public function __construct() {
        $this->error = false;
        $this->message = NULL;
        $this->data = NULL;
    }

    /**
     *
     */
    public function removeError() {
        $this->error = false;
    }

    /**
     * @return bool
     */
    public function hasError() {
        return $this->error;
    }

    /**
     * @return array
     */
    public function jsonSerialize():mixed {
        return [
            "error" => $this->error,
            "code" => $this->code,
            "message" => $this->message,
            "data" => $this->data,
        ];
    }

    public function toObject() {
        return (object) array(
            'error'=>$this->error,
            "code" => $this->code,
            'message'=>$this->message,
            "data" => $this->data
        );
    }

    /**
     * @return string
     */
    public function toJSON() {
        return json_encode($this->jsonSerialize());
    }


        /**
     * Getter
     *
     * @param $property_name
     * @return mixed
     * @throws Exception
     */
    public function __get($property_name) {
        if (property_exists($this, $property_name)) {
            return $this->$property_name;
        } else {
            throw new Exception("Field $property_name not found");
        }
    }

    /**
     * Setter
     *
     * @param $property_name
     * @param $val
     * @throws Exception
     */
    public function __set($property_name, $val) {
        if (property_exists($this, $property_name)) {
            $this->$property_name = $val;
        } else {
            throw new Exception("Field $property_name not found");
        }
    }
}
?>