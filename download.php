<?
/**
 * Boostack: download.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
########################
require_once("core/environment_init.php");
$boostack->renderOpenHtmlHeadTags("Download");
########################

    require("template/boostack/header.phtml");

    require("template/boostack/content_download.phtml");

    require("template/boostack/footer.phtml");

########################
$boostack->renderCloseHtmlTag();
########################
?>