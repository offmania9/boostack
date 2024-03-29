<?php
/**
 * Boostack: Language.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */

class Language
{

    private static $translatedLabels = null;

    public static function init()
    {
        $language = self::findLanguage();
        $translatedLabels = Language::getLabelsFromLanguage($language);
        if(Config::get('session_on')) Language::setSessionLanguage($language);
        self::$translatedLabels = $translatedLabels;
    }

    public static function getLabel($key)
    {
        if(is_array(self::$translatedLabels)) {
            $k = explode(".", $key);
            if(count($k) > 0) {
                $tempArray = self::$translatedLabels;
                foreach($k as $key) {
                    if(!empty($tempArray[$key]))
                        $tempArray = $tempArray[$key];
                    else
                        return "";
                }
                return $tempArray;
            }
        }
        return "";
    }

    private static function findLanguage()
    {
        $defaultLanguage = Config::get("language_default");
        $language = null;

        if(Config::get("language_force_default") == TRUE) {
            $language = $defaultLanguage;
        }
        else if(!empty(Request::hasQueryParam("lang"))) {
            $language = Request::getQueryParam("lang");
        }
//        else {
//            if (Config::get("session_on") && Session::get("SESS_LANGUAGE") !== "") { // if is set in the user session
//                $language = Session::get("SESS_LANGUAGE");
//            } else { // if isn't set in the user session, fetch it from browser
//                if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
//                    $language = explode(',', Utils::sanitizeInput($_SERVER['HTTP_ACCEPT_LANGUAGE']));
//                    $language = strtolower(substr(chop($language[0]), 0, 2));
//                }
//            }
//        }
        if(in_array($language,Config::get("enabled_languages"))) return $language;
        return $defaultLanguage;
    }

    private static function setSessionLanguage($lang)
    {
        Config::constraint("session_on");
        Config::constraint("database_on");
        Session::set("SESS_LANGUAGE",$lang);
    }

    private static function getLabelsFromLanguage($lang)
    {
        $filePath = ROOTPATH.Config::get("language_path").$lang.Config::get("language_file_extension");
        if(!is_file($filePath)) throw new Exception("Language file ".$filePath." not found");
        $jsonFileContent = file_get_contents($filePath);
        $decodedFileContent = json_decode($jsonFileContent, true);
        return $decodedFileContent;
    }
}