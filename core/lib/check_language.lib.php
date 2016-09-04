<?
/**
 * Boostack: check_language.lib.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */
$l;
$language;
$defaultLanguage = $boostack->getConfig("language_default");

// FORCE DEFAULT LANGUAGE
if($boostack->getConfig("language_force_default") == TRUE) {
    if (is_file(ROOTPATH."core/lang/" . $defaultLanguage . ".inc.php")) {
        include(ROOTPATH . "core/lang/" . $defaultLanguage . ".inc.php");
        $l = $defaultLanguage;
    } else {
        throw new Exception_LanguageNotFound("Language file ".ROOTPATH."core/lang/" . $defaultLanguage . ".inc.php"." not found");
    }
}
// SESSION / BROWSER LANGUAGE
else if (! isset($_GET['lang'])) { // if isn't set by user from url
    if ($boostack->getConfig("session_on") && $objSession->SESS_LANGUAGE !== "") { // if is set in the user session
        if (is_file(ROOTPATH."core/lang/" . $objSession->SESS_LANGUAGE . ".inc.php")) { // if the translation file exists
            include (ROOTPATH."core/lang/" . $objSession->SESS_LANGUAGE . ".inc.php");
            $l = $objSession->SESS_LANGUAGE;
        } else { // default lang
            include (ROOTPATH."core/lang/" . $defaultLanguage . ".inc.php");
            $l = $defaultLanguage;
        }
    } else { // if isn't set in the user session, fetch it from browser
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $language = explode(',', Utils::sanitizeInput($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            $language = strtolower(substr(chop($language[0]), 0, 2));
            if (is_file(ROOTPATH."core/lang/" . $language . ".inc.php")) { // if the translation file exists
                include (ROOTPATH."core/lang/" . $language . ".inc.php");
                $l = $language;
            } else {
                include (ROOTPATH."core/lang/" . $defaultLanguage . ".inc.php");
                $l = $defaultLanguage;
            }
        } else {
            include (ROOTPATH."core/lang/" . $defaultLanguage . ".inc.php");
            $l = $defaultLanguage;
        }
    }
}
// URL LANGUAGE
else { // if is set by user from url
    $language = Utils::sanitizeInput($_GET['lang']);
    if (is_file(ROOTPATH."core/lang/" . $language . ".inc.php")) { // if the translation file exists
        include (ROOTPATH."core/lang/" . $language . ".inc.php");
        $l = $language;
    } else { // default lang
        include (ROOTPATH."core/lang/" . $defaultLanguage . ".inc.php");
        $l = $defaultLanguage;
    }
}
$boostack->labels = $boostack_labels_strings;

if ($boostack->getConfig("session_on") && !$boostack->getConfig("language_force_default"))
    $objSession->SESS_LANGUAGE = $l;
unset($l, $language);
?>