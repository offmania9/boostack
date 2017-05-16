<?php
/**
 * Boostack: Template.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 3.1
 */

class Template
{

    private static $customCssFiles;
    private static $customJsFiles;

    public static function render($template, $values = null)
    {
        $templateDir = ROOTPATH.Config::get('template_path');
        $templateFile = $templateDir.$template;
        if(!file_exists($templateFile))
            throw new Exception_FileNotFound("Template file ".$templateFile. "not found");
        if($values !== null) {
            foreach ($values as $valueName => $value) {
                ${$valueName} = $value;
            }
        }
        require $templateFile;
    }

    public static function renderDefaultJSFiles()
    {
        $defaultJsFiles = Config::get("default_js_files");
        $defaultIeJsFiles = Config::get("default_ie_js_files");
        if(!empty($defaultJsFiles)) {
            foreach ($defaultJsFiles as $jsFile) {
                self::renderJSFile($jsFile);
            }
        }
        if(!empty($defaultIeJsFiles)) {
            echo "<!--[if lt IE 9]>";
            foreach ($defaultIeJsFiles as $jsFile) {
                self::renderJSFile($jsFile);
            }
            echo "<![endif]-->";
        }
    }

    public static function renderDefaultCSSFiles()
    {
        $defaultCssFiles = Config::get("default_css_files");
        if(!empty($defaultCssFiles)) {
            foreach ($defaultCssFiles as $cssFile) {
                self::renderCSSFile($cssFile);
            }
        }
    }

    public static function renderCustomCSSFiles()
    {
        if(!empty(self::$customCssFiles)) {
            foreach(self::$customCssFiles as $customCssFile) {
                self::renderCSSFile($customCssFile["path"],$customCssFile["isAbsolute"]);
            }
        }
    }

    public static function renderCustomJSFiles()
    {
        if(!empty(self::$customJsFiles)) {
            foreach(self::$customJsFiles as $customJsFile) {
                self::renderJSFile($customJsFile["path"],$customJsFile["isAbsolute"]);
            }
        }
    }

    public static function addCssFile($cssFile, $isAbsolute = false)
    {
        self::$customCssFiles[] = [
            "path" => $cssFile,
            "isAbsolute" => $isAbsolute
        ];
    }

    public static function addJsFile($jsFile, $isAbsolute = false)
    {
        self::$customJsFiles[] = [
            "path" => $jsFile,
            "isAbsolute" => $isAbsolute
        ];
    }

    public static function renderJSFile($file, $isAbsolute = false)
    {
        if($isAbsolute) {
            echo '<script type="text/javascript" src="'.$file.'"></script>';
        } else {
            $minified = Config::get('developmentMode') ? "" : ".min";
            $fileName = str_replace(".js", $minified . ".js", $file);
            echo '<script type="text/javascript" src="'.Config::get('url').Config::get('js_path').$fileName.'"></script>';
        }
    }

    public static function renderCSSFile($file, $isAbsolute = false)
    {
        if($isAbsolute) {
            echo '<link href="'.$file.'" rel="stylesheet" type="text/css"/>';
        } else {
            $minified = Config::get('developmentMode') ? "" : ".min";
            $fileName = str_replace(".css", $minified . ".css", $file);
            echo '<link href="'.Config::get('url').Config::get('css_path').$fileName.'" rel="stylesheet" type="text/css"/>';
        }
    }

    public static function getImageLink($image)
    {
        return Config::get('url').Config::get('image_path').$image;
    }

    public static function getMailTemplate($mail, $parameters = null)
    {
        $file = ROOTPATH.Config::get('mail_template_path').$mail;
        if(!file_exists($file)) throw new Exception("Mail templating file ($file) not found");
        $template = file_get_contents($file);
        foreach ($parameters as $template_param => $value){
            $template = str_replace("[$template_param]", $value, $template);
        }
        return $template;
    }


}