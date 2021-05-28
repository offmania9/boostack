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
    protected function getTest()
    {
        $res = array();
        if ($this->method == 'GET') {
            $res = array("visible","1");
        } else {
            return "Only accepts GET requests";
        }
        return $res;
    }

    /**
     * @return array|string
     */
    protected function getFilteredData()
    {
        $res = array();
        if ($this->method == 'POST') {
            $res = array("visible","1");
        } else {
            return "Only accepts GET requests";
        }
        return $res;
    }
 }
?>
 
