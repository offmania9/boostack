<?php

namespace My\Controllers;

use Boostack\Views\View;
use Boostack\Models\Request;
use Boostack\Models\Language;

class Error extends \My\Controller
{
    public static function init()
    {
        parent::init();
        // Call a template view Rendering
        View::render("error.phtml", array(
            "canonical" =>  Request::getFriendlyUrl("error"),
            "pageTitle" => Language::getLabel("navigation.error"),
        ));
    }
}