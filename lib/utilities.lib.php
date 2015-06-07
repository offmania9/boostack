<?
/**
 * Boostack: utilities.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

function sanitizeInput($array){
	if(is_array($array)){
		$res = array();
		foreach($array as $key => $value){
			if(is_array($value)){
				$res[$key] = sanitizeInput($value); 
				continue;
			}
			$res[$key] = addslashes(htmlspecialchars($value));
		}
		return $res;
	}
	else
		return addslashes(htmlspecialchars($array));
}		
		
function getIpAddress(){
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
      $ip = $_SERVER['REMOTE_ADDR'];
    
	return sanitizeInput($ip);
}	

?>