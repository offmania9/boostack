<?
/**
 * Boostack: check_language.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

	if (!isset($_GET['lang'])) { # if isn't set by user from url
		if($objSession->SESS_LANGUAGE !== ""){ # if is set in the user session
			if(is_file("language/".$objSession->SESS_LANGUAGE.".inc.php")) #if the translation file exists
				include("language/".$objSession->SESS_LANGUAGE.".inc.php");
			else{ # default lang
				include("language/it.inc.php");
				$objSession->SESS_LANGUAGE = "it";
			}
		}
		else{  # if isn't set in the user session, fetch it from browser
			$language = explode(',',sanitizeInput($_SERVER['HTTP_ACCEPT_LANGUAGE'])); 
			$language = strtolower(substr(chop($language[0]),0,2)); 
			include("language/".$language.".inc.php"); 
			$objSession->SESS_LANGUAGE = $language;
		}
	}
	else{ # if is set by user from url
		if(is_file("language/".sanitizeInput($_GET['lang']).".inc.php")){ #if the translation file exists
			include("language/".sanitizeInput($_GET['lang']).".inc.php");
			$objSession->SESS_LANGUAGE = sanitizeInput($_GET['lang']);
		}
		else{ # default lang
			include("language/it.inc.php");
			$objSession->SESS_LANGUAGE = "it";
		}
	}

?>