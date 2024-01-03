<?php
/**
 * Boostack: helpers.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
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

function dumpPreparedQuery(PDOStatement $q) {
    echo "<pre>";
    $q->debugDumpParams();
    echo "</pre>";
}

?>
