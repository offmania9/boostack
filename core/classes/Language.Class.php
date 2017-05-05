<?php
/**
 * Boostack: Language.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Language {

    const LANGUAGE_FILES_PATH = "lang/";

    const LANGUAGE_FILES_EXTENSION = ".inc.json";

    private static $translatedLabels = null;

    public static function init() {
        $language = self::findLanguage();
        $translatedLabels = Language::getLabelsFromLanguage($language);
        if(Config::get('session_on')) Language::setSessionLanguage($language);
        self::$translatedLabels = $translatedLabels;
    }

    public static function getLabel($key) {
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

    private static function findLanguage() {
        global $objSession;
        $defaultLanguage = Config::get("language_default");
        $language = null;

        if(Config::get("language_force_default") == TRUE) {
            $language = $defaultLanguage;
        }
        else if(!empty($_GET['lang'])) {
            $language = Utils::sanitizeInput($_GET['lang']);
        }
//        else {
//            if (Config::get("session_on") && $objSession->SESS_LANGUAGE !== "") { // if is set in the user session
//                $language = $objSession->SESS_LANGUAGE;
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

    private static function setSessionLanguage($lang) {
        global $objSession;
        Config::constraint("session_on");
        Config::constraint("database_on");
        $objSession->SESS_LANGUAGE = $lang;
    }

    private static function getLabelsFromLanguage($lang) {
        $filePath = ROOTPATH.self::LANGUAGE_FILES_PATH.$lang.self::LANGUAGE_FILES_EXTENSION;
        if(!is_file($filePath)) throw new Exception("Language file ".$filePath." not found");
        $jsonFileContent = file_get_contents($filePath);
        $decodedFileContent = json_decode($jsonFileContent, true);
        return $decodedFileContent;
    }
}