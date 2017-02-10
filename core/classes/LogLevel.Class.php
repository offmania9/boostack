<?php
/**
 * Boostack: LogLevel.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
abstract class LogLevel  {//extends SplEnum

    const __default = self::Information;

    const Error = "error";
    const Warning = "warning";
    const Failure = "failure";
    const Information = "information";
    const Success = "success";
    const User = "user";
    const Cronjob = "cronjob";
}