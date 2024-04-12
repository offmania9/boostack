<?php

namespace My\Controllers;


class Logout extends \My\Controller
{
    public static function init()
    {
        parent::init();
        \Boostack\Models\Auth::logout();
        \Boostack\Models\Request::goToUrl("home");        
    }
}