<?php
namespace Core\Models\Field;
/**
 * Boostack: FieldType.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */
abstract class FieldType extends \Core\Models\BasicEnum
{
    const STRING = "string";
    const USERNAME = "username";
    const EMAIL = "email";
    const PASSWORD = "password";
    const NUMERIC = "numeric";
    const INTEGER = "integer";
    const FLOAT = "float";
    const DATE = "date";
    const COMBO = "combo";
    const FLAG = "checkbox";
    const TEXT = "text";
}
