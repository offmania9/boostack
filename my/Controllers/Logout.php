<?php

declare(strict_types=1);

namespace My\Controllers;

class Logout extends \My\Controller
{
    public static function init(): void
    {
        parent::init();
        \Boostack\Models\Auth::logout();
        \Boostack\Models\Request::goToUrl("home");
    }
}
