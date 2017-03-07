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
        global $config;
        $this->config = $config;
        if ($this->config['developmentMode']) {
            if (! ini_get('zlib.output_compression') && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && ! in_array('ob_gzhandler', ob_list_handlers()))
                ob_start("ob_gzhandler");
            else
                ob_start();
        }
        $this->url = $this->config['url'];
        $this->developmentMode = $this->config['developmentMode'];
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
    public function getConfig($key)
    {
        return (isset($this->config[$key]))?$this->config[$key]:"";
    }

    public function constraitConfig($key, $value = true)
    {
        if(isset($this->config[$key]) && $this->config[$key] == $value) return true;
        throw new Exception_Misconfiguration("You must enable ".$key."config");
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
             .'<html lang="'.$this->config['html_lang'].'"'
             .'xmlns:og="https://opengraphprotocol.org/schema/" xmlns:fb="https://www.facebook.com/2008/fbml">'
             .'<head>';
        $this->registerAllDefaultMetaTags($titlePrepend);
        $this->registerAllDefaultCssFiles();
        echo '</head><body>';
        if(isset($this->config['setupFolderExists']) && $this->config['setupFolderExists'])
            require $this->registerTemplateFile("boostack/setup_exists_header.phtml");
    }

    /*
     *
     */
    public function getFriendlyUrl($virtualPath)
    {
        if($this->getConfig('session_on')){
            global $objSession;
            $langUrl = $objSession->SESS_LANGUAGE."/";
            if(!$this->config['show_default_language_in_URL'] && $objSession->SESS_LANGUAGE == $this->config['language_default'])
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
        if(!$noToken && $this->getConfig('session_on') && $this->getConfig('csrf_on')){
            global $objSession;
            echo $objSession->CSRFRenderHiddenField();
        }
        echo '<script type="text/javascript"> var rootUrl = "' . $this->url . '";var developmentMode = "' . $this->developmentMode . '";</script>';

        $defaultJsFiles = $this->getConfig("default_js_files");
        if(!empty($defaultJsFiles)) {
            foreach ($defaultJsFiles as $jsFile) {
                $this->registerScriptFile($jsFile);
            }
        }

        echo "<!--[if lt IE 9]>";
        $defaultIeJsFiles = $this->getConfig("default_ie_js_files");
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
                if ($this->config['database_on'] && $this->config['log_on'])
                    Database_AccessLogger::getInstance($CURRENTUSER)->Log($logMesg, $level);
                break;
            case LogType::File:
                if ($this->config['log_on'])
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
        $defaultCssFiles = $this->getConfig("default_css_files");
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
<meta charset="utf-8"/><meta name="viewport" content="<?=$this->config['viewport']?>"/>
<?php
if ($this->facebookMetaTag) {
    ?>
    <meta property="og:title" content="<?=$this->config['og_title']?>" />
    <meta property="og:type" content="<?=$this->config['og_type']?>" />
    <meta property="og:url" content="<?=$this->url?>" />
    <meta property="og:image" content="<?=$this->url.$this->config["url_logo"];?>" />
    <meta property="og:description" content="<?=$this->config['site_description'];?>" />
    <?php
if ($this->config['fb_app_id'] != "") {
?>
    <meta property="fb:app_id" content="<?=$this->config['fb_app_id']?>" /><?php
}
?><?php
if ($this->config['fb_app_id'] != "") {
?>
    <meta property="fb:admins" content="<?=$this->config['fb_admins']?>" /><?php }?>
<?php
}
?><title><?=($titlePrepend!="")?$titlePrepend." | ":""?><?=$this->config['site_title'];?> | <?=$this->config['project_sitename']?></title>
<meta name="description" content="<?=$this->config['site_description']?>"/>
<meta name="author" content="<?=$this->config['site_author']?>"/>
<meta content="<?=$this->config['site_keywords'];?>" name="Keywords" />
<meta content="INDEX, FOLLOW" name="ROBOTS" />
<link rel="shortcut icon" href="<?=$this->url.$this->config['site_shortcuticon'];?>" />
<link rel="image_src" href="<?=$this->url.$this->config['url_logo'];?>" />
<link rel="apple-touch-icon" sizes="144x144" href="<?=$this->url.$this->config['appletouchicon_144'];?>"/>
<link rel="apple-touch-icon" sizes="114x114" href="<?=$this->url.$this->config['appletouchicon_114'];?>"/>
<link rel="apple-touch-icon" sizes="72x72" href="<?=$this->url.$this->config['appletouchicon_72'];?>"/>
<link rel="apple-touch-icon" href="<?=$this->url.$this->config['appletouchicon_def'];?>"/>
<meta name="apple-mobile-web-app-title" content="<?=$this->url.$this->config['sitename']?>"/>
<base href="<?=$this->url?>" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<?php
}
}

?>