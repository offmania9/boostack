<?php
require __DIR__ . '/../vendor/autoload.php';
Core\Environment::init();
/**
 * Boostack: index.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

Core\Models\Template::render("index.phtml", array(
    "canonical" =>  Core\Models\Request::getFriendlyUrl("home"),
    "pageTitle" => Core\Models\Language::getLabel("navigation.home"),
));
