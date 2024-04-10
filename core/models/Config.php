<?php

namespace Core\Models;

use Core\Exception\Exception_Misconfiguration;

/**
 * Boostack: Config.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
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
     * Prevents direct instantiation of Config.
     */
    private function __construct()
    {
    }

    /**
     * Initializes the configuration settings.
     */
    public static function init()
    {
        $envRelativePath = __DIR__ . "/../../config/env/env.php";
        $envPath = realpath($envRelativePath);
        if ($envPath && is_file($envPath)) {
            require_once $envPath;
        } else {
            if (is_dir(__DIR__ . "setup")) {
                header("Location: setup");
            } else {
                echo "Rename 'config/env/sample.env.php' into 'env.php'";
            }
            exit();
        }
        require_once(__DIR__ . "/../../config/env/global.env.php");
        //global $config;
        self::$configs = $config;
        return self::$configs;
    }

    /**
     * Retrieves the value of a configuration attribute.
     *
     * @param string $configKey The key of the configuration attribute.
     * @return mixed The value of the configuration attribute.
     * @throws \Exception_Misconfiguration If the configuration attribute is not found.
     */
    public static function get($configKey)
    {
        if (self::$configs === null) {
            self::$configs = self::init();
        }

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
     * @throws \Exception_Misconfiguration If the configuration attribute is not found or does not meet the constraint.
     */
    public static function constraint($configKey, $configvalue = true)
    {
        if (isset(self::$configs[$configKey]) && self::$configs[$configKey] == $configvalue) return true;
        throw new Exception_Misconfiguration("You must enable '" . $configKey . "' configuration attribute in config/env.php file");
    }

    /**
     * Set the value of a configuration attribute.
     *
     * @param string $configKey The key of the configuration attribute.
     * @param string $configValue The value of the configuration attribute.
     * @throws \Exception_Misconfiguration If the configuration attribute is not found.
     */
    public static function set($configKey, $configValue)
    {
        self::$configs[$configKey] = $configValue;
    }
}
