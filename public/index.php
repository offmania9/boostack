<?php

/**
 * Boostack: index.php
 * ========================================================================
 * Copyright 2014-2025 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */

require __DIR__ . '/../vendor/autoload.php';
Boostack\Environment::init();

use Boostack\Controllers\Router;
use Boostack\Models\Cache;
use Boostack\Models\Config;
use Boostack\Models\HttpMethod;
use Boostack\Models\Request;

function initRoutes(): Router
{
    $router = new Router();
    $router->addRoute('', [My\Controllers\Index::class, 'init']);
    $router->addRoute('home', [My\Controllers\Index::class, 'init']);
    $router->addRoute('login', [My\Controllers\Login::class, 'init'], HttpMethod::POST);
    $router->addRoute('login', [My\Controllers\Login::class, 'init'], HttpMethod::GET);
    $router->addRoute('setup', "setup/index.php");
    $router->addRoute('docs/(\d{1,3}(?:\.[\dx]{1,3}){0,2})/([a-zA-Z0-9_-]+)', [My\Controllers\Documentation::class, 'init'], HttpMethod::GET, ['version', 'docpath']);
    $router->addRoute('docs/(\d{1,3}(?:\.[\dx]{1,3}){0,2})', [My\Controllers\Documentation::class, 'init'], HttpMethod::GET, ['version']);
    $router->addRoute('docs', [My\Controllers\Documentation::class, 'init']);
    $router->addRoute('download', [My\Controllers\Download::class, 'init']);
    $router->addRoute('registration', [My\Controllers\Registration::class, 'init']);
    $router->addRoute('registration', [My\Controllers\Registration::class, 'init'], HttpMethod::POST);
    $router->addRoute('logout', [My\Controllers\Logout::class, 'init']);
    $router->addRoute('downloadLatest', [My\Controllers\DownloadLatest::class, 'init']);
    $router->addRoute('log', [My\Controllers\LogList::class, 'init']);
    $router->addRoute('api/([^\.]+)', [My\Controllers\Api::class, 'init'], HttpMethod::GET, ['request']);
    $router->addRoute('api/([^\.]+)', [My\Controllers\Api::class, 'init'], HttpMethod::POST, ['request']);
    $router->addRoute('api/([^\.]+)', [My\Controllers\Api::class, 'init'], HttpMethod::PUT, ['request']);
    $router->addRoute('api/([^\.]+)', [My\Controllers\Api::class, 'init'], HttpMethod::DELETE, ['request']);
    #$router->addRoute('course', [My\Controllers\CourseController::class, 'init']);
    return $router;
}

$r = new Router();
if (Config::get("cache_enabled") && Config::get("cache_routing_enabled")) {
    $cache_routing_key = Config::get("cache_routing_key");
    if (Cache::has($cache_routing_key)) {
        $json = Cache::get($cache_routing_key);
        $r = Router::fromArray(json_decode($json, true));
    } else {
        $r = initRoutes();
        Cache::set($cache_routing_key, json_encode($r));
    }
} else {
    $r = initRoutes();
}

$r->dispatch(Request::getServerParam('REQUEST_URI'));
