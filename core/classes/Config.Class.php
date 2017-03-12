<?php

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
    public static function initConfig()
    {
        global $config;
        self::$configs = $config;
    }

    /**
     * @param $configKey
     * @return string
     */
    public static function get($configKey)
    {
        return isset(self::$configs[$configKey]) ? self::$configs[$configKey] : "";
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
            throw new Exception_Misconfiguration("You must enable '".$configKey."' configuration attribute");
    }

}