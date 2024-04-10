<?php

namespace My\Controllers;


class Logout extends \My\Controller
{
    public static function init()
    {
        parent::init();
        \Core\Models\Auth::logout();
        \Core\Models\Request::goToUrl("home");        
    }
}