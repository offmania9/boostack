<?php
namespace Core\Models\Utils;
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
     * Checks the format of a string and optionally throws an \Exception if the format is not valid.
     *
     * @param string $string The string to check.
     * @param string $fieldname The name of the field being checked.
     * @param bool $throwException Whether to throw an \Exception if the format is not valid.
     * @return bool True if the string format is valid, false otherwise.
     * @throws \Exception If $throwException is true and the format is not valid.
     */
    public static function checkStringFormat($string, $fieldname = "Password", $throwException = true)
    {
        if ($string == "" || strlen($string) < 6) {
            if ($throwException)
                throw new \Exception("Attention! " . $fieldname . " value is wrong.", 4);
            return false;
        }
        return true;
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
}
