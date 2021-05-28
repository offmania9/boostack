<?php
/**
 * Boostack: Template.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 4
 */

class Template
{
    /**
     * @var
     */
    private static $customCssFiles;

    /**
     * @var
     */
    private static $customJsFiles;

    /**
     * Renderizza un file di template
     *
     * @param $template
     * @param null $values
     * @throws Exception
     * @throws Exception_FileNotFound
     */
    public static function render($template, $values = null)
    {
        if(empty($template))
            throw new Exception("Missing 'template' param");
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

    /**
     * Renderizza una pagina di errore restituendo il relativo http response code
     *
     * @param $code
     * @param null $template
     * @param null $values
     */
    public static function renderErrorPage($code, $template = null, $values = null)
    {
        http_response_code($code);
        $errorTemplate = $template != null ? $template : Config::get("default_error_page");
        self::render($errorTemplate, $values);
        die();
    }

    /**
     * Stampa i file JS di default specificati nelle configurazioni
     */
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

    /**
     * Stampa i file CSS di default specificati nelle configurazioni
     */
    public static function renderDefaultCSSFiles()
    {
        $defaultCssFiles = Config::get("default_css_files");
        if(!empty($defaultCssFiles)) {
            foreach ($defaultCssFiles as $cssFile) {
                self::renderCSSFile($cssFile);
            }
        }
    }

    /**
     * Stampa i file CSS custom specificati on-the-fly
     */
    public static function renderCustomCSSFiles()
    {
        if(!empty(self::$customCssFiles)) {
            foreach(self::$customCssFiles as $customCssFile) {
                self::renderCSSFile($customCssFile["path"],$customCssFile["isAbsolute"]);
            }
        }
    }

    /**
     * Stampa i file JS custom specificati on-the-fly
     */
    public static function renderCustomJSFiles()
    {
        if(!empty(self::$customJsFiles)) {
            foreach(self::$customJsFiles as $customJsFile) {
                self::renderJSFile($customJsFile["path"],$customJsFile["isAbsolute"]);
            }
        }
    }

    /**
     * Aggiunge un file CSS da renderizzare nel template
     *
     * @param $cssFile
     * @param bool $isAbsolute
     */
    public static function addCssFile($cssFile, $isAbsolute = false)
    {
        self::$customCssFiles[] = [
            "path" => $cssFile,
            "isAbsolute" => $isAbsolute
        ];
    }

    /**
     * Aggiunge un file JS da renderizzare nel template
     *
     * @param $jsFile
     * @param bool $isAbsolute
     */
    public static function addJsFile($jsFile, $isAbsolute = false)
    {
        self::$customJsFiles[] = [
            "path" => $jsFile,
            "isAbsolute" => $isAbsolute
        ];
    }

    /**
     * Stampa la stringa di inclusione del file JS specificato come parametro
     *
     * @param $file
     * @param bool $isAbsolute
     */
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

    /**
     * Stampa la stringa di inclusione del file CSS specificato come parametro
     *
     * @param $file
     * @param bool $isAbsolute
     */
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

    /**
     * Ritorna l'URL assoluta dell'immagine specificata come parametro
     *
     * @param $image
     * @return string
     */
    public static function getImageLink($image)
    {
        return Config::get('url').Config::get('image_path').$image;
    }

    /**
     * Ritorna il template mail specificato come parametro
     *
     * @param $mail
     * @param null $parameters
     * @return bool|mixed|string
     * @throws Exception
     */
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