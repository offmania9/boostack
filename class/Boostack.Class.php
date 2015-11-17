<?php

/**
 * Boostack: Boostack.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
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
    public function getLabel($key)
    {
        $k = explode(".", $key);
        $lenght = count($k);
        if (is_array(($this->labels)))
            if ($lenght == 1) {
                if (isset($this->labels[$k[0]]))
                    return $this->labels[$k[0]];
            } else {
                if ($lenght == 2) {
                    if (isset($this->labels[$k[0]][$k[1]]))
                        return $this->labels[$k[0]][$k[1]];
                }
            }
    }

    /*
     *
     */
    public function registerScriptFile($fileName)
    {
        $minified = $this->developmentMode ? "" : ".min";
        $fileName = str_replace(".js", $minified . ".js", $fileName);
        ?><script type="text/javascript"
	src="<?=$this->url.self::jsUrl.$fileName;?>"></script><?
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
        ?><script type="text/javascript" src="<?=$fileURL?>"></script><?
    }

    /*
     *
     */
    public function registerCssFile($fileName)
    {
        $minified = $this->developmentMode ? "" : ".min";
        $fileName = str_replace(".css", $minified . ".css", $fileName);
        ?><link href="<?=$this->url.self::cssUrl.$fileName;?>"
	rel="stylesheet" type="text/css"><?
    }

    /*
     *
     */
    public function registerAbsoluteCssFile($fileURL)
    {
        ?><link href="<?=$fileURL?>" rel="stylesheet" type="text/css"><?
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
        ?>
            <!DOCTYPE html>
            <html lang="<?=$this->config['html_lang']?>"
            	xmlns:og="http://opengraphprotocol.org/schema/"
            	xmlns:fb="http://www.facebook.com/2008/fbml">
            <head><?
                    $this->registerAllDefaultMetaTags($titlePrepend);
                    $this->registerAllDefaultCssFiles();
                    ?></head>
            <body>
        <?
    }

    /*
     *
     */
    public function getFriendlyUrl($virtualPath)
    {
        return $this->url . $virtualPath;
    }

    /*
     *
     */
    public function renderCloseHtmlTag()
    {
        global $db;
        echo '<script type="text/javascript"> var rootUrl = "' . $this->url . '"</script>';
        echo '<script type="text/javascript"> var developmentMode = "' . $this->developmentMode . '"</script>';
        $this->registerScriptFile("lib/jquery.js");
        $this->registerScriptFile("lib/bootstrap.js");
        $this->registerScriptFile("custom.js");
        echo "<!--[if lt IE 9]>";
        $this->registerScriptFile("lib/html5shiv.js");
        $this->registerScriptFile("lib/respond.js");
        echo "<![endif]-->";
        ?>
        <div id="fb-root"></div>
    	<div class="overlay"></div>
    	<div class="loading"></div>
        </body>
        </html>
		<?
    }

    /*
     *
     */
    public function writeLog($logMesg = "")
    {
        global $objSession;
        if ($this->config['database_on']) {
            if ($this->config['log_on']) {
                if ($this->config['session_on'])
                    Database_AccessLogger::getInstance($objSession)->Log($logMesg);
                else
                    Database_AccessLogger::getInstance()->Log($logMesg);
            }
        }
    }

    /*
     *
     */
    public function __get($property_name)
    {
        return isset($this->$property_name)?:NULL;
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
        $this->registerCssFile("lib/bootstrap.css");
        $this->registerCssFile("lib/animate.css");
        $this->registerCssFile("custom.css");
    }

    public function logout()
    {
        global $objSession;
        if ($this->config['session_on'] && isset($objSession) && $objSession->IsLoggedIn())
            $objSession->LogOut();
        if ($this->config['cookie_on']) {
            setcookie('' . $this->config['cookie_name'], false, time() - $this->config['cookie_expire']);
            setcookie('' . $this->config['cookie_name'], false, time() - $this->config['cookie_expire'], "/");
        }
    }

    /*
     *
     */
    public function registerAllDefaultMetaTags($titlePrepend = "")
    {
        ?><meta charset="utf-8">
<meta name="viewport" content="<?=$this->config['viewport']?>"><?
        if ($this->facebookMetaTag) {
            ?>
<meta property="og:title" content="<?=$this->config['og_title']?>" />
<meta property="og:type" content="<?=$this->config['og_type']?>" />
<meta property="og:url" content="<?=$this->url?>" />
<meta property="og:image"
	content="<?=$this->url.$this->config["url_logo"];?>" />
<meta property="og:description"
	content="<?=$this->config['site_description'];?>" /><?
            if ($this->config['fb_app_id'] != "") {
                ?><meta property="fb:app_id"
	content="<?=$this->config['fb_app_id']?>" /><?
            }
            ?><?

            if ($this->config['fb_app_id'] != "") {
                ?><meta property="fb:admins"
	content="<?=$this->config['fb_admins']?>" /><? }?>
        <?
        }
        ?><title><?=($titlePrepend!="")?$titlePrepend." | ":""?><?=$this->config['site_title'];?> | <?=$this->config['project_sitename']?></title>
<meta name="description"
	content="<?=$this->config['site_description']?>">
<meta name="author" content="<?=$this->config['site_author']?>">
<meta content="<?=$this->config['site_keywords'];?>" name="Keywords" />
<meta content="INDEX, FOLLOW" name="ROBOTS" />
<link rel="shortcut icon"
	href="<?=$this->url.$this->config['site_shortcuticon'];?>" />
<link rel="image_src" href="<?=$this->url.$this->config['url_logo'];?>" />
<link rel="apple-touch-icon" sizes="144x144"
	href="<?=$this->url.$this->config['appletouchicon_144'];?>">
<link rel="apple-touch-icon" sizes="114x114"
	href="<?=$this->url.$this->config['appletouchicon_114'];?>">
<link rel="apple-touch-icon" sizes="72x72"
	href="<?=$this->url.$this->config['appletouchicon_72'];?>">
<link rel="apple-touch-icon"
	href="<?=$this->url.$this->config['appletouchicon_def'];?>">
<meta name="apple-mobile-web-app-title"
	content="<?=$this->url.$this->config['sitename']?>">
<base href="<?=$this->url?>" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<?
    }
}

?>