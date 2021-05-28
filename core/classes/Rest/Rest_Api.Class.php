<?php
/**
 * Boostack: Rest_Api.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */

class Rest_Api extends Rest_ApiAbstract
{
    /**
     * @return array|string
     */
    protected function getTest() {
        if(strcasecmp($this->method , 'GET') == 0){
            $res = (object) array(
                'message'=>'TEST API:OK'
            );
            return $res;
        } else {
            throw new Exception("Only accepts GET requests");
        }
    }
 }
?>