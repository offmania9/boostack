<?php

namespace My\Controllers;

use Core\Models\Template;
use Core\Models\Request;
use Core\Models\Language;

class DownloadLatest extends \My\Controller
{
    public static function init()
    {
        parent::init();
        header("location: https://github.com/offmania9/boostack/archive/master.zip");
        exit();
    }
}
