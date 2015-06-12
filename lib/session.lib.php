<?
/**
 * Boostack: session.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

require_once("class/User.Class.php");
require_once("class/HTTPSession.Class.php");
$objSession = new HTTPSession();
$objSession->dbhandle=$db;
$objSession->Impress();
if($checkcookie && isset($_COOKIE[''.$cookiename])){
	$c = sanitizeInput($_COOKIE[''.$cookiename]);
	if(!$objSession->IsLoggedIn() && $c!==""){
		$info_user = mysql_query("SELECT username,pwd FROM boostack_user WHERE session_cookie ='".$c."'");
		if(mysql_num_rows($info_user) == 1){
			$user_info = mysql_fetch_array($info_user);
			$objSession->Login($user_info['username'],"",$user_info['pwd']);
		}
		else{// cookie is set but wrong (manually edited)
			header("Location: logout.php");
            exit();
		}
	}	
}
?>