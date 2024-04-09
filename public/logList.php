<?php
require __DIR__ . '/../vendor/autoload.php';
Core\Environment::init();
/**
 * Boostack: logList.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

use Core\Models\Config;
use Core\Models\Request;
use Core\Models\Auth;
use Core\Models\Log\Database\Log_Database_List;
use Core\Models\Language;
use Core\Models\Template;


if (!(Config::get('session_on') && Auth::isLoggedIn() && Auth::hasPrivilege(Auth::getUserLoggedObject(), PRIVILEGE_SUPERADMIN)))
    Request::goToUrl("home");

$logList = new Log_Database_List();
$logList->loadAll("id", "desc");

Template::render("logList.phtml", array(
    "logList" => $logList,
    "pageTitle" => Language::getLabel("navigation.log"),
));
