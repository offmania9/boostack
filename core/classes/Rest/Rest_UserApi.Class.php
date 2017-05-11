<?php
/**
 * Boostack: Rest_UserApi.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
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
 
