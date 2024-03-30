<?php

/**
 * Boostack: CurlRequest.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5
 */
class CurlRequest
{
    /**
     * The endpoint URL.
     *
     * @var string
     */
    private $endpoint = "";

    /**
     * Indicates if the request is a POST request.
     *
     * @var bool
     */
    private $is_post = true;

    /**
     * Indicates if the transfer should be returned as a string.
     *
     * @var bool
     */
    private $return_transfer = true;

    /**
     * The encoding to be used in the request.
     *
     * @var string
     */
    private $encoding = "";

    /**
     * The GET fields to be sent with the request.
     *
     * @var array
     */
    private $getFields = array();

    /**
     * The POST fields to be sent with the request.
     *
     * @var array
     */
    private $postFields = array();

    /**
     * Custom headers to be included in the request.
     *
     * @var array
     */
    private $customHeader = array();

    /**
     * Constructs a new CurlRequest instance.
     */
    public function __construct()
    {
    }

    /**
     * Sets the endpoint URL for the request.
     *
     * @param string $endpoint The endpoint URL.
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Sets whether the request is a POST request.
     *
     * @param bool $isPost Indicates if the request is a POST request.
     */
    public function setIsPost($isPost)
    {
        $this->is_post = $isPost;
    }

    /**
     * Sets whether the transfer should be returned as a string.
     *
     * @param bool $returnTransfer Indicates if the transfer should be returned as a string.
     */
    public function setReturnTransfer($returnTransfer)
    {
        $this->return_transfer = $returnTransfer;
    }

    /**
     * Sets the GET fields to be sent with the request.
     *
     * @param array $fields The GET fields.
     */
    public function setGetFields($fields)
    {
        $this->getFields = $fields;
    }

    /**
     * Sets the POST fields to be sent with the request.
     *
     * @param array $fields The POST fields.
     */
    public function setPostFields($fields)
    {
        $this->postFields = $fields;
    }

    /**
     * Sets custom headers to be included in the request.
     *
     * @param array $data The custom headers.
     */
    public function setCustomHeader($data)
    {
        $this->customHeader = $data;
    }

    /**
     * Sends the HTTP request and returns the response.
     *
     * @return MessageBag The response object.
     */
    public function send()
    {
        $response = new MessageBag();

        $endpoint = $this->endpoint;
        if (!empty($this->getFields)) {
            $endpoint = $this->endpoint . "?" . http_build_query($this->getFields);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_ENCODING, $this->encoding);
        if ($this->is_post) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        if (!empty($this->postFields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->postFields));
        }

        if (!empty($this->customHeader)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->customHeader);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->return_transfer);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $curlResult = curl_exec($ch);
        if ($curlResult === false) {
            $response->error = true;
            $response->data = curl_error($ch);
        } else {
            $response->error = false;
            $response->data = $curlResult;
        }
        curl_close($ch);
        return $response;
    }
}
