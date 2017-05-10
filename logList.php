<?php
/**
 * Boostack: download.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Log");
// #######################
if (!(Config::get('session_on') && Auth::isLoggedIn() && Utils::hasPrivilege($CURRENTUSER, PRIVILEGE_SUPERADMIN)))
    Utils::goToUrl("home");

require_once $boostack->registerTemplateFile("boostack/header.phtml");

require_once $boostack->registerTemplateFile("boostack/content_logList.phtml");

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Log List Page");
// #######################
?>