<?php
/**
 * Boostack: Utils.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.1
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
        $pathcore = ROOTPATH . "core/classes/";
        $pathcustom = ROOTPATH . "classes/";
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
        if (is_readable($pathcustom . $filename))
            require_once($pathcustom . $filename);
        else
            if (is_readable($pathcore . $filename))
                require_once($pathcore . $filename);
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
     * @return array|string
     */
    public static function getUserAgent()
    {
        return Request::hasServerParam("HTTP_USER_AGENT") ? Request::getServerParam("HTTP_USER_AGENT") : null;
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
    public static function goToError()
    {
        $url = Config::get("url");
        header("Location: $url");
        exit();
    }

    /**
     *
     */
    public static function goToLogout()
    {
        $url = Config::get("url");
        header("Location: " . $url . "logout");
        exit();
    }

    /**
     * @param null $callbackURL
     */
    public static function goToLogin($callbackURL = NULL)
    {
        $url = Config::get("url");
        if ($callbackURL != NULL) {
            Session::set("loginCallbackURL",$callbackURL);
        }
        header("Location: " . $url . "login");
        exit();
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
        $secondsAccepted = Config::get("seconds_accepted_between_requests");
        if ((!empty($timeLastRequest) || $timeLastRequest !== null) && (time() - $timeLastRequest >= $secondsAccepted))
            return true;
        return false;
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


    public static function datetimeToTimestamp($inputDate, $originTimezone = "Europe/Rome", $resultTimezone = "Europe/Rome") {
        $date = new DateTime($inputDate,new DateTimeZone($originTimezone));
        $date->setTimezone(new DateTimeZone($resultTimezone));
        return $date->getTimestamp();
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
     *
     */
    public static function goToMaintenance()
    {
        header("Location: " . Config::get ("url"). Config::get("url_maintenance"));
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

}