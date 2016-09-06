<?php
/**
 * Boostack: download.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */
// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Download");
// #######################

require_once $boostack->registerTemplateFile("boostack/header.phtml");

require_once $boostack->registerTemplateFile("boostack/content_download.phtml");

require_once $boostack->registerTemplateFile("boostack/footer.phtml");

// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Download Page");
// #######################
?>