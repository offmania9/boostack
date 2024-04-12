<?php

namespace My\Controllers;

use Boostack\Views\View;
use Boostack\Models\Request;
use Boostack\Models\Language;

class DownloadLatest extends \My\Controller
{
    public static function init()
    {
        parent::init();
        header("location: https://github.com/offmania9/boostack/archive/master.zip");
        exit();
    }
}
