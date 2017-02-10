<?php
/**
 * Boostack: CurlRequest.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class CurlRequest {

    private $endpoint = "";
    private $is_post = true;
    private $return_transfer = true;
    private $encoding = "";
    private $getFields = array();
    private $postFields = array();

    public function __construct() {

    }

    public function setEndpoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    public function setIsPost($isPost) {
        $this->is_post = $isPost;
    }

    public function setReturnTransfer($returnTransfer) {
        $this->return_transfer = $returnTransfer;
    }

    public function setGetFields($fields) {
        $this->getFields = $fields;
    }

    public function setPostFields($fields) {
        $this->postFields = $fields;
    }

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

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->return_transfer);
        $curlResult = curl_exec($ch);
        if($curlResult == false) {
            $response->setError(curl_error($ch));
        } else {
            $response->setData($curlResult);
        }
        curl_close($ch);
        return $response;
    }
















}