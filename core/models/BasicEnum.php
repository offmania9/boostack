<?php
namespace Core\Models;
/**
 * Boostack: BasicEnum.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */

abstract class BasicEnum
{
    private static $constCacheArray = NULL;

    public static function getConstants()
    {
        if (self::$constCacheArray === null) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }
    
    /**
     * Checks if a given name is a valid constant in the class.
     * @param string $name The constant name.
     * @param bool $strict Whether to perform a strict case-sensitive check.
     * @return bool
     */
    public static function isValidName($name, $strict = false)
    {
        $constants = self::getConstants();
    
        if ($strict) {
            return array_key_exists($name, $constants);
        }
    
        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }
    
    /**
     * Checks if a given value is a valid constant value in the class.
     * @param mixed $value The constant value.
     * @param bool $strict Whether to perform a strict type check.
     * @return bool
     */
    public static function isValidValue($value, $strict = true)
    {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }
    
}
