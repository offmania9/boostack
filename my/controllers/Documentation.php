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

        View::render($templatePath, array(
            "canonical" =>  Request::getFriendlyUrl("docs/" . $currentVersion),
            "pageTitle" => Language::getLabel("navigation.documentation"),
            "currentVersion" => $currentVersion
        ));
    }
}
