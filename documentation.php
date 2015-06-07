<?
########################
require_once("core/environment_init.php");
$boostack->renderOpenHtmlHeadTags();
########################
/*if($boostack->session_on && $objSession->IsLoggedIn()){
    $user = new User($objSession->GetUserID());
}*/

require("template/boostack/header.phtml");

require("template/boostack/content_documentation.phtml");

require("template/boostack/footer.phtml");

########################
$boostack->renderCloseHtmlTag();
########################
?>