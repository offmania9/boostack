<?php

class Rest_ApiRequest extends Abstract_Traced {

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

    protected $created_at;
    protected $last_update;
    protected $last_access;
    protected $created_datetime;

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
        "created_at" => null,
        "last_update" => null,
        "last_access" => null,
        "created_datetime" => null
    ];

    const TABLENAME = "boostack_api_request";

    public function __construct($id = NULL) {
        parent::__construct($id);
        if ($id == NULL) 
            $this->created_datetime = date('Y-m-d H:i:s');
    }

}