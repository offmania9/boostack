<?php

/**
 * Boostack: Language.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5
 */

class Language
{

    private static $translatedLabels = null;
    /**
     * Initialize the language settings.
     */
    public static function init()
    {
        // Find the language based on configuration
        $language = self::findLanguage();

        // Get translated labels for the language
        $translatedLabels = Language::getLabelsFromLanguage($language);

        // Set session language if session is enabled
        if (Config::get('session_on')) {
            Language::setSessionLanguage($language);
        }

        // Set translated labels
        self::$translatedLabels = $translatedLabels;
    }

    /**
     * Get the label for the given key.
     *
     * @param string $key The key for the label.
     * @return string The translated label.
     */
    public static function getLabel($key)
    {
        if (is_array(self::$translatedLabels)) {
            $keys = explode(".", $key);
            $tempArray = self::$translatedLabels;
            foreach ($keys as $k) {
                if (!empty($tempArray[$k])) {
                    $tempArray = $tempArray[$k];
                } else {
                    return "";
                }
            }
            return $tempArray;
        }
        return "";
    }

    /**
     * Find the language based on configuration and request.
     *
     * @return string The language found.
     */
    private static function findLanguage()
    {
        $defaultLanguage = Config::get("language_default");
        $language = null;

        // Check if the default language should be forced
        if (Config::get("language_force_default")) {
            $language = $defaultLanguage;
        } elseif (!empty(Request::hasQueryParam("lang"))) {
            $language = Request::getQueryParam("lang");
        }

        if (in_array($language, Config::get("enabled_languages"))) {
            return $language;
        }

        return $defaultLanguage;
    }

    /**
     * Set the session language.
     *
     * @param string $lang The language to set in session.
     * @throws Exception_Misconfiguration If session or database is not enabled.
     */
    private static function setSessionLanguage($lang)
    {
        Config::constraint("session_on");
        Config::constraint("database_on");
        Session::set("SESS_LANGUAGE", $lang);
    }

    /**
     * Get translated labels from language file.
     *
     * @param string $lang The language for which to get labels.
     * @return array The translated labels.
     * @throws Exception If language file not found.
     */
    private static function getLabelsFromLanguage($lang)
    {
        $filePath = ROOTPATH . Config::get("language_path") . $lang . Config::get("language_file_extension");
        if (!is_file($filePath)) {
            throw new Exception("Language file " . $filePath . " not found");
        }
        $jsonFileContent = file_get_contents($filePath);
        $decodedFileContent = json_decode($jsonFileContent, true);
        return $decodedFileContent;
    }
}
