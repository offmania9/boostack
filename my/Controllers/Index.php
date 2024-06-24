<?php

namespace My\Controllers;

use Boostack\Views\View;
use Boostack\Models\Request;
use Boostack\Models\Language;

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