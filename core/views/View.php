<?php

namespace Core\Views;
use Core\Models\Config;
use Core\Exception\Exception_FileNotFound;

/**
 * Boostack: Template.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 6.0
 */

abstract class View

{
    private static $customCssFiles;

    private static $customJsFiles;

    /**
     * Render a template file.
     *
     * @param string $template The template file path.
     * @param array|null $values An associative array of values to be passed to the template.
     * @throws \Exception If the template file is not found.
     */
    public static function render(string $template, ?array $values = null): void
    {
        if (empty($template)) {
            throw new \Exception("Missing 'template' param");
        }

        $templateDir = ROOTPATH . Config::get('template_path');
        $templateFile = $templateDir . $template;

        if (!file_exists($templateFile)) {
            throw new Exception_FileNotFound("Template file " . $templateFile . " not found");
        }

        if ($values !== null) {
            foreach ($values as $valueName => $value) {
                ${$valueName} = $value;
            }
        }

        require $templateFile;
    }

    /**
     * Render an error page and set the HTTP response code.
     *
     * @param int $code The HTTP response code.
     * @param string|null $template The template file path for the error page.
     * @param array|null $values An associative array of values to be passed to the error template.
     * @return void
     */
    public static function renderErrorPage(int $code, ?string $template = null, ?array $values = null): void
    {
        http_response_code($code);
        $errorTemplate = $template !== null ? $template : Config::get("default_error_page");
        self::render($errorTemplate, $values);
        die();
    }

    /**
     * Render default JS files specified in the configurations.
     *
     * @return void
     */
    public static function renderDefaultJSFiles(): void
    {
        $defaultJsFiles = Config::get("default_js_files");
        $defaultIeJsFiles = Config::get("default_ie_js_files");

        if (!empty($defaultJsFiles)) {
            foreach ($defaultJsFiles as $jsFile) {
                self::renderJSFile($jsFile);
            }
        }

        if (!empty($defaultIeJsFiles)) {
            echo "<!--[if lt IE 9]>";
            foreach ($defaultIeJsFiles as $jsFile) {
                self::renderJSFile($jsFile);
            }
            echo "<![endif]-->";
        }
    }

    /**
     * Render the JS file inclusion string based on the provided parameters.
     *
     * @param string $file
     * @param bool $isAbsolute
     */
    public static function renderJSFile(string $file, bool $isAbsolute = false): void
    {
        if ($isAbsolute) {
            echo '<script type="text/javascript" src="' . $file . '"></script>';
        } else {
            $minified = Config::get('developmentMode') ? "" : ".min";
            $fileName = str_replace(".js", $minified . ".js", $file);
            echo '<script type="text/javascript" src="' . Config::get('url') . Config::get('js_path') . $fileName . '"></script>';
        }
    }

    /**
     * Render default CSS files specified in the configurations.
     */
    public static function renderDefaultCSSFiles(): void
    {
        self::renderDefaultCSSFiles_critical();
        self::renderDefaultCSSFiles_nonCritical();
    }

    /**
     * Render critical default CSS files specified in the configurations.
     */
    public static function renderDefaultCSSFiles_critical(): void
    {
        $defaultCssFiles = Config::get("default_css_files_critical");
        if (!empty($defaultCssFiles)) {
            foreach ($defaultCssFiles as $cssFile) {
                self::renderCSSFile($cssFile);
            }
        }
    }

    /**
     * Render non-critical default CSS files specified in the configurations.
     */
    public static function renderDefaultCSSFiles_nonCritical(): void
    {
        $defaultCssFiles = Config::get("default_css_files_noncritical");
        if (!empty($defaultCssFiles)) {
            foreach ($defaultCssFiles as $cssFile) {
                self::renderCSSFile($cssFile);
            }
        }
    }

    /**
     * Render custom CSS files specified on-the-fly.
     */
    public static function renderCustomCSSFiles(): void
    {
        if (!empty(self::$customCssFiles)) {
            foreach (self::$customCssFiles as $customCssFile) {
                self::renderCSSFile($customCssFile["path"], $customCssFile["isAbsolute"]);
            }
        }
    }

    /**
     * Render custom JS files specified on-the-fly.
     */
    public static function renderCustomJSFiles(): void
    {
        if (!empty(self::$customJsFiles)) {
            foreach (self::$customJsFiles as $customJsFile) {
                self::renderJSFile($customJsFile["path"], $customJsFile["isAbsolute"]);
            }
        }
    }

    /**
     * Add a CSS file to be rendered in the template.
     *
     * @param string $cssFile
     * @param bool $isAbsolute
     */
    public static function addCssFile(string $cssFile, bool $isAbsolute = false): void
    {
        self::$customCssFiles[] = [
            "path" => $cssFile,
            "isAbsolute" => $isAbsolute
        ];
    }

    /**
     * Add a JS file to be rendered in the template.
     *
     * @param string $jsFile
     * @param bool $isAbsolute
     */
    public static function addJsFile(string $jsFile, bool $isAbsolute = false): void
    {
        self::$customJsFiles[] = [
            "path" => $jsFile,
            "isAbsolute" => $isAbsolute
        ];
    }

    /**
     * Render the CSS file inclusion string based on the provided parameters.
     *
     * @param string $file
     * @param bool $isAbsolute
     */
    public static function renderCSSFile(string $file, bool $isAbsolute = false): void
    {
        if ($isAbsolute) {
            echo '<link href="' . $file . '" rel="stylesheet" type="text/css"/>';
        } else {
            $minified = Config::get('developmentMode') ? "" : ".min";
            $fileName = str_replace(".css", $minified . ".css", $file);
            echo '<link href="' . Config::get('url') . Config::get('css_path') . $fileName . '" rel="stylesheet" type="text/css"/>';
        }
    }

    /**
     * Get the absolute URL of the specified image.
     *
     * @param string $image
     * @return string
     */
    public static function getImageLink(string $image): string
    {
        return Config::get('url') . Config::get('image_path') . $image;
    }


    /**
     * Return the mail template specified by the parameter.
     *
     * @param string $mail
     * @param array|null $parameters
     * @return string
     * @throws \Exception
     */
    public static function getMailTemplate(string $mail, ?array $parameters = null): string
    {
        $file = ROOTPATH . Config::get('mail_template_path') . $mail;
        if (!file_exists($file)) {
            throw new \Exception("Mail templating file ($file) not found");
        }
        $template = file_get_contents($file);
        if (!empty($parameters)) {
            foreach ($parameters as $templateParam => $value) {
                $template = str_replace("[$templateParam]", $value, $template);
            }
        }
        return $template;
    }
}
