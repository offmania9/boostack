<?php

/**
 * Boostack: Config.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5
 */

class Config
{

    /**
     * Holds the configuration settings.
     *
     * @var mixed|null
     */
    private static $configs = NULL;

    /**
     * Initializes the configuration settings.
     */
    public static function init()
    {
        global $config;
        self::$configs = $config;
    }

    /**
     * Retrieves the value of a configuration attribute.
     *
     * @param string $configKey The key of the configuration attribute.
     * @return mixed The value of the configuration attribute.
     * @throws Exception_Misconfiguration If the configuration attribute is not found.
     */
    public static function get($configKey)
    {
        if (isset(self::$configs[$configKey]))
            return self::$configs[$configKey];
        throw new Exception_Misconfiguration("Configuration attribute '" . $configKey . "' not found'");
    }

    /**
     * Checks if a configuration attribute meets a specified constraint.
     *
     * @param string $configKey The key of the configuration attribute.
     * @param bool $configvalue The value the configuration attribute should have.
     * @return bool True if the constraint is met, false otherwise.
     * @throws Exception_Misconfiguration If the configuration attribute is not found or does not meet the constraint.
     */
    public static function constraint($configKey, $configvalue = true)
    {
        if (isset(self::$configs[$configKey]) && self::$configs[$configKey] == $configvalue) return true;
        throw new Exception_Misconfiguration("You must enable '" . $configKey . "' configuration attribute in config/env.php file");
    }
}
