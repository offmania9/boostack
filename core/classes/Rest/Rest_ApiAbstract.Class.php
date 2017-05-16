<?php
/**
 * Boostack: Rest_Api_Abstract.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.1
 */

abstract class Rest_ApiAbstract
{
    /**
     * @var string
     */
    protected $method = '';

    /**
     * Property: endpoint
     * The Model requested in the URI.
     * eg: /files
     */
    protected $endpoint = '';

    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods.
     * eg: /files/process
     */
    protected $verb = '';

    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource.
     * eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();

    /**
     * Rest_Api_Abstract constructor.
     * @param $request
     * @throws Exception
     */
    public function __construct($request)
    {
        header("Access-Control-Allow-Orgin: *"); // Allow for CORS
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        
        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && ! is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            throw new Exception("Unexpected Header");
        }
        
        switch ($this->method) {
            case 'POST':
                $this->request = Request::getPostArray();
                break;
            case 'GET':
                $this->request = Request::getQueryArray();
                break;
            default:
                $this->_response('Invalid Method', 405);
                break;
        }
    }

    /**
     * @return string
     */
    public function processAPI()
    {
        if ((int)method_exists($this, $this->endpoint) > 0) {
            return $this->_response($this->{$this->endpoint}($this->args));
        }
        return $this->_response("No Endpoint: " . $this->endpoint, 404);
    }

    /**
     * @param $data
     * @param int $status
     * @return string
     */
    private function _response($data, $status = 200)
    {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return json_encode($data);
    }

    /**
     * @param $code
     * @return mixed
     */
    private function _requestStatus($code)
    {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error'
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }
}
?>