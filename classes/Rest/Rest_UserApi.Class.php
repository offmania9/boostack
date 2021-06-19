<?php
/**
 * Boostack: Rest_UserApi.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */

class Rest_UserApi extends Rest_ApiAbstract
{
    /**
     * @var string
     */
    private $privateKey = "iuRJ-8hcN-nXXc-sT3f";

    /**
     * @var string
     */
    private $hashType = "sha256";

    /**
     * Rest_UserApi constructor.
     * @param $request
     * @param $origin
     */
    public function __construct($request, $origin)
    {
        parent::__construct($request);
    }

    /**
     * @param $getstr
     * @return string
     */
    protected function authenticate($getstr)
    {
        if ($this->method == 'GET') {
			$authid = $getstr[0];
			$token = $getstr[1];
			if ($token == $this->tokenGenerator($authid))
			 return "ok";
			else
				return "error";			
        } else {
            return "Only accepts GET requests";
        }
     }

    /**
     * @param $authid
     * @return string
     */
    protected function tokenGenerator($authid)
    {
         return base64_encode(hash_hmac($this->hashType, $authid, $this->privateKey));
     }
     
 }
?>
 
