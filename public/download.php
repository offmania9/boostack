<?php

/**
 * Boostack: download.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

require_once "../core/environment_init.php";

Template::render("download.phtml", array(
    "canonical" =>  Utils::getFriendlyUrl("download"),
    "pageTitle" => Language::getLabel("navigation.download"),
));
