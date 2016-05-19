<?php

class Validator {

    private $error;
    private $errorMessages;

    const RULES_SEPARATOR = "|";


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

    /** RULES **/

    public function string($input) {
        return is_string($input);
    }

    public function numeric($input) {
        return is_numeric($input);
    }

    public function email($input) {
        return is_string($input) && filter_var($input, FILTER_VALIDATE_EMAIL);
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