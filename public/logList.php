<?php

/**
 * Boostack: logList.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */

require_once "../core/environment_init.php";

if (!(Config::get('session_on') && Auth::isLoggedIn() && Utils::hasPrivilege(Auth::getUserLoggedObject(), PRIVILEGE_SUPERADMIN)))
    Utils::goToUrl("home");

Template::render("logList.phtml",array(
    "filterField_Log" => $filterField_Log,
    "session_filter_log" => Session::get("filter_log")
));

?>