<?php

namespace My\Controllers;

use Core\Models\Template;
use Core\Models\Request;
use Core\Models\Language;

class Documentation extends \My\Controller
{
    public static function init()
    {
        parent::init();
        Template::render("documentation.phtml", array(
            "canonical" =>  Request::getFriendlyUrl("documentation"),
            "pageTitle" => Language::getLabel("navigation.documentation"),
        ));
    }
}