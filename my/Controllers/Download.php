<?php

namespace My\Controllers;

use Boostack\Views\View;
use Boostack\Models\Request;
use Boostack\Models\Language;

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