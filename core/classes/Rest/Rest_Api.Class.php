<?php
/**
 * Boostack: Rest_Api.Class.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */
require_once 'classes/Rest/Rest_Api_Abstract.Class.php';
class Rest_Api extends Rest_Api_Abstract
{    
	 protected function getTest() {
	 	$res = array();
	 	if ($this->method == 'GET') {
			$res = array("visible","1");	
		} else {
            return "Only accepts GET requests";
        }
		return $res;
     }
 }
?>
 
