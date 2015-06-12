<?
/**
 * Boostack: error.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

########################
require_once("core/environment_init.php");
$boostack->renderOpenHtmlHeadTags();
########################
/*if($boostack->session_on && $objSession->IsLoggedIn()){
    $user = new User($objSession->GetUserID());
}*/

require("template/boostack/header.phtml");?>

<!-- Example: Inline HTML code -->
    <section class="disclaimer sectionTitle">
        <div class="line">
            <div class="container">
                <div class="row">
                    <h1><?=$boostack->getLabel("error.attention")?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="download centerContent">
        <div class="container">
            <div class="row description">
                <h2><?=$boostack->getLabel("error.urlnotfound")?></h2>
                <p><a href="<?=$boostack->url?>"><?=$boostack->url?></a></p>
            </div>
        </div>
    </section>
<?
require("template/boostack/footer.phtml");

########################
$boostack->renderCloseHtmlTag();
########################
?>