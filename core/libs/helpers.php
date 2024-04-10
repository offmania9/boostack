<?php
/**
 * Boostack: helpers.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */

/**
 * Dump variables in a formatted manner for debugging.
 *
 * @param mixed $args One or more variables to dump.
 */
function d()
{
    $args = func_get_args();
    echo "<pre>";
    foreach ($args as $arg) {
        var_dump($arg);
    }
    echo "</pre>";
}

/**
 * Dump variables in a formatted manner for debugging and halt the script execution.
 *
 * @param mixed $args One or more variables to dump.
 */
function dd()
{
    $args = func_get_args();
    echo "<pre>";
    foreach ($args as $arg) {
        var_dump($arg);
    }
    echo "</pre>";
    die();
}

/**
 * Dump a prepared query in a formatted manner for debugging.
 *
 * @param \PDOStatement $q The prepared query to dump.
 */
function dumpPreparedQuery(\PDOStatement $q)
{
    echo "<pre>";
    $q->debugDumpParams();
    echo "</pre>";
}
