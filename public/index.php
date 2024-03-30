<?php

/**
 * Boostack: index.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

require_once "../core/environment_init.php";

Template::render("index.phtml", array(
    "canonical" =>  Utils::getFriendlyUrl("home"),
    "pageTitle" => Language::getLabel("navigation.home"),
));
