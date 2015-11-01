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

#require_once("class/User.Class.php");
#require_once("class/HTTPSession.Class.php");
$objSession = new Session_HTTP();
$objSession->Impress();
if($config['cookie_on'] && isset($_COOKIE[''.$cookiename])){
	$c = sanitizeInput($_COOKIE[''.$config['cookie_name']]);
	if(!$objSession->IsLoggedIn() && $c!==""){
		if (!$objSession->loginByCookie($c)) {// cookie is set but wrong (manually edited)
			$boostack->logout();
			header("Location: ".$boostack->url);
		}
	}	
}
define('CURRENTUSER',$objSession->GetUserObject());
?>