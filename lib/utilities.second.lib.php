<?
/**
 * Boostack: utilities.second.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

function textescaping($text,$minlenght,$maxlenght,$newlinereplace){
    $text = trim($text);
    $text = substr($text,0,$maxlenght);
    $text = preg_replace("([^ ]{85})",$newlinereplace,$text);
    $text = str_replace(array("\r\n","\n","\r"), $newlinereplace, $text);
    $text = addslashes($text);
    return $text;
}
function datetime_format_string($datetime_sql){
    $array_data = explode(" ",$datetime_sql);
    return date_format_string($array_data[0])." - ".$array_data[1];

}

function datetime_format_string_to_sqlformat($string_with_slash){
    $array_data = explode("/",$string_with_slash);
    return $array_data[2]."-".$array_data[1]."-".$array_data[0];

}

function datetime_format_string_to_slashedformat($datetime_sql){ ## controllare
    $array_data = explode("-",$datetime_sql);
    return $array_data[2]."/".$array_data[1]."/".$array_data[0];

}
function date_format_string_to_slashedformat($date_sql){
    $array_data = explode("-",$date_sql);
    return $array_data[2]."/".$array_data[1]."/".$array_data[0];
}

function getDateTime(){
        $date = mysql_query("SELECT CURDATE() as datas");
        $data = mysql_fetch_array($date);
        $times = mysql_query("SELECT CURTIME() as tempo");
        $time = mysql_fetch_array($times);
        $datetime_now = "$data[datas] $time[tempo]";
        return $datetime_now;
}
function getDateN(){
        $date = mysql_query("SELECT CURDATE() as datas");
        $data = mysql_fetch_array($date);
        $datetime_now = "$data[datas]";
        return $datetime_now;
}
function getTimeN(){
        $times = mysql_query("SELECT CURTIME() as tempo");
        $time = mysql_fetch_array($times);
        $datetime_now = "$time[tempo]";
        return $datetime_now;
}
function getDateN_Month(){
        $t = explode("-",getDateN());
        return $t[1];
}
function getDateN_Day(){
        $t = explode("-",getDateN());
        return $t[2];
}
function getDateN_Year(){
        $t = explode("-",getDateN());
        return $t[0];
}

function getDateTimeTimestamp($datetime_sql){
    list($date, $time) = explode(' ', $datetime_sql);
    list($year, $month, $day) = explode('-', $date);
    list($hour, $minute, $second) = explode(':', $time);
    $timestamp = @mktime($hour, $minute, $second, $month, $day, $year);
    return $timestamp;
}
/*
function getElapsedTime($datetime_timestamp){ #echo"datetima:".getDateTimeTimestamp(getDateTime())."<br>";
    $et = getDateTimeTimestamp(getDateTime()) - $datetime_timestamp;
    $len = strlen("".$et);
    if($et <= 60){
        $res = "$et seconds ago";
    }
    elseif($et <= 3600){
        $t =(int)($et/60);
        $res = ($t > 1)?"$t minutes ago":"$t minute ago";
    }
    elseif($et < 86400){
        $t =(int)($et/3600);
        $res = ($t > 1)?"$t hours ago":"$t hour ago";
    }
    elseif($et >= 86400){
        $t =(int)($et/86400);
        $res = ($t > 1)?"$t days ago":"$t day ago";
    }

    return $res;
}

function sanitizeBadWords($str){
    global $words,$exten;
    $string = explode(' ',strtolower($str));
    $res = "";
    foreach($string as $s){
       if(!in_array($s, $words))
          $res .= " ".$s;
       else
          $res .= " ****";
    }
    return $res;
}
*/		

###########################################  FUNCTIONS	|| (!preg_match("^[a-zA-Z0-9_@.-]+$", $email))

function check_email($email){
    global $lang_register_form;
    $regexp="/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";

    if ($email == "" || !preg_match($regexp, $email)  || strlen($email >= 255)){
        return "<span class=\"message_form_error\">".$lang_register_form["email_error"]."</span>";
    }
    elseif(mysql_num_rows(mysql_query("SELECT id FROM user WHERE email = '".$email."'")) > 0){
        return  "<span class=\"message_form_error\">".$lang_register_form["email_notavailable_error"]."</span>
                <input type=\"hidden\" name=\"mailerr\" id=\"mailerr\" value=\"ok\" />";
    }
    else{
        return "<span class=\"message_form_ok\">".$lang_register_form["email_ok"]."</span>
        <input type=\"hidden\" name=\"mailerr\" id=\"mailerr\" value=\"no\" />";
    }
}

function passwordGenerator($length=9, $strength=0) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

/*

		
function check_password($password){

    global $lang_register_form;
            if (strlen($password) < 6){
                return  "<span class=\"message_form_error\">".$lang_register_form["password_toosmall_error"]."</span>";
            }
            elseif (strlen($password) > 25){
                return  "<span class=\"message_form_error\">".$lang_register_form["password_toobig_error"]."</span>";
            }
            else{
                return "<span class=\"message_form_ok\">".$lang_register_form["password_ok"]."</span>";
            }
}
		
		
function check_username($username){

    $resource = mysql_query("SELECT id FROM user WHERE username = '".$username."'") or die (mysql_error());

    global $lang_register_form;
    if (strlen($username) < 3){
        return  "<span class=\"message_form_error\">".$lang_register_form["username_toosmall_error"]."</span>";
    }
    elseif (strlen($username) > 50){
        return  "<span class=\"message_form_error\">".$lang_register_form["username_toobig_error"]."</span>";
    }
    elseif(mysql_num_rows(mysql_query("SELECT id FROM user WHERE username = '".$username."'")) > 0){
        return  "<span class=\"message_form_error\">".$lang_register_form["username_notavailable_error"]."</span>";
    }
    else{
        return "<span class=\"message_form_ok\">".$lang_register_form["username_ok"]."</span>";
    }
}

		
function check_firstlast($firstlast){

    global $lang_register_form;
    if (strlen($firstlast) < 3){
        return  "<span class=\"message_form_error\">".$lang_register_form["name_toosmall_error"]."</span>";
    }
    elseif (strlen($firstlast) > 50){
        return  "<span class=\"message_form_error\">".$lang_register_form["name_toobig_error"]."</span>";
    }
    else{
        return "<span class=\"message_form_ok\">".$lang_register_form["name_ok"]."</span>";
    }
}

function isMobileBrowser(){
    global $_SERVER;
    $mobile_browser = '0';

    if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i',
        strtolower($_SERVER['HTTP_USER_AGENT']))){
        $mobile_browser++;
        }

    if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or
        ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))){
        $mobile_browser++;
        }

    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
    $mobile_agents = array(
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
        'wapr','webc','winw','winw','xda','xda-');

    if(in_array($mobile_ua,$mobile_agents)){
        $mobile_browser++;
        }
    if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
        $mobile_browser++;
        }
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
        $mobile_browser=0;
        }


    if($mobile_browser>0){
       return true;
    }
    else
        return false;
}

*/
?>