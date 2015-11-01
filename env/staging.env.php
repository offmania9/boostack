<?
/**
 * Boostack: local.env.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.1
 */

// ====== ENVIRONMEN local ======

// local
$init['url']['local'] = "http://localhost/foodraising/";
$init['path']['local'] = $_SERVER['DOCUMENT_ROOT']."/foodraising/";
$database['host']['local'] = 'localhost';
$database['name']['local'] = 'foodraising';
$database['username']['local'] = 'root';
$database['password']['local'] = '';

// staging
$init['url']['staging'] = "http://dev.foodraising.netatlas.it/";
$init['path']['staging'] = $_SERVER['DOCUMENT_ROOT']."/";
$database['host']['staging'] = 'localhost';
$database['name']['staging'] = 'fr_prod_8_10_15';
$database['username']['staging'] = 'root';
$database['password']['staging'] = 'admin';

// production
$init['url']['production'] = "";
$init['path']['production'] = $_SERVER['DOCUMENT_ROOT']."/foodraising/";
$database['host']['production'] = 'localhost';
$database['name']['production'] = '';
$database['username']['production'] = 'root';
$database['password']['production'] = 'admin';

?>