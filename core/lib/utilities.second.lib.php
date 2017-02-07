<?php
/**
 * Boostack: utilities.second.lib.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.4
 */
function textescaping($text, $minlenght, $maxlenght, $newlinereplace)
{
    $text = trim($text);
    $text = substr($text, 0, $maxlenght);
    $text = preg_replace("([^ ]{85})", $newlinereplace, $text);
    $text = str_replace(array(
        "\r\n",
        "\n",
        "\r"
    ), $newlinereplace, $text);
    $text = addslashes($text);
    return $text;
}

/*
 * function datetime_format_string($datetime_sql){
 * $array_data = explode(" ",$datetime_sql);
 * return date_format_string($array_data[0])." - ".$array_data[1];
 *
 * }
 */
function datetime_format_string_to_sqlformat($string_with_slash)
{
    $array_data = explode("/", $string_with_slash);
    return $array_data[2] . "-" . $array_data[1] . "-" . $array_data[0];
}

function datetime_format_string_to_slashedformat($datetime_sql)
{ // # controllare
    $array_data = explode("-", $datetime_sql);
    return $array_data[2] . "/" . $array_data[1] . "/" . $array_data[0];
}

function date_format_string_to_slashedformat($date_sql)
{
    $array_data = explode("-", $date_sql);
    return $array_data[2] . "/" . $array_data[1] . "/" . $array_data[0];
}

function getDateTime()
{
    $pdo = Database_PDO::getInstance();
    $date = $pdo->query("SELECT CURDATE() as datas");
    $data = $date->fetch();
    $times = $pdo->query("SELECT CURTIME() as tempo");
    $time = $times->fetch();
    $datetime_now = "$data[datas] $time[tempo]";
    return $datetime_now;
}

function getDateN()
{
    $pdo = Database_PDO::getInstance();
    $date = $pdo->query("SELECT CURDATE() as datas");
    $data = $date->fetch();
    $datetime_now = "$data[datas]";
    return $datetime_now;
}

function getTimeN()
{
    $pdo = Database_PDO::getInstance();
    $times = $pdo->query("SELECT CURTIME() as tempo");
    $time = $times->fetch();
    $datetime_now = "$time[tempo]";
    return $datetime_now;
}

function getDateN_Month()
{
    $t = explode("-", getDateN());
    return $t[1];
}

function getDateN_Day()
{
    $t = explode("-", getDateN());
    return $t[2];
}

function getDateN_Year()
{
    $t = explode("-", getDateN());
    return $t[0];
}

function getDateTimeTimestamp($datetime_sql)
{
    list ($date, $time) = explode(' ', $datetime_sql);
    list ($year, $month, $day) = explode('-', $date);
    list ($hour, $minute, $second) = explode(':', $time);
    $timestamp = @mktime($hour, $minute, $second, $month, $day, $year);
    return $timestamp;
}

function getElapsedTime($datetime_timestamp)
{ // echo"datetima:".getDateTimeTimestamp(getDateTime())."<br>";
    $et = getDateTimeTimestamp(getDateTime()) - $datetime_timestamp;
    $len = strlen("" . $et);
    if ($et <= 60) {
        $res = "$et seconds ago";
    } elseif ($et <= 3600) {
        $t = (int) ($et / 60);
        $res = ($t > 1) ? "$t minutes ago" : "$t minute ago";
    } elseif ($et < 86400) {
        $t = (int) ($et / 3600);
        $res = ($t > 1) ? "$t hours ago" : "$t hour ago";
    } elseif ($et >= 86400) {
        $t = (int) ($et / 86400);
        $res = ($t > 1) ? "$t days ago" : "$t day ago";
    }
    
    return $res;
}

function check_email($email)
{
    $regexp = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
    if ($email == "" || ! preg_match($regexp, $email) || strlen($email >= 255))
        return - 1;
    elseif (Database_PDO::getInstance()->query("SELECT id FROM user WHERE email = '" . $email . "'")->rowCount() > 0)
        return 0;
    else
        return 1;
}

function check_password($password)
{
    $len = strlen($password);
    if ($len < 6 || $len > 25)
        return 0;
    else
        return 1;
}

function check_username($username)
{
    $len = strlen($username);
    if ($len < 3 || $len > 50)
        return - 1;
    elseif (Database_PDO::getInstance()->query("SELECT id FROM user WHERE username = '" . $username . "'")->rowCount() > 0)
        return 0;
    else
        return 1;
}
/*
 * function isMobileBrowser(){
 * global $_SERVER;
 * $mobile_browser = '0';
 *
 * if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i',
 * strtolower($_SERVER['HTTP_USER_AGENT']))){
 * $mobile_browser++;
 * }
 *
 * if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or
 * ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))){
 * $mobile_browser++;
 * }
 *
 * $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
 * $mobile_agents = array(
 * 'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
 * 'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
 * 'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
 * 'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
 * 'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
 * 'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
 * 'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
 * 'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
 * 'wapr','webc','winw','winw','xda','xda-');
 *
 * if(in_array($mobile_ua,$mobile_agents)){
 * $mobile_browser++;
 * }
 * if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
 * $mobile_browser++;
 * }
 * if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
 * $mobile_browser=0;
 * }
 *
 *
 * if($mobile_browser>0){
 * return true;
 * }
 * else
 * return false;
 * }
 */

/*
 * function sanitizeBadWords($str){
 * global $words,$exten;
 * $string = explode(' ',strtolower($str));
 * $res = "";
 * foreach($string as $s){
 * if(!in_array($s, $words))
 * $res .= " ".$s;
 * else
 * $res .= " ****";
 * }
 * return $res;
 * }
 */
?>