<?php
/**
 * Boostack: Config.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */

/**
 * Class Config
 */
class Config {

    /**
     * @var null
     */
    private static $configs = NULL;

    /**
     *
     */
    public static function init()
    {
        global $config;
        self::$configs = $config;
    }

    /**
     * @param $configKey
     * @return mixed
     * @throws Exception_Misconfiguration
     */
    public static function get($configKey)
    {
        if(isset(self::$configs[$configKey])) 
            return self::$configs[$configKey];
        throw new Exception_Misconfiguration("Configuration attribute '".$configKey."' not found'");
    }

    /**
     * @param $configKey
     * @param bool $configvalue
     * @return bool
     * @throws Exception_Misconfiguration
     */
    public static function constraint($configKey, $configvalue = true)
    {
        if(isset(self::$configs[$configKey]) && self::$configs[$configKey] == $configvalue) return true;
        throw new Exception_Misconfiguration("You must enable '".$configKey."' configuration attribute in config/env.php file");
    }

}