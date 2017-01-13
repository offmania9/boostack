<?php

/** CURL DOCS: http://php.net/manual/en/book.curl.php */

class CurlRequest {

    private $endpoint = "";
    private $is_post = true;
    private $fields = array();
    private $return_transfer = true;
    private $encoding = "";

    public function __construct() {

    }

    public function setEndpoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    public function setIsPost($isPost) {
        $this->is_post = $isPost;
    }

    public function setData($data) {
        $this->fields = $data;
    }

    public function setReturnTransfer($returnTransfer) {
        $this->return_transfer = $returnTransfer;
    }

    public function send() {
        $response = new MessageBag();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_ENCODING , $this->encoding);
        if($this->is_post) {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->fields));
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