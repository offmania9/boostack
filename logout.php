<?php
/**
 * Boostack: logout.php
 * ========================================================================
 * Copyright 2015-2016 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.2
 */
require_once "core/environment_init.php";
$boostack->logout();
header("location: " . $boostack->url);
exit();
?>