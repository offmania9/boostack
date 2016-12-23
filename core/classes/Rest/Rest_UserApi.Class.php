<?php
/**
 * Boostack: Rest_UserApi.Class.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */
require_once 'classes/Rest/Rest_Api_Abstract.Class.php';
class Rest_UserApi extends Rest_Api_Abstract
{
    private $privateKey = "iuRJ-8hcN-nXXc-sT3f"; 
    
    private $hashType = "sha256";
    
    public function __construct($request, $origin) {
        parent::__construct($request);
    }

//	protected function authentication($getstr) {
//        if ($this->method == 'GET') {
//        	  $strUsername = $getstr[1];
//		 	  $strMD5Password = $getstr[2];
//        	  $stmt = "SELECT id FROM user WHERE username = '$strUsername'
//			  AND md5_pw = '$strMD5Password' AND active='1'";
//		      $result = mysql_query($stmt) or die (mysql_error().": $stmt");
//		      if (mysql_num_rows($result)>0) {
//		        $row = mysql_fetch_array($result);
//				$usr = new User($row[0]);
//			    $this->User = $usr;
//				$token = $this->tokenGenerator($this->User->id);
//                return array("Username"=>$this->User->username,"E-mail"=>$this->User->email,"id"=>$this->User->id, "token"=> $token);
//			  }
//			  return array("Error"=>"User not found.");
//        }
//        else {
//            return "Only accepts GET requests";
//        }
//     }
	
	 protected function authenticate($getstr) {
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
     
     protected function tokenGenerator($authid) {
         return base64_encode(hash_hmac($this->hashType, $authid, $this->privateKey));
     }
     
 }
?>
 
