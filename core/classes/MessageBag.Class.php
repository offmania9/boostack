<?php
/**
 * Boostack: MessageBag.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class MessageBag implements JsonSerializable
{

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
    public function __construct()
    {
        $this->error = false;
        $this->message = NULL;
        $this->data = NULL;
    }

    /**
     * @param $message
     */
    public function setError($message)
    {
        $this->error = true;
        $this->message = $message;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     *
     */
    public function removeError()
    {
        $this->error = false;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->error;
    }

    /**
     * @return null
     */
    public function getErrorMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            "error" => $this->error,
            "code" => $this->code,
            "message" => $this->message,
            "data" => $this->data,
        ];
    }

    /**
     * @return string
     */
    public function toJSON()
    {
        return json_encode(self::jsonSerialize());
    }
}
?>