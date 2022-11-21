<?php
/**
 * Boostack: Utils.Class.php
 * ========================================================================
 * Copyright 2014-2023 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.1
 */
class Utils
{
    /**
     * @param $array
     * @param string $encoding
     * @return array|string
     */
    public static function sanitizeInput($array, $encoding = 'UTF-8')
    {
        if (is_array($array)) {
            $res = array();
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $res[$key] = self::sanitizeInput($value);
                    continue;
                }
                $res[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, $encoding);
            }
            return $res;
        } else
            return htmlspecialchars($array, ENT_QUOTES | ENT_HTML401, $encoding);
    }

    /**
     * @param $className
     */
    public static function autoloadClass($className)
    {
        $cn = explode("_", $className);
        $pathcore = ROOTPATH . "../core/classes/";
        $pathcustom = ROOTPATH . "../classes/";
        $filename = "";
        $cnt = count($cn);
        if ($cnt == 1) {
            $filename .= $className . ".Class.php";
        } else {
            $i = 0;
            for ($i; $i < $cnt - 1; $i++)
                $filename .= $cn[$i] . "/";
            $filename .= $className . ".Class.php";
            
        }
        if (is_readable($pathcore . $filename)){
            require_once($pathcore . $filename);
        }       
        else
            if (is_readable($pathcustom . $filename))
                require_once($pathcustom . $filename);
    }

    /**
     * @return array|false|string
     */
    public static function getIpAddress()
    {
        $ip = getenv('HTTP_CLIENT_IP') ?:
            getenv('HTTP_X_FORWARDED_FOR') ?:
                getenv('HTTP_X_FORWARDED') ?:
                    getenv('HTTP_FORWARDED_FOR') ?:
                        getenv('HTTP_FORWARDED') ?:
                            getenv('REMOTE_ADDR');
        return $ip;
    }

    /**
     * @param $pwd
     * @return int
     */
    public static function isStrongPassword($pwd){
        return preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $pwd);
    }

    /**
     * @return array|string
     */
    public static function getUserAgent()
    {
        return Request::getServerParam("HTTP_USER_AGENT");
    }

    /**
     * @param $currentUser
     * @param $privilegeLevel
     */
    public static function checkPrivilege($currentUser, $privilegeLevel)
    {
        if (!self::hasPrivilege($currentUser, $privilegeLevel))
        self::goToError();
    }

    /**
     * @param $currentUser
     * @param $privilegeLevel
     */
    public static function checkControllerPrivilege($currentUser, $privilegeLevel)
    {
        if (!self::hasPrivilege($currentUser, $privilegeLevel))
            exit();
    }

    /**
     * @param $currentUser
     * @param $privilegeLevel
     * @return bool
     */
    public static function hasPrivilege($currentUser, $privilegeLevel)
    {
        if ($currentUser == null)
            return false;

        if ($currentUser->privilege > $privilegeLevel)
            return false;

        return true;
    }

    /**
     *
     */
    public static function goToError(int $status_code = NULL)
    {
        
        header("Location: ".Config::get("url")."error/".(empty($status_code)?"":$status_code));
        exit();
    }

    /**
     *
     */
    public static function goToLogout()
    {
        global $boostack;
        header("Location: " . $boostack->url . "logout");
        exit();
    }

    /**
     * @param null $callbackURL
     */
    public static function goToLogin($callbackURL = NULL)
    {
        global $boostack, $objSession;
        if ($callbackURL != NULL) {
            $objSession->loginCallbackURL = $callbackURL;
        }
        header("Location: " . $boostack->url . "login");
        exit();
    }

    /**
     * @param $string
     */
    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $URL
     */
    public static function goToUrl($URL)
    {
        header("Location: " . $URL);
        exit();
    }

    /**
     * @param $timeLastRequest
     * @return bool
     */
    public static function checkAcceptedTimeFromLastRequest($timeLastRequest)
    {
        if(!is_numeric($timeLastRequest))
            return true;
        $secondsAccepted = Config::get("seconds_accepted_between_requests");
        if ((!empty($timeLastRequest) || $timeLastRequest !== null) && (time() - $timeLastRequest >= $secondsAccepted))
            return true;
        return false;
    }

        /**
     * @param $virtualPath
     * @return string
     */
    public static function getFriendlyUrl($virtualPath)
    {
        if(Config::get('session_on')) {
            $langUrl = Session::get("SESS_LANGUAGE")."/";
            if(!Config::get('show_default_language_in_URL') && Session::get("SESS_LANGUAGE") == Config::get('language_default'))
                $langUrl = "";
            return Config::get('url') . $langUrl . $virtualPath;
        }
        return Config::get('url') . $virtualPath;
    }

    /**
     * @param $var
     */
    public static function debug($var)
    {
        ini_set('display_errors', 1);
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    public static function formatAmount($number) {
        return number_format($number, 2, ",", ".");
    }
    
    public static function formatNumber($number) {
        return number_format($number, 0, ",", ".");
    }

    /**
     * @param $string
     * @return string
     */
    public static function removeAccents($string)
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

    /**
     * @param $code
     * @return mixed
     */
    public static function getFileErrorDescription($code)
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

    /**
     * @param $timestamp
     * @return string
     */
    public static function timestampToDate($timestamp)
    {
        if ($timestamp > 0) {
            //return date(Config::get("default_datetime_format"), $timestamp);
            $date = new DateTime();
            $date->setTimestamp($timestamp);
            $date->setTimezone(new DateTimeZone('Europe/Rome'));
            return $date->format(Config::get("default_datetime_format"));
        }
    }

    /**
     * @param $datetime_timestamp
     * @return string
     */
    public static function getElapsedTime($datetime_timestamp)
    {
        $et = getDateTimeTimestamp(getDateTime()) - $datetime_timestamp;
        $len = strlen("" . $et);
        if ($et <= 60) {
            $res = "$et seconds ago";
        } elseif ($et <= 3600) {
            $t = (int)($et / 60);
            $res = ($t > 1) ? "$t minutes ago" : "$t minute ago";
        } elseif ($et < 86400) {
            $t = (int)($et / 3600);
            $res = ($t > 1) ? "$t hours ago" : "$t hour ago";
        } elseif ($et >= 86400) {
            $t = (int)($et / 86400);
            $res = ($t > 1) ? "$t days ago" : "$t day ago";
        }
        return $res;
    }

    /**
     * @param $email
     * @return bool
     */
    public static function checkEmailFormat($email)
    {
        $regexp = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
        if ($email == "" || !preg_match($regexp, $email) || strlen($email >= 255)) {
            return false;
        }
        return true;
    }

    /**
     *
     */
    public static function goToMaintenance()
    {
        $boostack = Boostack::getInstance();
        header("Location: " . $boostack->url . Config::get("url_maintenance"));
        exit();
    }

    /**
     * @param int $length
     * @param int $strength
     * @return string
     */
    public static function passwordGenerator($length = 9, $strength = 0)
    {
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
        for ($i = 0; $i < $length; $i ++) {
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

    /**
     * @param $length
     * @return string
     */
    public static function getRandomString($length)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLength = strlen($chars) - 1;
        $randomString = '';
        for ($i = 0; $i < $length; $i ++) {
            $randomString .= $chars[rand(0, $charsLength)];
        }
        return $randomString;
    }

    /**
     * @param $length
     * @param string $keyspace
     * @return string
     */
    public static function getSecureRandomString($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[mt_rand(0, $max)];
        }
        return $str;
    }

    /**
     * @param $string
     * @param string $fieldname
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public static function checkStringFormat($string, $fieldname="Password", $throwException = true)
    {
        if ($string == "" || strlen($string) < 6){
            if ($throwException)
                throw new Exception("Attention! ".$fieldname." value is wrong.",4);
            return false;
        }
        return true;
    }

    /*
    *  Genera il valore del remember-me cookie
    */
    /**
     * @return string
     */
    public static function generateCookieHash()
    {
        return  md5(time()).md5(Utils::getIpAddress() . Utils::getUserAgent());
    }

    /**
     * @param $cookieValue
     * @return bool
     */
    public static function checkCookieHashValidity($cookieValue)
    {
        return substr($cookieValue,32) == md5(Utils::getIpAddress().Utils::getUserAgent());
    }

}