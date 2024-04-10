<?php

namespace My\Controllers;

use Core\Models\Template;
use Core\Models\Request;
use Core\Models\Language;

class Index extends \My\Controller
{
    public static function init()
    {
        parent::init();
        Template::render("index.phtml", array(
            "canonical" =>  Request::getFriendlyUrl("home"),
            "pageTitle" => Language::getLabel("navigation.home"),
        ));
    }
}