<?php
/**
 * Boostack: logout.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */

require_once "core/environment_init.php";

Auth::logout();
header("location: " . Config::get("url"));
exit();

?>