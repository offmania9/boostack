<?
/**
 * Boostack: index.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

########################
require_once("core/environment_init.php");
$boostack->renderOpenHtmlHeadTags("Home");
########################
/*if($boostack->session_on && $objSession->IsLoggedIn()){
    $user = new User($objSession->GetUserID());
}*/

    require("template/boostack/header.phtml");

    require("template/boostack/content_index_out.phtml");

    require("template/boostack/footer.phtml");

########################
$boostack->renderCloseHtmlTag();
########################
?>