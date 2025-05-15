<?php

use Boostack\Models\Config;
use Boostack\Models\Request;

// insert here your custom logic TO BE EXECUTED DIRECTLY AFTER environment_init and before page controller



# CORS
header("Access-Control-Allow-Origin: " . Config::get('url'));
if (Request::hasRequestParam("REQUEST_METHOD") && Request::getRequestParam("REQUEST_METHOD") === 'OPTIONS') {
    http_response_code(200); // positive response to preflight
    exit;
}
