<?php

namespace My\Controllers;

use Core\Views\View;
use Core\Models\Request;
use Core\Models\Language;

class Index extends \My\Controller
{
    public static function init()
    {
        parent::init();
        View::render("index.phtml", array(
            "canonical" =>  Request::getFriendlyUrl("home"),
            "pageTitle" => Language::getLabel("navigation.home"),
        ));
    }
}