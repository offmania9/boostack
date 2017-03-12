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

    /**
     *
     */
    const LANGUAGE_FILES_PATH = "lang/";
    /**
     *
     */
    const LANGUAGE_FILES_EXTENSION = ".inc.json";

    /**
     * @return array|null|string
     */
    public static function getLanguage() {
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

    /**
     * @param $lang
     */
    public static function setSessionLanguage($lang) {
        global $objSession;
        Config::constraint("session_on");
        Config::constraint("database_on");
        $objSession->SESS_LANGUAGE = $lang;
    }

    /**
     * @param $lang
     * @return string
     * @throws Exception
     */
    public static function findLanguageFile($lang) {
        $filePath = ROOTPATH.self::LANGUAGE_FILES_PATH.$lang.self::LANGUAGE_FILES_EXTENSION;
        if(is_file($filePath)) {
            return $filePath;
        }
        throw new Exception("Language file ".$filePath." not found");
    }

    /**
     * @param $file
     * @return mixed
     */
    public static function readAndDecodeLanguageFile($file) {
        $jsonFileContent = file_get_contents($file);
        $decodedFileContent = json_decode($jsonFileContent, true);
        return $decodedFileContent;
    }
}