<?
/**
 * Boostack: global.env.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
 */
$config["url_assets"] = "assets/";
$config["url_assets_img"] = $config["url_assets"]."img/";
// global Project infos
$config["url_logo"] = $config["url_assets_img"]."boostack_logo_x210.png";
$config["url_logo_dark"] = $config["url_assets_img"]."boostack_logo_x210.png";
$config["sitename"] = "Boostack.com";
$config["project_name"] = "Boostack";
$config["project_sitename"] = "Boostack.com";
$config["project_mission"] = "Boostack.com - Improve your development and build a modern website in minutes";

// global Html meta tags
$config["viewport"] = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0";
$config["site_title"] = "Boostack - a full stack web layer for PHP";
$config["site_keywords"] = "boostack, php, framework, website, productive, simplicity, seo, secure, mysql, open-source"; // comma separated
$config["site_description"] = "Improve your development and build a modern website in minutes";
$config["site_author"] = "stefano spagnolo";
$config["site_shortcuticon"] = $config["url_assets_img"]."favicon.ico";
$config["appletouchicon_144"] = $config["url_assets_img"]."apple-touch-icon-144-precomposed.png";
$config["appletouchicon_114"] = $config["url_assets_img"]."apple-touch-icon-114-precomposed.png";
$config["appletouchicon_72"] = $config["url_assets_img"]."apple-touch-icon-72-precomposed.png";
$config["appletouchicon_def"] = $config["url_assets_img"]."apple-touch-icon-57-precomposed.png";

// <!DOCTYPE html><html lang="$config["html_lang"]" ....
$config["html_lang"] = "en";

// Facebook Metatags. OpenGraph
$config["facebookMetaTag"] = TRUE; // enable or disable Facebook meta tags
$config["og_type"] = "product";
$config["og_title"] = "Boostack.com - Improve your development and build a modern website in minutes";
$config["fb_app_id"] = "";
$config["fb_app_secret"] = "";
$config["fb_admins"] = "";

// Social accounts
$config["twitter"] = "@getBoostack";
$config["gplus"] = "https://plus.google.com/+BoostackFramework/";
?>