<?php

/**
 * Boostack: Utils.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */
class Utils
{
    /**
     * Sanitizes input data to prevent XSS attacks.
     *
     * @param array|string $array The input data to be sanitized.
     * @param string $encoding The character encoding (default is 'UTF-8').
     * @return array|string The sanitized input data.
     */
    public static function sanitizeInput($data, $encoding = 'UTF-8')
    {
        if (is_array($data)) {
            return array_map(function ($value) use ($encoding) {
                return self::sanitizeInput($value, $encoding);
            }, $data);
        } elseif ($data !== null) {
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
        } else {
            return $data;
        }
    }


    /**
     * Autoloads classes based on the provided class name.
     *
     * @param string $className The name of the class to autoload.
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
        if (is_readable($pathcore . $filename)) {
            require_once($pathcore . $filename);
        } else if (is_readable($pathcustom . $filename)) {
            require_once($pathcustom . $filename);
        }
    }

    /**
     * Retrieves the IP address of the client.
     *
     * @return array|false|string The IP address of the client.
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
     * Checks if a password meets the criteria for a strong password.
     *
     * @param string $pwd The password to be checked.
     * @return int Returns 1 if the password is strong, 0 otherwise.
     */
    public static function isStrongPassword($pwd)
    {
        return preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $pwd);
    }

    /**
     * Retrieves the User-Agent string from the request headers.
     *
     * @return array|string The User-Agent string.
     */
    public static function getUserAgent()
    {
        return Request::getServerParam("HTTP_USER_AGENT");
    }

    /**
     * Checks if the current user has the specified privilege level.
     *
     * @param mixed $currentUser The current user object.
     * @param int $privilegeLevel The privilege level to be checked.
     */
    public static function checkPrivilege($currentUser, $privilegeLevel)
    {
        if (!self::hasPrivilege($currentUser, $privilegeLevel))
            self::goToError();
    }

    /**
     * Checks if the current user has the specified controller privilege level.
     *
     * @param mixed $currentUser The current user object.
     * @param int $privilegeLevel The privilege level to be checked.
     */
    public static function checkControllerPrivilege($currentUser, $privilegeLevel)
    {
        if (!self::hasPrivilege($currentUser, $privilegeLevel))
            exit();
    }

    /**
     * Checks if the current user has the specified privilege level.
     *
     * @param mixed $currentUser The current user object.
     * @param int $privilegeLevel The privilege level to be checked.
     * @return bool Returns true if the user has the specified privilege level, false otherwise.
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
     * Redirects the user to the error page.
     *
     * @param int|null $status_code The HTTP status code to be used for the error page.
     */
    public static function goToError(int $status_code = NULL)
    {
        header("Location: " . Config::get("url") . "error/" . (empty($status_code) ? "" : $status_code));
        exit();
    }

    /**
     * Redirects the user to the logout page.
     */
    public static function goToLogout()
    {
        header("Location: " . Config::get("url") . "logout");
        exit();
    }

    /**
     * Redirects the user to the login page.
     *
     * @param string|null $callbackURL The URL to redirect after successful login.
     */
    public static function goToLogin($callbackURL = NULL)
    {
        global $objSession;
        if ($callbackURL != NULL) {
            $objSession->loginCallbackURL = $callbackURL;
        }
        header("Location: " . Config::get("url") . "login");
        exit();
    }

    /**
     * Checks if a string is a valid JSON format.
     *
     * @param string $string The string to be checked.
     * @return bool Returns true if the string is a valid JSON, false otherwise.
     */
    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Redirects the user to the specified URL.
     *
     * @param string $URL The URL to redirect to.
     */
    public static function goToUrl($URL)
    {
        header("Location: " . $URL);
        exit();
    }

    /**
     * Redirects the user to the home page.
     */
    public static function goToHome()
    {
        header("Location: " . Config::get("url"));
        exit();
    }

    /**
     * Checks if the time since the last request is within the accepted time limit.
     *
     * @param int|string $timeLastRequest The time of the last request.
     * @return bool Returns true if the time since the last request is within the accepted time limit, false otherwise.
     */
    public static function checkAcceptedTimeFromLastRequest($timeLastRequest)
    {
        if (!is_numeric($timeLastRequest))
            return true;
        $secondsAccepted = Config::get("seconds_accepted_between_requests");
        if ((!empty($timeLastRequest) || $timeLastRequest !== null) && (time() - $timeLastRequest >= $secondsAccepted))
            return true;
        return false;
    }

    /**
     * Generates a friendly URL based on the virtual path provided.
     *
     * @param string $virtualPath The virtual path.
     * @return string The friendly URL.
     */
    public static function getFriendlyUrl($virtualPath)
    {
        if (Config::get('session_on')) {
            $langUrl = Session::get("SESS_LANGUAGE") . "/";
            if (!Config::get('show_default_language_in_URL') && Session::get("SESS_LANGUAGE") == Config::get('language_default'))
                $langUrl = "";
            return Config::get('url') . $langUrl . $virtualPath;
        }
        return Config::get('url') . $virtualPath;
    }

    /**
     * Displays a variable for debugging purposes.
     *
     * @param mixed $var The variable to be debugged.
     */
    public static function debug($var)
    {
        ini_set('display_errors', 1);
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    /**
     * Formats a number as currency amount.
     *
     * @param float|null $number The number to be formatted.
     * @return string The formatted amount. Returns "-" if the number is null.
     */
    public static function formatAmount($number)
    {
        if ($number == null)
            return "-";
        return number_format($number, 2, ",", ".");
    }

    /**
     * Formats a number with thousands separator.
     *
     * @param int|float $number The number to be formatted.
     * @return string The formatted number.
     */
    public static function formatNumber($number)
    {
        return number_format($number, 0, ",", ".");
    }

    /**
     * Removes accents from a string.
     *
     * @param string $string The string from which to remove accents.
     * @return string The string without accents.
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
     * Retrieves the description of a file error code.
     *
     * @param int $code The error code.
     * @return string The description of the error code.
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
     * Converts a UNIX timestamp to a formatted date string.
     *
     * @param int $timestamp The UNIX timestamp.
     * @return string|null The formatted date string or null if timestamp is not greater than 0.
     */
    public static function timestampToDate($timestamp)
    {
        if ($timestamp > 0) {
            $date = new DateTime();
            $date->setTimestamp($timestamp);
            $date->setTimezone(new DateTimeZone('Europe/Rome'));
            return $date->format(Config::get("default_datetime_format"));
        }
    }

    /**
     * Calculates the elapsed time since a given timestamp.
     *
     * @param int $datetime_timestamp The UNIX timestamp of the datetime.
     * @return string The elapsed time string.
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
     * Checks if an email address is in the correct format.
     *
     * @param string $email The email address to be checked.
     * @return bool True if the email address is in correct format, false otherwise.
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
     * Redirects to the maintenance page.
     */
    public static function goToMaintenance()
    {
        header("Location: " . Config::get("url") . Config::get("url_maintenance"));
        exit();
    }
    /**
     * Generates a password string based on given length and strength.
     *
     * @param int $length The length of the password.
     * @param int $strength The strength of the password.
     * @return string The generated password.
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

    /**
     * Generates a random string of specified length.
     *
     * @param int $length The length of the random string.
     * @return string The generated random string.
     */
    public static function getRandomString($length)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLength = strlen($chars) - 1;
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[rand(0, $charsLength)];
        }
        return $randomString;
    }

    /**
     * Generates a secure random string of specified length.
     *
     * @param int $length The length of the random string.
     * @param string $keyspace The characters to choose from.
     * @return string The generated random string.
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
     * Checks the format of a string and optionally throws an exception if the format is not valid.
     *
     * @param string $string The string to check.
     * @param string $fieldname The name of the field being checked.
     * @param bool $throwException Whether to throw an exception if the format is not valid.
     * @return bool True if the string format is valid, false otherwise.
     * @throws Exception If $throwException is true and the format is not valid.
     */
    public static function checkStringFormat($string, $fieldname = "Password", $throwException = true)
    {
        if ($string == "" || strlen($string) < 6) {
            if ($throwException)
                throw new Exception("Attention! " . $fieldname . " value is wrong.", 4);
            return false;
        }
        return true;
    }


    /*
 * Generates the value of the remember-me cookie.
 */
    /**
     * Generates a hash for the remember-me cookie based on current time, IP address, and user agent.
     *
     * @return string The generated cookie hash.
     */
    public static function generateCookieHash()
    {
        return  md5(time()) . md5(Utils::getIpAddress() . Utils::getUserAgent());
    }

    /**
     * Checks the validity of the remember-me cookie hash.
     *
     * @param string $cookieValue The value of the remember-me cookie.
     * @return bool True if the cookie hash is valid, false otherwise.
     */
    public static function checkCookieHashValidity($cookieValue)
    {
        return substr($cookieValue, 32) == md5(Utils::getIpAddress() . Utils::getUserAgent());
    }

    /**
     * Extracts a substring from a string between specified start and end strings.
     *
     * @param string $string The input string.
     * @param string $start The starting string.
     * @param string|null $end The ending string (optional).
     * @return string The extracted substring.
     */
    public static function extractString($string, $start, $end = null)
    {
        if (is_null($end)) {
            $arr = explode($start, $string);
            return $arr[1];
        }
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Extracts a substring from a string between two specified words.
     *
     * @param string $str The input string.
     * @param string $starting_word The starting word.
     * @param string $ending_word The ending word.
     * @return string The extracted substring.
     */
    public static function string_between_two_string($str, $starting_word, $ending_word)
    {
        $arr = explode($starting_word, $str);
        if (isset($arr[1])) {
            $arr = explode($ending_word, $arr[1]);
            return $arr[0];
        }
        return '';
    }

    /**
     * Cleans a string by replacing spaces with hyphens, removing special characters, and reducing multiple hyphens to single ones.
     *
     * @param string $string The input string.
     * @return string The cleaned string.
     */
    public static function cleanStr($string)
    {
        // Replaces all spaces with hyphens.
        $string = str_replace(' ', '-', $string);
        // Removes special characters.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        // Replaces multiple hyphens with single ones.
        $string = preg_replace('/-+/', '-', $string);
        return $string;
    }

    /**
     * Adds a string after every nth occurrence of a character in a given string.
     *
     * @param string $s The input string.
     * @param string $c The character to search for.
     * @param string $n The string to add.
     * @param int $frequency The frequency of occurrence after which the string should be added.
     * @return string The resulting string.
     */
    public static function addStringAfterCharRepeats($s, $c, $n, $frequency)
    {
        $occurrences = 0;
        $result = '';
        for ($i = 0; $i < strlen($s); $i++) {
            $result .= $s[$i];

            if (substr($s, $i, strlen($c)) === $c) {
                $occurrences++;
                if ($occurrences % $frequency === 0) {
                    $result .= $n;
                }
            }
        }
        return $result;
    }

    /**
     * Sets object properties from an array, excluding specified keys, and saves the object.
     *
     * @param object $obj The object to set properties for.
     * @param array $array The array containing property values.
     * @param array $arrayExcluded The keys to exclude from setting as properties.
     * @return void
     */
    public static function setObjFromArray(&$obj, $array, $arrayExcluded = array("id"))
    {
        if (property_exists($obj, "active")) {
            $obj->active = array_key_exists("active", $array) ? 1 : 0;
            $arrayExcluded[] = "active";
        }
        foreach ($array as $key => $value) {
            if (in_array($key, $arrayExcluded)) continue;
            $obj->{$key} = $value;
        }
        $obj->save();
    }
}
