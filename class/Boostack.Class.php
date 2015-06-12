<?php
/**
 * Boostack: Boostack.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
class Boostack{
    protected $developmentMode = false;
    protected $publicUrl = "http://boostack.com/";
    protected $privateUrl = "http://localhost/boostack/";
    protected $url;
    protected $url_logo = "img/boostack_logo_x210.png";
    protected $sitename = "Boostack.com";
    protected $project_name = "Boostack";
    protected $project_sitename = "Boostack.com";
    protected $mail_admin = "info@boostack.com";
    protected $mail_noreply = "no-reply@boostack.com";
    protected $html_lang = "en";
    protected $project_mission = "Boostack.com - Improve your development and build a modern website in minutes";
    protected $facebookMetaTag = true;
    protected $og_type = "product";
    protected $og_title = "Boostack.com - Improve your development and build a modern website in minutes";
    protected $fb_app_id = "";
    protected $fb_app_secret = "";
    protected $fb_admins = "";
    protected $twitter = "@getBoostack";
    protected $gplus = "https://plus.google.com/+BoostackFramework/";

    protected $database_on = true;
    protected $session_on = false;  #true need database_on=true
    protected $checkcookie=false;  #true need database_on=true AND session_on=true
    protected $cookieexpire= 3600; //1 hour
    protected $cookiename= ""; //md5 key
    protected $checklanguage = true;
    protected $defaultlanguage = "en"; #must exists file: lang/[$defaultlanguage].inc.php   es:lang/en.inc.php
    protected $checkMobile = false;
    protected $log_on = true; #true need database_on=true

    protected $viewport = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0";
    protected $site_title = "Boostack - a full stack web layer for PHP";
    protected $site_keywords = "boostack, php, framework, website, productive, simplicity, seo, secure, mysql, open-source";//comma separated
    protected $site_description = "Improve your development and build a modern website in minutes";
    protected $site_author = "stefano spagnolo";
    protected $site_shortcuticon = "img/favicon.ico";
    protected $appletouchicon_144 = "img/apple-touch-icon-144-precomposed.png";
    protected $appletouchicon_114 = "img/apple-touch-icon-114-precomposed.png";
    protected $appletouchicon_72  = "img/apple-touch-icon-72-precomposed.png";
    protected $appletouchicon_def = "img/apple-touch-icon-57-precomposed.png";

    protected $labels;

    public function __construct($developmentMode = false){
        $this->cookieexpire = 60*60*24*59;
        if($developmentMode){
            $this->url = $this->privateUrl;
            if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && !in_array('ob_gzhandler', ob_list_handlers())) ob_start("ob_gzhandler"); else ob_start();
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        else
            $this->url = $this->publicUrl;

        $this->developmentMode = $developmentMode;
    }

    public function getLabel($key){
        $k = explode(".",$key);
        $lenght = count($k);
        if(is_array(($this->labels)))
            if($lenght == 1) {
                if (isset($this->labels[$k[0]]))
                    return $this->labels[$k[0]];
            }
            else{
                if($lenght == 2) {
                    if (isset($this->labels[$k[0]][$k[1]]))
                        return $this->labels[$k[0]][$k[1]];
                }
            }
    }

    public function registerScriptFile($fileName) {
        ?>
        <script type="text/javascript" src="<?=$this->url?>js/<?=$fileName?>"></script>
    <?
    }

    public function registerCssFile($fileName) {
        ?>
        <link href="<?=$this->url?>css/<?=$fileName?>" rel="stylesheet">
    <?
    }
    public function registerCoreServerFile($fileName) {
        #require_once(dirname(__FILE__)."/../core/".$fileName);
        $f = dirname(__FILE__)."/../core/".$fileName;
        if (!file_exists($f))
            exit($f." not found.");
        require_once($f);

    }

    public function renderOpenHtmlHeadTags($titlePrepend="") {
        ?>
        <!DOCTYPE html><html lang="<?=$this->html_lang?>" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"><head>
            <?
            $this->registerAllDefaultMetaTags($titlePrepend);
            $this->registerAllDefaultCssFiles();
            ?>
        </head>
        <body>
    <?
    }

    public function getFriendlyUrl($virtualPath){
        {       return $this->url.$virtualPath;
        }   }


    public function renderCloseHtmlTag($logMesg="") {
        global $db;
        $this->registerScriptFile("jquery.min.js");
        $this->registerScriptFile("bootstrap.min.js");
        $this->registerScriptFile("custom.js");
        echo "<!--[if lt IE 9]>";
        $this->registerScriptFile("html5shiv.js");
        $this->registerScriptFile("respond.min.js");
        echo "<![endif]-->";
        ?>
        <div id="fb-root"></div><div class="overlay"></div><div class="loading"></div>
        </body></html>
        <?
        $this->writeLog($logMesg);
        $db->Close();
    }

    public function writeLog($logMesg=""){
        global $db,$objSession;
        if($this->database_on){
            if($this->log_on){
                if($this->session_on)
                    DatabaseAccessLogger::getInstance($db,$objSession)->Log($logMesg);
                else
                    DatabaseAccessLogger::getInstance($db)->Log($logMesg);
            }
        }
    }

    public function __get($property_name) {
        if(isset($this->$property_name)) {
            return($this->$property_name);
        } else {
            return(NULL);
        }
    }

    public function __set($property_name, $val) {
        $this->$property_name = $val;
    }

    public function registerAllDefaultCssFiles(){
        $minified = $this->developmentMode ? "":".min";
        $this->registerCssFile("bootstrap".$minified.".css");
        $this->registerCssFile("animate".$minified.".css");
        $this->registerCssFile("custom".$minified.".css");
        $this->registerCssFile("custom_in".$minified.".css");
    }

    public function registerAllDefaultMetaTags($titlePrepend=""){?>
        <meta charset="utf-8">
        <meta name="viewport" content="<?=$this->viewport?>">
        <? if($this->facebookMetaTag){?>
            <meta property="og:title" content="<?=$this->og_title?>" />
            <meta property="og:type" content="<?=$this->og_type?>" />
            <meta property="og:url" content="<?=$this->url?>"/>
            <meta property="og:image" content="<?=$this->url_logo;?>"/>
            <meta property="og:description" content="<?=$this->site_description;?>" />
            <? if($this->fb_app_id!=""){?><meta property="fb:app_id" content="<?=$this->fb_app_id?>" /><? }?>
            <? if($this->fb_app_id!=""){?><meta property="fb:admins" content="<?=$this->fb_admins?>" /><? }?>
        <? }?>
        <title><?=($titlePrepend!="")?$titlePrepend." | ":""?><?=$this->site_title;?> | <?=$this->project_sitename?></title>
        <meta name="description" content="<?=$this->site_description?>"><meta name="author" content="<?=$this->site_author?>"><meta content="<?=$this->site_keywords;?>" name="Keywords" /><meta content="INDEX, FOLLOW" name="ROBOTS" />
        <link rel="shortcut icon" href="<?=$this->site_shortcuticon;?>" /><link rel="image_src" href="<?=$this->url_logo;?>" /><link rel="apple-touch-icon" sizes="144x144" href="<?=$this->appletouchicon_144;?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?=$this->appletouchicon_114;?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?=$this->appletouchicon_72;?>">
        <link rel="apple-touch-icon" href="<?=$this->appletouchicon_def;?>">
        <meta name="apple-mobile-web-app-title" content="<?=$this->sitename?>">
        <base href="<?=$this->url?>" /><meta name="apple-mobile-web-app-capable" content="yes" />
    <?
    }
}
### DATABASE SCHEMA

/*
 *
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*
-- Database: `boostack`
-- --------------------------------------------------------
-- Table `boostack_http_session`

CREATE TABLE `boostack_http_session` (
`id` int(11) NOT NULL auto_increment,
  `ascii_session_id` varchar(32) NOT NULL,
  `logged_in` varchar(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_impression` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `user_agent` varchar(256) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Data dump for `boostack_http_session`
                 -- --------------------------------------------------------
-- Table `boostack_log`

CREATE TABLE `boostack_log` (
`id` int(11) NOT NULL auto_increment,
  `datetime` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `useragent` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `query` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Data dump for `boostack_log`
                 -- --------------------------------------------------------
-- Table `boostack_session_variable`

CREATE TABLE `boostack_session_variable` (
`id` int(11) NOT NULL auto_increment,
  `session_id` int(11) NOT NULL,
  `variable_name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `variable_value` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- Data dump for `boostack_session_variable`
                 -- --------------------------------------------------------
-- Table `boostack_user`

CREATE TABLE `boostack_user` (
`id` int(11) NOT NULL auto_increment,
  `active` varchar(1) NOT NULL,
  `privilege` int(11) NOT NULL,
  `username` text,
  `pwd` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pic_square` varchar(255) NOT NULL,
  `last_access` int(11) NOT NULL default '0',
  `session_cookie` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- Data dump for `boostack_user`  username:boostack  password: boostackAdm1n
INSERT INTO `boostack_user` VALUES(0, '0', 3, 'boostack', 'fbd5ee51bd4f9f23201396c9d9d58117d20fdb82c63f9ca8574b67461a1110ad03e3a0a1d9e000371ceb9211fb5676e1688ea060c47f31573465615e73039ab2', '@', '', 522720000, '');
-- --------------------------------------------------------
-- Table `boostack_user_info`
CREATE TABLE `boostack_user_info` (
    `id` int(11) NOT NULL,
  `first_name` varchar(70) NOT NULL,
  `last_name` varchar(70) default NULL,
  `name` varchar(255) default NULL,
  `locale` varchar(255) default NULL,
  `city` varchar(200) default NULL,
  `state` varchar(100) default NULL,
  `country` varchar(100) default NULL,
  `zip` varchar(10) default NULL,
  `about_me` text,
  `tel` varchar(20) default NULL,
  `cell` varchar(20) default NULL,
  `profession` varchar(25) default NULL,
  `birthday` varchar(30) default NULL,
  `movies` varchar(300) default NULL,
  `music` varchar(300) default NULL,
  `political` varchar(300) default NULL,
  `interests` varchar(300) default NULL,
  `tv` varchar(300) default NULL,
  `religion` varchar(300) default NULL,
  `pic_big` varchar(255) default NULL,
  `sex` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data dump for `boostack_user_info`
                 -- --------------------------------------------------------
-- Table `boostack_user_registration`

CREATE TABLE `boostack_user_registration` (
    `id` int(11) NOT NULL,
  `activation_date` int(11) NOT NULL default '0',
  `access_code` varchar(10) default NULL,
  `ip` varchar(16) NOT NULL,
  `join_date` int(11) NOT NULL,
  `join_idconfirm` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data dump for `boostack_user_registration`
                 -- --------------------------------------------------------
-- Table `boostack_user_social`

CREATE TABLE `boostack_user_social` (
    `id` int(11) NOT NULL,
  `type` varchar(2) NOT NULL,
  `uid` varchar(90) NOT NULL,
  `uid_token` varchar(90) NOT NULL,
  `uid_token_secret` varchar(90) NOT NULL,
  `autosharing` varchar(1) NOT NULL default '1',
  `website` varchar(255) NOT NULL,
  `extra` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uid` (`uid`,`type`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- Data dump for `boostack_user_social`

                 -- Constraints

                 -- Constraints`boostack_http_session`
    --
ALTER TABLE `boostack_http_session`
  ADD CONSTRAINT `http_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE;
--
-- Constraints`boostack_session_variable`
    --
ALTER TABLE `boostack_session_variable`
  ADD CONSTRAINT `session_variable_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `boostack_http_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Constraints`boostack_user_info`
    --
ALTER TABLE `boostack_user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Constraints`boostack_user_registration`
    --
ALTER TABLE `boostack_user_registration`
  ADD CONSTRAINT `user_registration_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Constraints`boostack_user_social`
    --
ALTER TABLE `boostack_user_social`
  ADD CONSTRAINT `user_social_ibfk_1` FOREIGN KEY (`id`) REFERENCES `boostack_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
 * */
?>