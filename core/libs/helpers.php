<?php

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
