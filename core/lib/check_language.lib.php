<?
/**
 * Boostack: check_language.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
$l;
$language;
if (! isset($_GET['lang'])) { // if isn't set by user from url
    if ($config['session_on'] && $objSession->SESS_LANGUAGE !== "") { // if is set in the user session
        if (is_file(ROOTPATH."core/lang/" . $objSession->SESS_LANGUAGE . ".inc.php")) { // if the translation file exists
            include (ROOTPATH."core/lang/" . $objSession->SESS_LANGUAGE . ".inc.php");
            $l = $objSession->SESS_LANGUAGE;
        } else { // default lang
            include (ROOTPATH."core/lang/" . $config["language_default"] . ".inc.php");
            $l = $config["language_default"];
        }
    } else { // if isn't set in the user session, fetch it from browser
        $language = explode(',', sanitizeInput($_SERVER['HTTP_ACCEPT_LANGUAGE']));
        $language = strtolower(substr(chop($language[0]), 0, 2));
        if (is_file(ROOTPATH."core/lang/" . $language . ".inc.php")) { // if the translation file exists
            include (ROOTPATH."core/lang/" . $language . ".inc.php");
            $l = $language;
        } else {
            include (ROOTPATH."core/lang/" . $config["language_default"] . ".inc.php");
            $l = $config["language_default"];
        }
    }
} else { // if is set by user from url
    $language = sanitizeInput($_GET['lang']);
    if (is_file(ROOTPATH."core/lang/" . $language . ".inc.php")) { // if the translation file exists
        include (ROOTPATH."core/lang/" . $language . ".inc.php");
        $l = $language;
    } else { // default lang
        include (ROOTPATH."core/lang/" . $config["language_default"] . ".inc.php");
        $l = $config["language_default"];
    }
}
$boostack->labels = $boostack_labels_strings;

if ($config['session_on'])
    $objSession->SESS_LANGUAGE = $l;
unset($l, $language);
?>