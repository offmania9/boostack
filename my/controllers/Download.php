<?php

namespace My\Controllers;

use Core\Models\Template;
use Core\Models\Request;
use Core\Models\Language;

class Download extends \My\Controller
{
    public static function init()
    {
        parent::init();
        Template::render("download.phtml", array(
            "canonical" =>  Request::getFriendlyUrl("download"),
            "pageTitle" => Language::getLabel("navigation.download"),
        ));
    }
}