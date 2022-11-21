<?php
/**
 * Boostack: documentation.php
 * ========================================================================
 * Copyright 2014-2023 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.1
 */

require_once "../core/environment_init.php";

Template::addCssFile("lib/atom-one-light.min.css");
Template::render("documentation.phtml");

?>