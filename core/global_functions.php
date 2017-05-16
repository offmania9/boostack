<?php
/**
 * Boostack: global_functions.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 3.1
 */

function d() {
    $args = func_get_args();
    echo "<pre>";
    for ($i = 0; $i < count($args); $i++) {
        var_dump($args[$i]);
    }
    echo "</pre>";
}

function dd() {
    $args = func_get_args();
    echo "<pre>";
    for ($i = 0; $i < count($args); $i++) {
        var_dump($args[$i]);
    }
    echo "</pre>";
    die();
}


?>