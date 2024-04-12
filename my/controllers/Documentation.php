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
        View::render("documentation.phtml", array(
            "canonical" =>  Request::getFriendlyUrl("documentation"),
            "pageTitle" => Language::getLabel("navigation.documentation"),
        ));
    }
}