<?php
/**
 * Boostack: index.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.3
 */

// #######################
require_once "core/environment_init.php";
$boostack->renderOpenHtmlHeadTags("Home");
// #######################

require_once $boostack->registerTemplateFile("boostack/header.phtml");

require_once $boostack->registerTemplateFile("boostack/content_index_out.phtml");

require_once $boostack->registerTemplateFile("boostack/footer.phtml");


// #######################
$boostack->renderCloseHtmlTag();
$boostack->writeLog("Homepage Page", LogLevel::Information);
// #######################
?>