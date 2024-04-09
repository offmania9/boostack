<?php
namespace Core\Models\Utils;
use Core\Models\Config;
/**
 * Boostack: Validator.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

class Validator
{

    private $error;

    private $errorMessages;

    const RULES_SEPARATOR = "|";

    const INTRA_RULES_SEPARATOR = ":";


    /**
     * Validates an associative array of values.
     *
     * @param array $input An associative array where keys correspond to input value names.
     *                     Example: ['name' => 'Foo', 'age' => 42, 'email' => 'foo@bar.com']
     * @param array $rules An associative array where keys correspond to field names to validate,
     *                     and values correspond to validation rules separated by '|'.
     *                     Example: ['name' => 'string', 'age' => 'numeric', 'email' => 'email']
     * @return array|bool Returns true on success, otherwise returns an array containing
     *                    the fields that did not pass validation.
     */
    public function validate(array $input, array $rules)
    {
        $this->error = false;
        $this->errorMessages = [];

        foreach ($rules as $key => $value) {
            $value = trim($value, self::RULES_SEPARATOR);
            $elemRules = explode(self::RULES_SEPARATOR, $value);

            foreach ($elemRules as $rule) {
                if ($rule == "required") {
                    if (!$this->$rule($key, $input)) {
                        $this->setError($key, $rule);
                    }
                } else {
                    if (isset($input[$key])) {
                        $valToValidate = $input[$key];
                        if (!$this->$rule($valToValidate)) {
                            $this->setError($key, $rule);
                        }
                    }
                }
            }
        }

        if ($this->hasError()) {
            return ["error" => true, "messages" => $this->errorMessages];
        }
        return true;
    }


    /**
     * Validates form values received from the frontend.
     *
     * @param array $input An associative array where keys correspond to field IDs and values
     *                     contain an array with the input values.
     *                     Example:
     *                     [
     *                          12 => ["values" => ["Alessio"]],
     *                          3 => ["values" => ["35"]],
     *                          4 => ["values" => ["12,0000"]],
     *                          6 => ["values" => ["Yes"]],
     *                          22 => ["values" => ["Check2", "Check3"]],
     *                     ]
     * @param array $rules An associative array where keys correspond to field IDs and values
     *                     contain validation rules separated by '|'.
     *                     Example:
     *                     [
     *                          12 => "Required|String",
     *                          3 => "Required|Integer",
     *                          4 => "Required|Float",
     *                          6 => "Required",
     *                          22 => "Required|Min:1|Max:3",
     *                          35 => "Required",
     *                     ]
     * @return array|bool Returns true on success, otherwise returns an array containing
     *                    the fields that did not pass validation.
     */
    public function validateFormValues(array $input, array $rules)
    {
        $this->error = false;
        $this->errorMessages = [];

        foreach ($rules as $key => $value) {
            $elemRules = explode(self::RULES_SEPARATOR, trim($value, self::RULES_SEPARATOR));

            foreach ($elemRules as $rule) {
                $fullRule = explode(self::INTRA_RULES_SEPARATOR, $rule);
                $rule_1 = $fullRule[0];
                $rule_2 = count($fullRule) > 1 ? $fullRule[1] : null;

                switch ($rule_1) {
                    case "required":
                        if (!($this->required($key, $input) && count($input[$key]["values"]) && !empty($input[$key]["values"][0]))) {
                            $this->setError($key, $rule_1);
                        }
                        break;
                    case "string":
                        if (isset($input[$key]) && count($input[$key]["values"]) > 0) {
                            if (!$this->string($input[$key]["values"])) {
                                $this->setError($key, $rule_1);
                            }
                        }
                        break;
                    case "integer":
                        if (isset($input[$key]) && count($input[$key]["values"]) > 0) {
                            if (!$this->integer($input[$key]["values"])) {
                                $this->setError($key, $rule_1);
                            }
                        }
                        break;
                    case "float":
                        if (isset($input[$key]) && count($input[$key]["values"]) > 0) {
                            if (!$this->float($input[$key]["values"])) {
                                $this->setError($key, $rule_1);
                            }
                        }
                        break;
                    case "min":
                        if (isset($input[$key])) {
                            if (empty($rule_2) || count($input[$key]["values"]) < $rule_2) {
                                $this->setError($key, $rule);
                            }
                        }
                        break;
                    case "max":
                        if (isset($input[$key])) {
                            if (empty($rule_2) || count($input[$key]["values"]) > $rule_2) {
                                $this->setError($key, $rule);
                            }
                        }
                        break;
                    case "in":
                        // TO-DO
                        break;
                    default:
                        break;
                }
            }
        }

        if ($this->hasError()) {
            return ["error" => true, "messages" => $this->errorMessages];
        }
        return true;
    }

    /**
     * Alternatively, validation functions can be used individually.
     * Example:
     * $validator = new Validator();
     * $validator->email("foo@bar.it");
     * Returns true/false.
     */



    /**
     * Validate if the input is a string or an array of strings.
     *
     * @param mixed $input
     * @return bool
     */
    public static function string($input)
    {
        $res = true;
        if (is_array($input)) {
            foreach ($input as $elem) {
                if (!is_string($elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = is_string($input);
        }
        return $res;
    }

    /**
     * Validate strings containing only letters.
     *
     * @param string $input
     * @return int
     */
    public static function onlyChars($input)
    {
        return preg_match('/^[A-Za-z]+$/', $input);
    }

    /**
     * Validate strings containing only letters and spaces.
     *
     * @param string $input
     * @return int
     */
    public static function onlyCharsWithSpace($input)
    {
        return preg_match('/^[A-Za-zÀ-ÿ ]+$/', $input);
    }

    /**
     * Validate strings containing only letters, numbers, or "-".
     *
     * @param string $input
     * @return int
     */
    public static function onlyCharNumbersUnderscore($input)
    {
        return preg_match('/^[A-Za-z1-9_]+$/', $input);
    }

    /**
     * Validate addresses like Google Maps default address or simply letters, numbers, accents, "-", "_", ",", and spaces.
     *
     * @param string $input
     * @return int
     */
    public static function address($input)
    {
        return preg_match("/^[A-Za-z0-9À-ÿ _\-,]*[A-Za-z0-9À-ÿ][A-Za-z0-9À-ÿ _\-,]*$/", $input);
    }

    /**
     * Validate operators for the view method.
     *
     * @param string $rule
     * @return bool
     */
    public static function operators($rule)
    {
        $rules = ["like", "not like", "&lt;&gt;", "=", "&lt;", "&lt;=", "&gt;", "&gt;="];
        return in_array($rule, $rules);
    }

    /**
     * Validate if the input is numeric or an array of numerics.
     *
     * @param mixed $input
     * @return bool
     */
    public static function numeric($input)
    {
        $res = true;
        if (is_array($input)) {
            foreach ($input as $elem) {
                if (!is_numeric($elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = is_numeric($input);
        }
        return $res;
    }

    /**
     * Validate if the input is alphanumeric or an array of alphanumeric strings.
     *
     * @param mixed $input
     * @return bool|int
     */
    public static function alphanumeric($input)
    {
        $res = true;
        if (is_array($input)) {
            foreach ($input as $elem) {
                if (!preg_match('/^[A-Za-z0-9 _]*[A-Za-z0-9_]+$/', $elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = preg_match('/^[A-Za-z0-9 _]*[A-Za-z0-9_]+$/', $input);
        }
        return $res;
    }

    /**
     * Validate if the input is an integer or an array of integers.
     *
     * @param mixed $input
     * @return bool|int
     */
    public static function integer($input)
    {
        $res = true;
        if (is_array($input)) {
            foreach ($input as $elem) {
                if (!preg_match('/^\d+$/', $elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = preg_match('/^\d+$/', $input);
        }
        return $res;
    }

    /**
     * Validate if the input is a float number or an array of float numbers.
     *
     * @param mixed $input
     * @return bool|int
     */
    public static function float($input)
    {
        $res = true;
        if (is_array($input)) {
            foreach ($input as $elem) {
                if (!preg_match('/^[-+]?(\d*[.])?\d+$/', $elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = preg_match('/^[-+]?(\d*[.])?\d+$/', $input);
        }
        return $res;
    }

    /**
     * Check if an element exists in an array.
     *
     * @param mixed $elem
     * @param array $array
     * @return bool
     */
    public static function in($elem, $array)
    {
        return in_array($elem, $array);
    }

    /**
     * Validate if the input is a valid email address.
     *
     * @param mixed $input
     * @return bool
     */
    public static function email($input)
    {
        return is_string($input) && filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate if the input is a phone number.
     *
     * @param mixed $input
     * @return bool
     */
    public static function phone($input)
    {
        // Regular expression pattern for a basic phone number validation
        $pattern = '/^[0-9]{10}$/'; // Esempio: 1234567890

        // Check if the input matches the pattern
        return preg_match($pattern, $input);
    }


    /**
     * Validate password for login purposes.
     *
     * @param string $password
     * @return bool
     */
    public static function password_login($password)
    {
        return !empty($password) && strlen($password) >= Config::get("password_min_length") && strlen($password) <= Config::get("password_max_length");
    }

    /**
     * Validate password.
     *
     * @param string $password
     * @return bool
     */
    public static function password($password)
    {
        return !empty($password) && strlen($password) >= Config::get("password_min_length") && strlen($password) <= Config::get("password_max_length");
    }

    /**
     * Validate if the password is strong.
     *
     * @param string $password
     * @return int
     */
    public static function strongPassword($password)
    {
        return preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password);
    }

    /**
     * Validate username.
     *
     * @param string $username
     * @return bool
     */
    public static function username($username)
    {
        return !empty($username) && strlen($username) >= Config::get("username_min_length") && strlen($username) <= Config::get("username_max_length");
    }

    /**
     * Validate filename.
     *
     * @param string $filename
     * @return bool
     */
    public static function filename($filename)
    {
        // TODO: Implement filename validation
        return true;
    }

    /**
     * Check if a key exists in an array.
     *
     * @param mixed $input
     * @param array $array
     * @return bool
     */
    public function required($input, $array)
    {
        return array_key_exists($input, $array);
    }

    /** PRIVATE METHODS */

    /**
     * Set error message.
     *
     * @param string $key
     * @param string $message
     * @return void
     */
    private function setError($key, $message)
    {
        $this->error = true;
        $this->errorMessages[$key]["message"][] = $message;
    }

    /**
     * Check if there's an error.
     *
     * @return mixed
     */
    private function hasError()
    {
        return $this->error;
    }
}
