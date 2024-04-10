<?php

namespace My\Controllers;

use Core\Views\View;
use Core\Models\Request;
use Core\Models\Language;

class Download extends \My\Controller
{
    public static function init()
    {
        parent::init();
        View::render("download.phtml", array(
            "canonical" =>  Request::getFriendlyUrl("download"),
            "pageTitle" => Language::getLabel("navigation.download"),
        ));
    }
}