<?php
/**
 * Boostack: Validator.Class.php
 * ========================================================================
 * Copyright 2014-2023 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.1
 */

class Validator
{

    /**
     * @var
     */
    private $error;
    /**
     * @var
     */
    private $errorMessages;

    /**
     *
     */
    const RULES_SEPARATOR = "|";
    /**
     *
     */
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
    public function validate($input, $rules)
    {
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
    public function validateFormValues($input, $rules)
    {
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

    public static function string($input)
    {
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

    /**
     * @param $input
     * @return int
     * validate strings containing only letters
     */
    public static function onlyChars($input){
        return preg_match('/^[A-Za-z]+$/', $input);
    }

    /**
     * @param $input
     * @return int
     * validate strings containing only letters and space
     */
    public static function onlyCharsWithSpace($input){
        return preg_match('/^[A-Za-zÀ-ÿ ]+$/', $input);
    }

    /**
     * @param $input
     * @return int
     * validate strings containing only letters, numbers or "-"
     */
    public static function onlyCharNumbersUnderscore($input){
        return preg_match('/^[A-Za-z1-9_]+$/', $input);
    }

    /**
     * @param $input
     * @return int
     * validate address like google maps default address or simply letters, numbers, accents, "-", "_", "," and space
     */
    public static function address ($input){
        return preg_match("/^[A-Za-z0-9À-ÿ _\-,]*[A-Za-z0-9À-ÿ][A-Za-z0-9À-ÿ _\-,]*$/", $input);
    }

    /**
     * @param $rule
     * @return bool
     * validate operators for view method
     */
    public static function operators($rule){
        $rules = ["like", "not like", "&lt;&gt;", "=", "&lt;", "&lt;=", "&gt;", "&gt;="];
        return in_array($rule , $rules);
    }

    /**
     * @param $input
     * @return bool
     */
    public static function varchar_max_length ($input){
        return strlen($input) < Config::get("varchar_max_length");
    }

    /**
     * @param $input
     * @return bool
     */
    public static function numeric($input)
    {
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

    /**
     * @param $input
     * @return bool|int
     */
    public static function alphanumeric($input)
    {
        $res = true;
        if(is_array($input)) {
            foreach($input as $elem) {
                if(!preg_match('/^[A-Za-z0-9 _]*[A-Za-z0-9_]+$/',$elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = preg_match('/^[A-Za-z0-9 _]*[A-Za-z0-9_]+$/',$input);
        }
        return $res;
    }

    /**
     * @param $input
     * @return bool|int
     */
    public static function integer($input)
    {
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

    /**
     * @param $input
     * @return bool|int
     */
    public static function float($input)
    {
        $res = true;
        if(is_array($input)) {
            foreach($input as $elem) {
                if(!preg_match('/^[-+]?(\d*[.])?\d+$/',$elem) && $res) {
                    $res = false;
                }
            }
        } else {
            $res = preg_match('/^[-+]?(\d*[.])?\d+$/',$input);
        }
        return $res;
    }

    /**
     * @param $elem
     * @param $array
     * @return bool
     */
    public static function in($elem, $array)
    {
        return in_array($elem,$array);
    }

    /**
     * @param $input
     * @return bool
     */
    public static function email($input)
    {
        return is_string($input) && filter_var($input, FILTER_VALIDATE_EMAIL);
    }

//    /**
//     * @param $email
//     * @return bool
//     */
//    public static function checkEmailFormat($email)
//    {
//        $regexp = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
//        if ($email == "" || !preg_match($regexp, $email) || strlen($email >= 255)) {
//            return false;
//        }
//        return true;
//    }

    /**
     * @param $input
     * @return bool
     */
    public static function phone($input)
    {
        // TODO find regex for phone numbers
        return true;
    }

    /**
     * @param $password
     * @return bool
     * validate password to login (when its length is lower than a strong password)
     */
    public static function password_login($password){
        return !empty($password) && (strlen(html_entity_decode($password, ENT_QUOTES)) >= Config::get("password_min_length")) && (strlen(html_entity_decode($password, ENT_QUOTES)) <= Config::get("password_max_length"));
    }

    /**
     * @param $password
     * @return bool
     */
    public static function password($password) {
        return !empty($password) && (strlen(html_entity_decode($password, ENT_QUOTES)) >= Config::get("password_min_length")) && (strlen(html_entity_decode($password, ENT_QUOTES)) <= Config::get("password_max_length"));
    }

    /**
     * @param $pwd
     * @return int
     */
    public static function strongPassword($password)
    {
        return preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password);
    }

    /**
     * @param $username
     * @return bool
     */
    public static function username($username)
    {
        return !empty($username) && (strlen($username) >= Config::get("username_min_length")) && (strlen($username) <= Config::get("username_max_length"));
    }

    /**
     * @param $filename
     * @return int
     */
    public static function filename($filename)
    {
        return true; // TODO find regex for filename
        // OWASP regex NOT WORK
        //$regex = '^(([a-zA-Z]:|\\)\\)?(((\.)|(\.\.)|([^\\/:*?"|<>. ](([^\\/:*?"|<>. ])|([^\\/:*?"|<>]*[^\\/:*?"|<>. ]))?))\\)*[^\\/:*?"|<>. ](([^\\/:*?"|<>. ])|([^\\/:*?"|<>]*[^\\/:*?"|<>. ]))?$';
        //$regex = '/^[\w-\d]{1}[\w-\d\s\.]*(\.){1}(\w)+$/i';
    }

    /**
     * @param $input
     * @param $array
     * @return bool
     */
    public function required($input, $array)
    {
        return array_key_exists($input,$array);
    }

    /** PRIVATE METHODS */

    private function setError($key,$message)
    {
        $this->error = true;
        $this->errorMessages[$key]["message"][] = $message;
    }

    /**
     * @return mixed
     */
    private function hasError()
    {
        return $this->error;
    }

}
?>