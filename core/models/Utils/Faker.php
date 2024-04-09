<?php
namespace Core\Models\Utils;
/**
 * Boostack: Faker.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

class Faker
{
    /**
     * Generates a random string of specified length.
     * Useful for generating usernames, passwords, etc.
     * 
     * @param int $length The length of the string to generate.
     * @return string A random string.
     */
    public static function string($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Generates a random integer between the specified minimum and maximum.
     * 
     * @param int $min The minimum value.
     * @param int $max The maximum value.
     * @return int A random integer.
     */
    public static function integer($min = 0, $max = 1000)
    {
        return rand($min, $max);
    }

    /**
     * Generates a random float between the specified minimum and maximum.
     * 
     * @param float $min The minimum value.
     * @param float $max The maximum value.
     * @param int $decimals The number of decimal places.
     * @return float A random float.
     */
    public static function float($min = 0, $max = 1000, $decimals = 2)
    {
        $scale = pow(10, $decimals);
        return rand($min * $scale, $max * $scale) / $scale;
    }

    /**
     * Generates an array of random strings.
     * 
     * @param int $numItems The number of strings to generate.
     * @param int $stringLength The length of each string.
     * @return array An array of random strings.
     */
    public static function stringArray($numItems = 5, $stringLength = 10)
    {
        $array = [];
        for ($i = 0; $i < $numItems; $i++) {
            $array[] = self::string($stringLength);
        }
        return $array;
    }

    /**
     * Generates an array of random integers.
     * 
     * @param int $numItems The number of integers to generate.
     * @param int $min The minimum value for each integer.
     * @param int $max The maximum value for each integer.
     * @return array An array of random integers.
     */
    public static function integerArray($numItems = 5, $min = 0, $max = 1000)
    {
        $array = [];
        for ($i = 0; $i < $numItems; $i++) {
            $array[] = self::integer($min, $max);
        }
        return $array;
    }

    /**
     * Generates an array of random floats.
     * 
     * @param int $numItems The number of floats to generate.
     * @param float $min The minimum value for each float.
     * @param float $max The maximum value for each float.
     * @param int $decimals The number of decimal places for each float.
     * @return array An array of random floats.
     */
    public static function floatArray($numItems = 5, $min = 0, $max = 1000, $decimals = 2)
    {
        $array = [];
        for ($i = 0; $i < $numItems; $i++) {
            $array[] = self::float($min, $max, $decimals);
        }
        return $array;
    }

    /**
     * Generates a random email address.
     * 
     * @return string A random email address.
     */
    public static function email()
    {
        $domains = ['example.com', 'mail.com', 'test.org', 'faker.net'];
        return self::string(10) . '@' . $domains[array_rand($domains)];
    }

    /**
     * Generates a random phone number.
     * 
     * @return string A random phone number.
     */
    public static function phoneNumber()
    {
        return '+39 ' . rand(310, 399) . ' ' . rand(1000000, 9999999);
    }

    /**
     * Generates a random date and time.
     * 
     * @param string $format The format of the date and time.
     * @param string $start The start date for the random date generation.
     * @param string $end The end date for the random date generation.
     * @return string A random date and time in the specified format.
     */
    public static function dateTime($format = 'Y-m-d H:i:s', $start = '-30 years', $end = 'now')
    {
        $startDate = strtotime($start);
        $endDate = strtotime($end);
        $timestamp = rand($startDate, $endDate);
        return date($format, $timestamp);
    }

    /**
     * Generates a random URL.
     * 
     * @return string A random URL.
     */
    public static function url()
    {
        return 'https://www.' . self::string(10) . '.com';
    }

    /**
     * Generates a vector typical of vector embeddings, useful in machine learning contexts.
     * 
     * @param int $length The length of the vector.
     * @param float $min The minimum value for each component of the vector.
     * @param float $max The maximum value for each component of the vector.
     * @param int $decimals The number of decimal places for each component.
     * @return array A vector of floats.
     */
    public static function vector($length = 50, $min = -1, $max = 1, $decimals = 3)
    {
        $vector = [];
        for ($i = 0; $i < $length; $i++) {
            $scale = pow(10, $decimals);
            $randomFloat = rand($min * $scale, $max * $scale) / $scale;
            $vector[] = $randomFloat;
        }
        return $vector;
    }
}
