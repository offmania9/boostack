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
function sanitizeInput($array,$encoding='UTF-8')
{
    if (is_array($array)) {
        $res = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $res[$key] = sanitizeInput($value);
                continue;
            }
            $res[$key] = htmlspecialchars($value,ENT_QUOTES | ENT_HTML401,$encoding);
        }
        return $res;
    } else
        return htmlspecialchars($array,ENT_QUOTES | ENT_HTML401,$encoding);
}

// AUTOLOAD
function autoloadClass($className)
{
    $cn = explode("_", $className);
    $filename = ROOTPATH . "class/";
    $cnt = count($cn);
    if ($cnt == 1)
        $filename .= $className . ".Class.php";
        else {
            $i = 0;
            for ($i; $i < $cnt - 1; $i ++)
                $filename .= $cn[$i] . "/";
                $filename .= $className . ".Class.php";
        }
        if (is_readable($filename))
            require_once ($filename);
}

function getIpAddress()
{
    $ip = getenv('HTTP_CLIENT_IP')?:
    getenv('HTTP_X_FORWARDED_FOR')?:
    getenv('HTTP_X_FORWARDED')?:
    getenv('HTTP_FORWARDED_FOR')?:
    getenv('HTTP_FORWARDED')?:
    getenv('REMOTE_ADDR');
    return $ip;
}

function getUserAgent()
{
    return sanitizeInput($_SERVER["HTTP_USER_AGENT"]);
}

function checkPrivilege($currentUser, $privilegeLevel)
{
    if (! hasPrivilege($currentUser, $privilegeLevel))
        goToError();
}

function checkControllerPrivilege($currentUser, $privilegeLevel)
{
    if (! hasPrivilege($currentUser, $privilegeLevel))
        exit();
}

function hasPrivilege($currentUser, $privilegeLevel)
{
    if ($currentUser == null)
        return false;
    
    if ($currentUser->privilege > $privilegeLevel)
        return false;
    
    return true;
}

function goToError()
{
    global $boostack;
    header("Location: $boostack->url");
    exit();
}

function goToLogout()
{
    global $boostack;
    header("Location: " . $boostack->url . "logout");
    exit();
}

function timeAcceptedFromLastRequest($timeLastRequest)
{
    global $boostack;
    if ($timeLastRequest - time() > $boostack->TIME_ELAPSED_ACCEPTED)
        return false;
    return true;
}

function debug($var)
{
    ini_set('display_errors', 1);
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function removeAccents($string)
{
    $string = trim($string);
    $unwanted_array = array(
        'Š' => 'S',
        'š' => 's',
        'Ž' => 'Z',
        'ž' => 'z',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'A',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'B',
        'ß' => 'Ss',
        'à' => 'a\'',
        'á' => 'a\'',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'a',
        'ç' => 'c',
        'è' => 'e\'',
        'é' => 'e\'',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i\'',
        'í' => 'i\'',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o\'',
        'ó' => 'o\'',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u\'',
        'ú' => 'u\'',
        'û' => 'u',
        'ý' => 'y',
        'ý' => 'y',
        'þ' => 'b',
        'ÿ' => 'y',
        '`' => '\'',
        '’' => '\''
    );
    $string = strtr($string, $unwanted_array);
    return $string;
}

function printPagination($list, $targetURL, $searchParams = "")
{
    $pages = ceil($list->total / $list->for_page);
    
    if ($pages > 1) {
        echo "<ul class='pagination'>";
        if ($list->current_page > 1)
            echo "
                        <li style='display: inline-block; padding: 0; margin: 0;'>
                            <a href='$targetURL" . ($list->current_page - 1) . "$searchParams' aria-label='Previous'>
                                <span aria-hidden='true'>&laquo;</span>
                            </a>
                        </li>
                    ";
        
        for ($i = 1; $i <= $pages; $i ++) {
            $active = $list->current_page == $i ? "class='active'" : "";
            echo "<li $active style='display: inline-block; padding: 0; margin: 0;'>";
            if ($list->current_page != $i)
                echo "<a href='$targetURL" . $i . "$searchParams'>
                                    $i
                                </a>";
            else
                echo "<a>$i</a>";
            echo "</li>";
        }
        
        if ($list->current_page < $pages && $pages > 1)
            echo "
                        <li style='display: inline-block; padding: 0; margin: 0;'>
                            <a href='$targetURL" . ($list->current_page + 1) . "$searchParams' aria-label='Next'>
                                <span aria-hidden='true'>&raquo;</span>
                            </a>
                        </li>
                    ";
        echo "</ul>";
    }
}

function getFileErrorDescription($code)
{
    $errors = array(
        0 => "There is no error, the file uploaded with success",
        1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
        2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        3 => "The uploaded file was only partially uploaded",
        4 => "No file was uploaded",
        6 => "Missing a temporary folder",
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.'
    );
    return $errors[$code];
}

function timestampToDate($timestamp)
{
    global $config;
    return date($config['datetimeFormatString'], $timestamp);
}

function getElapsedTime($datetime_timestamp)
{ 
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

function checkEmailFormat($email) {
    $regexp = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
    if ($email == "" || ! preg_match($regexp, $email) || strlen($email >= 255)){
        return false;
    }
    return true;
}

?>