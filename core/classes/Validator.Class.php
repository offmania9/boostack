<?php
/**
 * Boostack: Validator.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Validator {

    private $error;
    private $errorMessages;

    const RULES_SEPARATOR = "|";
    const INTRA_RULES_SEPARATOR = ":";

    /**
     * Valida un array associativo di valori
     *
     * @param $input : array associativo in cui le chiavi corrispondono ai nomi dei valori in input,
     * ex. $elem = ['name' => 'Foo', 'age' => 42, 'email' => 'foo@bar.com' ]
     *
     * @param $rules : array associativo in cui le chiavi corrispondono ai nomi dei campi da validare,
     * mentre i valori corrispondono alle regole di validazione separate dal carattere '|'
     * ex. $rules = ['name' => 'string', 'age' => 'numeric', 'email' => 'email']
     *
     * @return array|bool : ritorna true in caso di successo, altrimenti ritorna un array contenente
     * i campi che non hanno superato la validazione
     */
    public function validate($input, $rules) {
        $this->error = false;
        $this->errorMessages = array();

        foreach($rules as $key => $value) {
            $value = trim($value,self::RULES_SEPARATOR);
            $elemRules = explode(self::RULES_SEPARATOR,$value);

            foreach ($elemRules as $rule) {
                if($rule == "required") {
                    if(!$this->$rule($key,$input)) {
                        $this->setError($key,$rule);
                    }
                } else {
                    if(isset($input[$key])) {
                        $valToValidate = $input[$key];
                        if(!$this->$rule($valToValidate)) {
                            $this->setError($key,$rule);
                        }
                    }
                }
            }
        }

        if($this->hasError()) {
            return ["error" => true, "messages" => $this->errorMessages];
        }
        return true;
    }

    /**
     *  --- VALIDAZIONE ---
     *
     *  ESEMPIO DATI CHE ARRIVANO DA FRONTEND
     *
     *  $array [
     *    12 => ["values" => ["Alessio"]]
     *    3 => ["values" => ["35"]]
     *    4 => ["values" => ["12,0000"]]
     *    6 => ["values" => ["Yes"]]
     *    22 => ["values" => ["Check2","Check3"]]
     *  ]
     *
     *  ESEMPIO DATI DI VALIDAZIONE
     *
     *  $array [
     *      12  => ["Required|String"]
     *      3   => ["Required|Integer"]
     *      4   => ["Required|Float"]
     *      6   => ["Required"]
     *      22   => ["Required|Min:1|Max:3"]
     *      35  => ["Required"]
     *  ]
     *
     *  REGOLE
     *
     *  Required: isset($array[ID]) && count(values) > 0 && values[0] non vuoto (!empty())
     *  String: ogni elemento di values $validate->string()
     *  Integer: ogni elemento di values $validate->integer()
     *  Float: ogni elemento di values $validate->float()
     *  Min: count(values) > min
     *  Max: count(values) < max
     *  In: ogni elemento di values $validate->valueIn()
     *
     */
    public function validateFormValues($input, $rules) {
        $this->error = false;
        $this->errorMessages = array();

        foreach($rules as $key => $value) {
            $elemRules = explode(self::RULES_SEPARATOR,trim($value,self::RULES_SEPARATOR));

            foreach ($elemRules as $rule) {
                $fullRule = explode(self::INTRA_RULES_SEPARATOR,$rule);
                $rule_1 = $fullRule[0];
                $rule_2 = count($fullRule) > 1 ? $fullRule[1] : null;

                switch ($rule_1) {
                    case "required":
                        if(!($this->required($key,$input) && count($input[$key]["values"]) && !empty($input[$key]["values"][0]))) {
                            $this->setError($key,$rule_1);
                        }
                        break;
                    case "string":
                        if(isset($input[$key]) && count($input[$key]["values"]) > 0) {
                            if(!$this->string($input[$key]["values"])) {
                                $this->setError($key,$rule_1);
                            }
                        }
                        break;
                    case "integer":
                        if(isset($input[$key]) && count($input[$key]["values"]) > 0) {
                            if(!$this->integer($input[$key]["values"])) {
                                $this->setError($key,$rule_1);
                            }
                        }
                        break;
                    case "float":
                        if(isset($input[$key]) && count($input[$key]["values"]) > 0) {
                            if(!$this->float($input[$key]["values"])) {
                                $this->setError($key,$rule_1);
                            }
                        }
                        break;
                    case "min":
                        if(isset($input[$key])) {
                            if(empty($rule_2) || count($input[$key]["values"]) < $rule_2 ) {
                                $this->setError($key,$rule);
                            }
                        }
                        break;
                    case "max":
                        if(isset($input[$key])) {
                            if(empty($rule_2) || count($input[$key]["values"]) > $rule_2 ) {
                                $this->setError($key,$rule);
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

        if($this->hasError()) {
            return ["error" => true, "messages" => $this->errorMessages];
        }
        return true;
    }

    /** Alternativamente le funzioni di validazione possono essere usate singolarmente
     *  ex. $validator = new Validator();
     *      $validator->email("foo@bar.it");
     *  Ritornano true/false
     **/

    public static function string($input) {
        $res = true;
        if(is_array($input)) {
            foreach($input as $elem) {
                if(!is_string($elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = is_string($input);
        }
        return $res;
    }

    public static function numeric($input) {
        $res = true;
        if(is_array($input)) {
            foreach($input as $elem) {
                if(!is_numeric($elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = is_numeric($input);
        }
        return $res;
    }

    public static function alphanumeric($input) {
        $res = true;
        if(is_array($input)) {
            foreach($input as $elem) {
                if(!preg_match('/^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]+$/',$elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = preg_match('/^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]+$/',$input);
        }
        return $res;
    }

    public static function integer($input) {
        $res = true;
        if(is_array($input)) {
            foreach($input as $elem) {
                if(!preg_match('/^\d+$/',$elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = preg_match('/^\d+$/',$input);
        }
        return $res;
    }

    public static function float($input) {
        $res = true;
        if(is_array($input)) {
            foreach($input as $elem) {
                if(!preg_match('[-+]?(\d*[.])?\d+',$elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = preg_match('[-+]?(\d*[.])?\d+',$input);
        }
        return $res;
    }

    public static function in($elem,$array) {
        return in_array($elem,$array);
    }

    public static function email($input) {
        return is_string($input) && filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    public static function phone($input) {
        // TODO find regex for phone numbers
        return true;
    }

    public static function password($password) {
        return !empty($password) && (strlen($password) >= Config::get("password_min_length")) && (strlen($password) <= Config::get("password_max_length"));
    }

    public static function username($username) {
        return !empty($username) && (strlen($username) >= Config::get("username_min_length")) && (strlen($username) <= Config::get("username_max_length"));
    }

    public function required($input,$array) {
        return array_key_exists($input,$array);
    }

    /** PRIVATE METHODS */

    private function setError($key,$message) {
        $this->error = true;
        $this->errorMessages[$key]["message"][] = $message;
    }

    private function hasError() {
        return $this->error;
    }

}
?>