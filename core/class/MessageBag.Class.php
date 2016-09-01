<?php
/**
 * Boostack: MessageBag.Class.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */
class MessageBag implements JsonSerializable {

    private $error;
    private $message;
    private $data;

    public function __construct() {
        $this->error = false;
        $this->message = NULL;
        $this->data = NULL;
    }

    public function setError($message) {
        $this->error = true;
        $this->message = $message;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function removeError() {
        $this->error = false;
    }
    public function hasError() {
        return $this->error;
    }

    public function jsonSerialize() {
        return [
            "error" => $this->error,
            "message" => $this->message,
            "data" => $this->data,
        ];
    }


}

?>