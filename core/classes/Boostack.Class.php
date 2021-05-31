<?php
/**
 * Boostack: Boostack.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */
class Boostack
{
    // global url used all over boostack.
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $developmentMode;

    /**
     * @var
     */
    protected $config;

    /**
     * @var
     */
    protected $labels;

    /**
     *
     */
    const cssUrl = "assets/css/";

    /**
     *
     */
    const jsUrl = "assets/js/";

    /**
     *
     */
    const imgUrl = "assets/img/";

    /**
     *
     */
    const templateUrl = "template/";

    /**
     *
     */
    const mailTemplatePath = "template/mail/";

    /**
     * @var null
     */
    private static $instance = NULL;

    // #############################################################
    /**
     * Boostack constructor.
     */
    private function __construct()
    {
        if (Config::get('developmentMode')) {
            if (! ini_get('zlib.output_compression') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && ! in_array('ob_gzhandler', ob_list_handlers()))
                ob_start("ob_gzhandler");
            else
                ob_start();
        }
        $this->url = Config::get('url');
        $this->developmentMode = Config::get('developmentMode');
    }

    /**
     *
     */
    private function __clone()
    {}

    /**
     * @return Boostack|null
     */
    static function getInstance()
    {
        if (self::$instance == NULL)
            self::$instance = new Boostack();
        return self::$instance;
    }

    /**
     * @param $key
     * @return array|mixed|string
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

    /**
     * @param $fileName
     */
    public function registerScriptFile($fileName)
    {
        $minified = $this->developmentMode ? "" : ".min";
        $fileName = str_replace(".js", $minified . ".js", $fileName);
        ?><script type="text/javascript" src="<?=$this->url.self::jsUrl.$fileName;?>"></script><?php
    }

    /**
     * @param $fileName
     * @return string
     */
    public function registerTemplateFile($fileName)
    {
        return ROOTPATH.self::templateUrl.$fileName;
    }

    /**
     * @param $fileName
     * @return string
     */
    public function registerImgFile($fileName)
    {
        return $this->url.self::imgUrl.$fileName;
    }

    /**
     * @param $fileURL
     */
    public function registerAbsoluteScriptFile($fileURL)
    {
        ?><script type="text/javascript" src="<?=$fileURL?>"></script><?php
    }

    /**
     * @param $fileName
     */
    public function registerCssFile($fileName)
    {
        $minified = $this->developmentMode ? "" : ".min";
        $fileName = str_replace(".css", $minified . ".css", $fileName);
        echo '<link href="'.$this->url.self::cssUrl.$fileName.'" rel="stylesheet" type="text/css"/>';
    }

    /**
     * @param $fileURL
     */
    public function registerAbsoluteCssFile($fileURL)
    {
        ?><link href="<?=$fileURL?>" rel="stylesheet" type="text/css"><?php
    }

    /**
     * @param $fileName
     */
    public function registerCoreServerFile($fileName)
    {
        $f = dirname(__FILE__) . "/../core/" . $fileName;
        if (! file_exists($f))
            exit($f . " not found.");
        require_once ($f);
    }

    /**
     * @param string $titlePrepend
     */
    public function renderOpenHtmlHeadTags($titlePrepend = "")
    {
        echo '<!DOCTYPE html>'
            .'<html lang="'.Config::get('html_lang').'" '
            //.'xmlns:og="https://opengraphprotocol.org/schema/" xmlns:fb="https://www.facebook.com/2008/fbml"'
            .'>'
            .'<head>';
        $this->registerAllDefaultMetaTags($titlePrepend);
        $this->registerAllDefaultCssFiles();
        echo '<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700,900&display=swap" rel="stylesheet"/>';
        echo '</head><body>';
        if(Config::get('setupFolderExists'))
            require $this->registerTemplateFile("boostack/setup_exists_header.phtml");
    }



    /**
     * @param bool $noToken
     */
    public function renderCloseHtmlTag($noToken = false)
    {
        if(!$noToken && Config::get('session_on') && Config::get('csrf_on')){
            global $objSession;
            echo $objSession->CSRFRenderHiddenField();
        }
        echo '<script type="text/javascript"> var rootUrl = "' . $this->url . '";var developmentMode = "' . $this->developmentMode . '";</script>';

        echo '<script>
        var endpointGetAddresses = "' . Config::get('places_endpoint') . '/api/places/addresses";
        var endpointGetCities = "' . Config::get('places_endpoint') . '/api/places/cities";
        var endpointGetAddressDetail = "' . Config::get('places_endpoint') . '/api/places/addresses/:id";
        var endpointBasic = "'.Config::get('places_endpoint_basic').'";
        var dashboardRefreshIntervall = "'.Config::get('dashboardRefreshIntervall').'";
        </script>';


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

    /*
     * @param string $logMesg
     * @param string $level
     * @param string $type
     * @throws Exception
     
    public function writeLog($logMesg = "", $level = LogLevel::Information, $type = LogType::DB) {
        global $CURRENTUSER;
        switch ($type) {
            case Log_Type::DB:
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
    */

    /**
     * @param $mail
     * @param null $parameters
     * @return mixed|string
     * @throws Exception
     */
    public function getMailTemplate($mail, $parameters = null) {
        $file = ROOTPATH.self::mailTemplatePath.$mail;
        if(!file_exists($file)) throw new Exception("Mail templating file ($file) not found");
        $template = file_get_contents($file);
        foreach ($parameters as $template_param => $value){
            $template = str_replace("[$template_param]", $value, $template);
        }
        return $template;
    }

    /**
     * @param $property_name
     * @return null
     */
    public function __get($property_name)
    {
        return isset($this->$property_name)?$this->$property_name:NULL;
    }

    /**
     * @param $property_name
     * @param $val
     */
    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }

    /**
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

    /**
     * @param string $titlePrepend
     */
    public function registerAllDefaultMetaTags($titlePrepend = "")
    {   ?>
        <meta charset="utf-8"/><meta name="viewport" content="<?=Config::get('viewport')?>"/>
        <?php
        if (Config::get('facebookMetaTag')) {
            ?>
            <meta property="og:title" content="<?=Config::get('og_title')?>" />
            <meta property="og:type" content="<?=Config::get('og_type')?>" />
            <meta property="og:url" content="<?=Config::get('og_url')?>" />
            <meta property="og:image" content="<?=Config::get('og_image')?>" />
            <meta property="og:description" content="<?=Config::get('og_description')?>" />
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
        ?><title><?=($titlePrepend!="")?$titlePrepend." | ":""?><?=Config::get('site_title');?></title>
        <meta name="description" content="<?=Config::get('site_description')?>"/>
        <meta name="author" content="<?=Config::get('site_author')?>"/>
        <meta content="<?=Config::get('site_keywords');?>" name="Keywords" />
        <meta content="INDEX, FOLLOW" name="ROBOTS" />
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