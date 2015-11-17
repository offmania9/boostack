<?
/**
 * Boostack: database.lib.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

/**
 * Template to display a node.
 *
 * @defined $database DBVAR
 */
$pdo = Database_PDO::getInstance(
    $database['host'],
    $database['name'],
    $database['username'],
    $database['password']
);
?>