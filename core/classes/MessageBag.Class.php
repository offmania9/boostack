<?php

/**
 * Boostack: MessageBag.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

/**
 * Represents a message bag for handling errors and data in a unified format.
 */
class MessageBag implements JsonSerializable
{
    /**
     * @var bool Indicates if an error has occurred.
     */
    private $error;

    /**
     * @var int|null The error code.
     */
    private $code;

    /**
     * @var string|null The error message.
     */
    private $message;

    /**
     * @var mixed|null The data associated with the message.
     */
    private $data;

    /**
     * MessageBag constructor.
     */
    public function __construct()
    {
        $this->error = false;
        $this->message = null;
        $this->data = null;
    }

    /**
     * Removes the error flag.
     */
    public function removeError()
    {
        $this->error = false;
    }

    /**
     * Checks if an error has occurred.
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->error;
    }

    /**
     * Serializes the object to a JSON format.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            "error" => $this->error,
            "code" => $this->code,
            "message" => $this->message,
            "data" => $this->data,
        ];
    }

    /**
     * Converts the object to an stdClass object.
     *
     * @return object
     */
    public function toObject(): object
    {
        return (object) [
            'error' => $this->error,
            "code" => $this->code,
            'message' => $this->message,
            "data" => $this->data
        ];
    }

    /**
     * Converts the object to a JSON string.
     *
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * Getter method to access properties dynamically.
     *
     * @param string $property_name The name of the property to access.
     * @return mixed The value of the property.
     * @throws Exception If the property does not exist.
     */
    public function __get(string $property_name)
    {
        if (property_exists($this, $property_name)) {
            return $this->$property_name;
        } else {
            throw new Exception("Field $property_name not found");
        }
    }

    /**
     * Setter method to set properties dynamically.
     *
     * @param string $property_name The name of the property to set.
     * @param mixed $val The value to set.
     * @throws Exception If the property does not exist.
     */
    public function __set(string $property_name, $val)
    {
        if (property_exists($this, $property_name)) {
            $this->$property_name = $val;
        } else {
            throw new Exception("Field $property_name not found");
        }
    }
}
