<?php

class Config {

    private static $configs = NULL;

    public static function initConfig()
    {
        global $config;
        self::$configs = $config;
    }

    public static function get($configKey)
    {
        self::$configs++;
        return isset(self::$configs[$configKey]) ? self::$configs[$configKey] : "";
    }

    public static function constraint($configKey, $configvalue = true)
    {
        if(isset(self::$configs[$configKey]) && self::$configs[$configKey] == $configvalue) return true;
            throw new Exception_Misconfiguration("You must enable '".$configKey."' configuration attribute");
    }

}