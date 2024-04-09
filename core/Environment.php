<?php

namespace Core;

use Core\Models\Config;
use Core\Models\Request;
use Core\Models\Database\Database_PDO;
use Core\Models\Session\Session;
use Core\Models\Language;
use Core\Models\Log\Log_Driver;
use Core\Models\Log\Log_Level;
use Core\Models\Log\Logger;

class Environment
{
    public static function init()
    {
        try {
            require_once(__DIR__ . "/../core/libs/helpers.php");

            Config::init();

            if (Config::get('developmentMode')) {
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
            } else {
                error_reporting(0);
                ini_set('display_errors', 0);
            }
            require_once(__DIR__ . "/../my/pre_content.php");

            Request::init();

            if (Config::get('database_on')) {
                Database_PDO::getInstance(Config::get('db_host'), Config::get('db_name'), Config::get('db_username'), Config::get('db_password'), Config::get('db_port'));
                if (Config::get('session_on')) {
                    Session::init();

                    // if (Config::get('cookie_on') && Request::hasCookieParam(Config::get('cookie_name')) && Request::getCookieParam(Config::get('cookie_name')) != NULL) {
                    //     //user not logged in but remember-me cookie exists then try to perform loginByCookie function
                    //     $c = Request::getCookieParam(Config::get('cookie_name'));
                    //     if (!Auth::isLoggedIn() && $c !== "")
                    //         if (!Auth::loginByCookie($c)) //cookie is set but wrong (manually edited)
                    //             Auth::logout();
                    // }
                }
            }
            if (Config::get('language_on')) {
                Language::init();
            }
        } catch (\Exception $e) {
            $short_message = "System error. See log files.";
            $message = $short_message . $e->getMessage() . $e->getTraceAsString() . "\n";
            Logger::write($message, Log_Level::ERROR, Log_Driver::FILE);
            if (Config::get("developmentMode")) {
                echo $message;
            } else {
                echo $short_message;
            }
            exit();
        } catch (\PDOException $e) {
            $short_message = "Database error. See log files.";
            $message = $short_message . $e->getMessage() . $e->getTraceAsString() . "\n";
            Logger::write($message, Log_Level::ERROR, Log_Driver::FILE);
            if (Config::get("developmentMode")) {
                echo $message;
            } else {
                echo $short_message;
            }
            exit();
        }
    }
}

// try {
//     Request::init();
//     $CURRENTUSER = NULL;
//     if (Config::get('database_on')) {
//         Database_PDO::getInstance(Config::get('db_host'), Config::get('db_name'), Config::get('db_username'), Config::get('db_password'), Config::get('db_port'));
//         if (Config::get('session_on')) {
//             $objSession = new Session_HTTP(Config::get('session_timeout'), Config::get('session_lifespan'));
//             if (Config::get('cookie_on') && Request::hasCookieParam(Config::get('cookie_name')) && Request::getCookieParam(Config::get('cookie_name')) != NULL) {
//                 //user not logged in but remember-me cookie exists then try to perform loginByCookie function
//                 $c = Request::getCookieParam(Config::get('cookie_name'));
//                 if (!Auth::isLoggedIn() && $c !== "")
//                     if (!Auth::loginByCookie($c)) //cookie is set but wrong (manually edited)
//                         Auth::logout();
//             }
//             $CURRENTUSER = $objSession->GetUserObject(); // Auth::getUserLoggedObject();
//         }
//     }

//     if (Config::get('language_on')) {
//         $language = Language::init();
//     }
// } catch (\Exception $e) {
//     $short_message = "System error. See log files.";
//     $message = $short_message . $e->getMessage() . $e->getTraceAsString() . "\n";
//     Logger::write($message, Log_Level::ERROR, Log_Driver::FILE);
//     if (Config::get("developmentMode")) {
//         echo $message;
//     } else {
//         echo $short_message;
//     }
//     exit();
// } catch (\PDOException $e) {
//     $short_message = "Database error. See log files.";
//     $message = $short_message . $e->getMessage() . $e->getTraceAsString() . "\n";
//     Logger::write($message, Log_Level::ERROR, Log_Driver::FILE);
//     if (Config::get("developmentMode")) {
//         echo $message;
//     } else {
//         echo $short_message;
//     }
//     exit();
// }
