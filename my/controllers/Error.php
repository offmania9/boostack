<?php

namespace My\Controllers;

use Core\Models\Template;
use Core\Models\Request;
use Core\Models\Language;

class Error extends \My\Controller
{
    public static function init()
    {
        parent::init();
        // Call a template view Rendering
        Template::render("error.phtml", array(
            "canonical" =>  Request::getFriendlyUrl("error"),
            "pageTitle" => Language::getLabel("navigation.error"),
        ));
    }
}