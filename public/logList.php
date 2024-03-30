<?php

/**
 * Boostack: logList.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

require_once "../core/environment_init.php";

if (!(Config::get('session_on') && Auth::isLoggedIn() && Utils::hasPrivilege(Auth::getUserLoggedObject(), PRIVILEGE_SUPERADMIN)))
    Utils::goToUrl("home");

$logList = new Log_Database_List();
$logList->loadAll("id", "desc");

Template::render("logList.phtml", array(
    "logList" => $logList,
    "pageTitle" => Language::getLabel("navigation.log"),
));
