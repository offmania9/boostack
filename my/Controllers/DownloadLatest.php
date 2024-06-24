<?php

namespace My\Controllers;

class DownloadLatest extends \My\Controller
{
    public static function init()
    {
        parent::init();
        header("location: https://github.com/offmania9/boostack/archive/master.zip");
        exit();
    }
}
