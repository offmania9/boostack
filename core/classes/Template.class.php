<?php

class Template {

    public static function render($template, $values = null) {
        $templatePath = ROOTPATH.Config::get('template_path');
        if($values !== null) {
            foreach ($values as $valueName => $value) {
                ${$valueName} = $value;
            }
        }
        require $templatePath.$template;
    }

    public static function renderDefaultJSFiles() {
        $defaultJsFiles = Config::get("default_js_files");
        if(!empty($defaultJsFiles)) {
            foreach ($defaultJsFiles as $jsFile) {
                self::renderJSFile($jsFile);
            }
        }
    }

    public static function renderDefaultIEJSFiles() {
        $defaultIeJsFiles = Config::get("default_ie_js_files");
        if(!empty($defaultIeJsFiles)) {
            echo "<!--[if lt IE 9]>";
            foreach ($defaultIeJsFiles as $jsFile) {
                self::renderJSFile($jsFile);
            }
            echo "<![endif]-->";
        }
    }

    public static function renderDefaultCSSFiles() {
        $defaultCssFiles = Config::get("default_css_files");
        if(!empty($defaultCssFiles)) {
            foreach ($defaultCssFiles as $cssFile) {
                self::renderCSSFile($cssFile);
            }
        }
    }

    public static function renderJSFile($file) {
        $minified = Config::get('developmentMode') ? "" : ".min";
        $fileName = str_replace(".js", $minified . ".js", $file);
        echo '<script type="text/javascript" src="'.Config::get('url').Config::get('js_path').$fileName.'"></script>';
    }

    public static function renderCSSFile($file) {
        $minified = Config::get('developmentMode') ? "" : ".min";
        $fileName = str_replace(".css", $minified . ".css", $file);
        echo '<link href="'.Config::get('url').Config::get('css_path').$fileName.'" rel="stylesheet" type="text/css"/>';
    }

    public static function getImageLink($image) {
        return Config::get('url').Config::get('image_path').$image;
    }

    public static function getMailTemplate($mail, $parameters = null) {
        $file = ROOTPATH.Config::get('mail_template_path').$mail;
        if(!file_exists($file)) throw new Exception("Mail templating file ($file) not found");
        $template = file_get_contents($file);
        foreach ($parameters as $template_param => $value){
            $template = str_replace("[$template_param]", $value, $template);
        }
        return $template;
    }


}