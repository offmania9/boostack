<?php
namespace Core\Models\Rest;
/**
 * Boostack: Rest_ApiRequest.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */
class Rest_ApiRequest extends \Core\Models\BaseClassTraced
{

    protected $id;
    protected $method;
    protected $endpoint;
    protected $verb;
    protected $error;
    protected $code;
    protected $message;
    protected $client_code;
    protected $app_code;
    protected $user_code;
    protected $get_args;
    protected $post_args;
    protected $file_args;
    protected $remote_address;
    protected $remote_user_agent;

    protected $output;

    protected $default_values = [
        "id" => null,
        "method" => null,
        "endpoint" => null,
        "verb" => null,
        "error" => null,
        "code" => null,
        "message" => null,
        "client_code" => null,
        "app_code" => null,
        "user_code" => null,
        "get_args" => null,
        "post_args" => null,
        "file_args" => null,
        "remote_address" => null,
        "remote_user_agent" => null,
        "output" => null,
    ];

    const TABLENAME = "boostack_api_request";

    public function __construct($id = NULL)
    {
        parent::__construct($id);
    }
}
