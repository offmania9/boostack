<?php

namespace My\Controllers;

use Boostack\Views\View;
use Boostack\Models\Request;
use Boostack\Models\Language;

class Documentation extends \My\Controller
{
    public static function init()
    {
        parent::init();
        $defaultVersion = "6.x";
        $templatePath = "documentation_" . $defaultVersion . ".phtml";
        $defaultUrl = Request::getFriendlyUrl("docs/" . $defaultVersion . "");
        $currentVersion = $defaultVersion;
        if (Request::hasQueryParam("version")) {
            switch (Request::getQueryParam("version")) {
                case "6.x": {
                        $currentVersion = "6.x";
                        break;
                    }
                case "5.0": {
                        $currentVersion = "5.0";
                        break;
                    }
                default: {
                        Request::goToUrl($defaultUrl);
                    }
            }
            $templatePath = "documentation_" . $currentVersion . ".phtml";
        } else {
            Request::goToUrl($defaultUrl);
        }

        $jsonFilePath = ROOTPATH . "/../lang/url_mapping.json";
        $docpage_path = "setup";
        $docpage_title = "Setup";
        $partial_filename = "setup.phtml";
        $result = array("jsonData" => self::getItemFromJson($jsonFilePath), "item" => null);
        try {
            if (Request::hasQueryParam("docpath")) {
                $docpage_path = Request::getQueryParam("docpath");
                $result = self::getItemByDocpagePath($result["jsonData"], $docpage_path);
                if ($result !== NULL) {
                    $current_item = $result['item'];
                    $docpage_title = $current_item["title"];
                    $partial_filename = $current_item["partial_filename"];
                } else
                    Request::goToUrl($defaultUrl . "/setup");
            }
            View::render($templatePath, array(
                "canonical" =>  Request::getFriendlyUrl("docs/" . $currentVersion . "/" . $docpage_path),
                "pageTitle" => Language::getLabel("navigation.documentation") . " - " . $docpage_title,
                "currentVersion" => $currentVersion,
                "partial_filename" => $partial_filename,
                "all_items" => $result['jsonData'],
                "current_item" => $result['item']
            ));
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function getItemByDocpagePath($jsonData, $docpage_path)
    {
        foreach ($jsonData['categories'] as $category) {
            foreach ($category['items'] as $item) {
                if ($item['path'] === $docpage_path) {
                    return ['jsonData' => $jsonData, 'item' => $item];
                }
            }
        }
        return NULL;
    }

    public static function getItemFromJson($jsonFilePath)
    {
        if (!file_exists($jsonFilePath)) {
            throw new \Exception("File not found: $jsonFilePath");
        }
        $jsonContent = file_get_contents($jsonFilePath);
        $jsonData = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON decode error: " . json_last_error_msg());
        }
        return $jsonData;
    }
}
