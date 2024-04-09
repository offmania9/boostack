<?php
namespace Core\Models\Curl;
/**
 * Boostack: CurlRequest.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

/**
 * CurlRequest class for making cURL requests.
 */
class CurlRequest
{

    /**
     * @var string The URL endpoint for the cURL request.
     */
    private $endpoint = "";

    /**
     * @var bool Indicates whether the request is a POST request.
     */
    private $is_post = true;

    /**
     * @var bool Indicates whether to return the transfer as a string.
     */
    private $return_transfer = true;

    /**
     * @var string The encoding to be used in the cURL request.
     */
    private $encoding = "";

    /**
     * @var array An array of GET fields for the cURL request.
     */
    private $getFields = array();

    /**
     * @var array An array of POST fields for the cURL request.
     */
    private $postFields = array();

    /**
     * @var array An array of custom headers for the cURL request.
     */
    private $customHeader = array();

    /**
     * @var string The User-Agent header value.
     */
    private $userAgent = 'Boostack Curl';

    /**
     * Constructor for CurlRequest class.
     */
    public function __construct()
    {
    }

    /**
     * Adds a custom header to the cURL request.
     *
     * @param mixed $data The header data to be added.
     */
    public function addHeader($data)
    {
        $this->customHeader[] = $data;
    }

    /**
     * Sets the URL endpoint for the cURL request.
     *
     * @param string $endpoint The URL endpoint to be set.
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Sets whether the request is a POST request.
     *
     * @param bool $isPost A boolean indicating whether the request is a POST request.
     */
    public function setIsPost(bool $isPost)
    {
        $this->is_post = $isPost;
    }

    /**
     * Sets the Content-Type header to application/json if specified.
     *
     * @param bool $isContentTypeJSON A boolean indicating whether to set Content-Type to application/json.
     *                                 If true, sets the Content-Type header to application/json.
     */
    public function setContentTypeJSON(bool $isContentTypeJSON)
    {
        if ($isContentTypeJSON)
            $this->customHeader["contentTypeJson"] = "Content-Type: application/json";
        else {
            if (!empty($this->customHeader["contentTypeJson"]))
                unset($this->customHeader["contentTypeJson"]);
        }
    }

      /**
     * Sets the User-Agent header value.
     *
     * @param string $userAgent The User-Agent header value to be set.
     */
    public function setUserAgent(string $userAgent) {
        $this->userAgent = $userAgent;
    }

    /**
     * Sets whether to return the transfer as a string.
     *
     * @param mixed $returnTransfer A boolean indicating whether to return the transfer as a string.
     */
    public function setReturnTransfer($returnTransfer)
    {
        $this->return_transfer = $returnTransfer;
    }

    /**
     * Sets the GET fields for the cURL request.
     *
     * @param array $fields An array of GET fields to be set.
     */
    public function setGetFields($fields)
    {
        $this->getFields = $fields;
    }

    /**
     * Sets the POST fields for the cURL request.
     *
     * @param array $fields An array of POST fields to be set.
     */
    public function setPostFields($fields)
    {
        $this->postFields = $fields;
    }

    /**
     * Sets the custom header for the cURL request.
     *
     * @param mixed $data The custom header data to be set.
     */
    public function setCustomHeader($data)
    {
        $this->customHeader = $data;
    }

    /**
     * Gets the cURL command string for debugging purposes.
     *
     * @return string The cURL command string.
     */
    public function getCurlString()
    {
        $r = 'curl ' . $this->endpoint . ' \\';
        foreach ($this->customHeader as $h) {
            $r .=  ' -H "' . $h . '" \\';
        }
        $r .=  " -d '" . json_encode($this->postFields) . "'";

        return $r;
    }

    /**
     * Sends the cURL request and returns the response.
     *
     * @return MessageBag The response from the cURL request.
     */

     public function send() {
        $response = new \Core\Models\MessageBag();

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
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postFields);
        }

        if(!empty($this->customHeader)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->customHeader);
        }

        if(!empty($this->userAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->return_transfer);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $curlResult = curl_exec($ch);
        if($curlResult === false) {
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
