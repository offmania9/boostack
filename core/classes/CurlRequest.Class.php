<?php
/**
 * Boostack: CurlRequest.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class CurlRequest {

    /**
     * @var string
     */
    private $endpoint = "";
    /**
     * @var bool
     */
    private $is_post = true;
    /**
     * @var bool
     */
    private $return_transfer = true;
    /**
     * @var string
     */
    private $encoding = "";
    /**
     * @var array
     */
    private $getFields = array();
    /**
     * @var array
     */
    private $postFields = array();
    /**
     * @var array
     */
    private $customHeader = array();

    /**
     * CurlRequest constructor.
     */
    public function __construct() {

    }

    /**
     * @param $endpoint
     */
    public function setEndpoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    /**
     * @param $isPost
     */
    public function setIsPost($isPost) {
        $this->is_post = $isPost;
    }

    /**
     * @param $returnTransfer
     */
    public function setReturnTransfer($returnTransfer) {
        $this->return_transfer = $returnTransfer;
    }

    /**
     * @param $fields
     */
    public function setGetFields($fields) {
        $this->getFields = $fields;
    }

    /**
     * @param $fields
     */
    public function setPostFields($fields) {
        $this->postFields = $fields;
    }

    /**
     * @param $data
     */
    public function setCustomHeader($data) {
        $this->customHeader = $data;
    }

    /**
     * @return MessageBag
     */
    public function send() {
        $response = new MessageBag();

        $endpoint = $this->endpoint;
        if(!empty($this->getFields)) {
            $endpoint = $this->endpoint."?".http_build_query($this->getFields);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_ENCODING , $this->encoding);
        if($this->is_post) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        if(!empty($this->postFields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->postFields));
        }

        if(!empty($this->customHeader)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->customHeader);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->return_transfer);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $curlResult = curl_exec($ch);
        if($curlResult == false) {
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