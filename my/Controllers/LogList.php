<?php

namespace My\Controllers;

use Boostack\Models\Config;
use Boostack\Models\Request;
use Boostack\Models\Auth;
use Boostack\Views\View;
use Boostack\Models\Language;
use Boostack\Models\Log\Database\Log_Database_List;

class LogList extends \My\Controller
{
    public static function init()
    {
        parent::init();
        if (!(Config::get('session_on') && Auth::isLoggedIn() && Auth::hasPrivilege(Auth::getUserLoggedObject(), PRIVILEGE_SUPERADMIN)))
            Request::goToUrl("home");

        $logList = new Log_Database_List();
        $logList->loadAll("id", "desc");

        View::render("logList.phtml", array(
            "logList" => $logList,
            "pageTitle" => Language::getLabel("navigation.log"),
        ));
    }
}
