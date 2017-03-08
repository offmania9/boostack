<?php
/**
 * Boostack: Boostack.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Boostack
{

    // global url used all over boostack.
    protected $url;

    protected $developmentMode;

    protected $config;

    protected $labels;

    const cssUrl = "assets/css/";

    const jsUrl = "assets/js/";

    const imgUrl = "assets/img/";

    const templateUrl = "template/";

    const mailTemplatePath = "template/mail/";

    private static $instance = NULL;
    // #############################################################
    private function __construct()
    {
        if (Config::get('developmentMode')) {
            if (! ini_get('zlib.output_compression') && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && ! in_array('ob_gzhandler', ob_list_handlers()))
                ob_start("ob_gzhandler");
            else
                ob_start();
        }
        $this->url = Config::get('url');
        $this->developmentMode = Config::get('developmentMode');
    }

    private function __clone()
    {}

    static function getInstance()
    {
        if (self::$instance == NULL)
            self::$instance = new Boostack();
        return self::$instance;
    }

    /*
     *
     */
    public function getLabel($key) {
        if(is_array($this->labels)) {
            $k = explode(".", $key);
            if(count($k) > 0) {
                $tempArray = $this->labels;
                foreach($k as $key) {
                    if(!empty($tempArray[$key]))
                        $tempArray = $tempArray[$key];
                    else
                        return "";
                }
                return $tempArray;
            }
        }
        return "";
    }

    /*
     *
     */
    public function registerScriptFile($fileName)
    {
        $minified = $this->developmentMode ? "" : ".min";
        $fileName = str_replace(".js", $minified . ".js", $fileName);
        ?><script type="text/javascript" src="<?=$this->url.self::jsUrl.$fileName;?>"></script><?php
    }

    /*
     *
     */
    public function registerTemplateFile($fileName)
    {
        return ROOTPATH.self::templateUrl.$fileName;
    }

    /*
     *
     */
    public function registerImgFile($fileName)
    {
    	return $this->url.self::imgUrl.$fileName;
    }

    /*
     *
     */
    public function registerAbsoluteScriptFile($fileURL)
    {
        ?><script type="text/javascript" src="<?=$fileURL?>"></script><?php
    }

    /*
     *
     */
    public function registerCssFile($fileName)
    {
        $minified = $this->developmentMode ? "" : ".min";
        $fileName = str_replace(".css", $minified . ".css", $fileName);
        echo '<link href="'.$this->url.self::cssUrl.$fileName.'" rel="stylesheet" type="text/css"/>';
    }

    /*
     *
     */
    public function registerAbsoluteCssFile($fileURL)
    {
        ?><link href="<?=$fileURL?>" rel="stylesheet" type="text/css"><?php
    }

    /*
     *
     */
    public function registerCoreServerFile($fileName)
    {
        $f = dirname(__FILE__) . "/../core/" . $fileName;
        if (! file_exists($f))
            exit($f . " not found.");
        require_once ($f);
    }

    /*
     *
     */
    public function renderOpenHtmlHeadTags($titlePrepend = "")
    {
        echo '<!DOCTYPE html>'
             .'<html lang="'.Config::get('html_lang').'"'
             .'xmlns:og="https://opengraphprotocol.org/schema/" xmlns:fb="https://www.facebook.com/2008/fbml">'
             .'<head>';
        $this->registerAllDefaultMetaTags($titlePrepend);
        $this->registerAllDefaultCssFiles();
        echo '</head><body>';
        if(Config::get('setupFolderExists'))
            require $this->registerTemplateFile("boostack/setup_exists_header.phtml");
    }

    /*
     *
     */
    public function getFriendlyUrl($virtualPath)
    {
        if(Config::get('session_on')){
            global $objSession;
            $langUrl = $objSession->SESS_LANGUAGE."/";
            if(!Config::get('show_default_language_in_URL') && $objSession->SESS_LANGUAGE == Config::get('language_default'))
                $langUrl = "";
            return $this->url . $langUrl . $virtualPath;
        }
        return $this->url . $virtualPath;
    }

    /*
     *
     */
    public function renderCloseHtmlTag($noToken = false)
    {
        if(!$noToken && Config::get('session_on') && Config::get('csrf_on')){
            global $objSession;
            echo $objSession->CSRFRenderHiddenField();
        }
        echo '<script type="text/javascript"> var rootUrl = "' . $this->url . '";var developmentMode = "' . $this->developmentMode . '";</script>';

        $defaultJsFiles = Config::get("default_js_files");
        if(!empty($defaultJsFiles)) {
            foreach ($defaultJsFiles as $jsFile) {
                $this->registerScriptFile($jsFile);
            }
        }

        echo "<!--[if lt IE 9]>";
        $defaultIeJsFiles = Config::get("default_ie_js_files");
        if(!empty($defaultIeJsFiles)) {
            foreach ($defaultIeJsFiles as $jsFile) {
                $this->registerScriptFile($jsFile);
            }
        }
        echo "<![endif]-->";
        ?>
        <div id="fb-root"></div><div class="overlay"></div><div class="loading"></div></body></html>
		<?php
    }

    public function writeLog($logMesg = "", $level = LogLevel::Information, $type = LogType::DB) {
        global $CURRENTUSER;
        switch ($type) {
            case LogType::DB:
                if (Config::get('database_on') && Config::get('log_on'))
                    Database_AccessLogger::getInstance($CURRENTUSER)->Log($logMesg, $level);
                break;
            case LogType::File:
                if (Config::get('log_on'))
                    FileLogger::getInstance()->log($logMesg, $level);
                break;
            default:
                throw new Exception("Log type not found");
        }
    }

    public function getMailTemplate($mail,$parameters = null) {
        $file = ROOTPATH.self::mailTemplatePath.$mail;
        if(!file_exists($file)) throw new Exception("Mail templating file ($file) not found");
        $template = file_get_contents($file);
        foreach ($parameters as $template_param => $value){
            $template = str_replace("[$template_param]", $value, $template);
        }
        return $template;
    }

    /*
     *
     */
    public function __get($property_name)
    {
        return isset($this->$property_name)?$this->$property_name:NULL;
    }

    /*
     *
     */
    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }

    /*
     *
     */
    public function registerAllDefaultCssFiles()
    {
        $defaultCssFiles = Config::get("default_css_files");
        if(!empty($defaultCssFiles)) {
            foreach ($defaultCssFiles as $cssFile) {
                $this->registerCssFile($cssFile);
            }
        }
    }

    /*
     *
     */
    public function registerAllDefaultMetaTags($titlePrepend = "")
{   ?>
<meta charset="utf-8"/><meta name="viewport" content="<?=Config::get('viewport')?>"/>
<?php
if ($this->facebookMetaTag) {
    ?>
    <meta property="og:title" content="<?=Config::get('og_title')?>" />
    <meta property="og:type" content="<?=Config::get('og_type')?>" />
    <meta property="og:url" content="<?=$this->url?>" />
    <meta property="og:image" content="<?=$this->url.Config::get("url_logo");?>" />
    <meta property="og:description" content="<?=Config::get('site_description');?>" />
    <?php
if (Config::get('fb_app_id') != "") {
?>
    <meta property="fb:app_id" content="<?=Config::get('fb_app_id')?>" /><?php
}
?><?php
if (Config::get('fb_app_id') != "") {
?>
    <meta property="fb:admins" content="<?=Config::get('fb_admins')?>" /><?php }?>
<?php
}
?><title><?=($titlePrepend!="")?$titlePrepend." | ":""?><?=Config::get('site_title');?> | <?=Config::get('project_sitename')?></title>
<meta name="description" content="<?=Config::get('site_description')?>"/>
<meta name="author" content="<?=Config::get('site_author')?>"/>
<meta content="<?=Config::get('site_keywords');?>" name="Keywords" />
<meta content="INDEX, FOLLOW" name="ROBOTS" />
<link rel="shortcut icon" href="<?=$this->url.Config::get('site_shortcuticon');?>" />
<link rel="image_src" href="<?=$this->url.Config::get('url_logo');?>" />
<link rel="apple-touch-icon" sizes="144x144" href="<?=$this->url.Config::get('appletouchicon_144');?>"/>
<link rel="apple-touch-icon" sizes="114x114" href="<?=$this->url.Config::get('appletouchicon_114');?>"/>
<link rel="apple-touch-icon" sizes="72x72" href="<?=$this->url.Config::get('appletouchicon_72');?>"/>
<link rel="apple-touch-icon" href="<?=$this->url.Config::get('appletouchicon_def');?>"/>
<meta name="apple-mobile-web-app-title" content="<?=$this->url.Config::get('sitename')?>"/>
<base href="<?=$this->url?>" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<?php
}
}

?>