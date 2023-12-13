<?php
/**
 * Boostack: FieldRule.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
class FieldRule {

    private $field;
    private $required;

    public function __construct($name, $type) {
        if(!FieldType::isValidValue($type))
            throw new Exception("error: wrong field type"); 
        $this->field = new Field($name, $type);
        $this->field->rules["name"] = $name;
        $this->field->rules["type"] = $type;
        $this->field->rules["required"] = false;
    }

    public function get() {
        if($this->required)
            $this->addRule("required",true);
        return $this->field->rules; 
    }

    public function getString() {
        $a = $this->get();
        $res = "";
        foreach($a as $key => $value){
            $res .= $key.":".$value."|";
        }
        return substr($res,0,-1); 
    }

    public function required() {
        $this->required = true;
        return $this;
    }

    public function title($str) {
        $this->addRule("title",$str);
        return $this;
    }

    public function placeholder($str) {
        $this->addRule("placeholder",$str);
        return $this;
    }

    public function regex($str) {
        $this->addRule("regex",$str);
        return $this;
    }

    public function defaultValue($val) {
        $this->addRule("defaultValue",$val);
        return $this;
    }

    public function options(array $val) {
        $this->constraint(array(FieldType::COMBO));
        $this->addRule("options",$val);
        return $this;
    }

    public function description($str) {
        $this->addRule("description",$str);
        return $this;
    }

    public function max($upperbound) {
        $this->constraint(array(FieldType::INTEGER,FieldType::FLOAT,FieldType::NUMERIC));
        $this->addRule("max",$upperbound);
        return $this;
    }

    public function min($lowerbound) {
        $this->constraint(array(FieldType::INTEGER,FieldType::FLOAT,FieldType::NUMERIC));
        $this->addRule("min",$lowerbound);
        return $this;
    }

    public function min_length($min_length) {
        $this->constraint(array(FieldType::STRING,FieldType::TEXT,FieldType::EMAIL,FieldType::USERNAME,FieldType::PASSWORD));
        $this->addRule("min_length",$min_length);
        return $this;
    }

    public function max_length($max_length) {
        $this->constraint(array(FieldType::STRING,FieldType::TEXT,FieldType::EMAIL,FieldType::USERNAME,FieldType::PASSWORD));
        $this->addRule("max_length",$max_length);
        return $this;
    }

    public function from(DateTime $d) {
        $this->addRule("from",$d->format(Config::get("default_datetime_format")));
        return $this;
    }

    public function to(DateTime $d) {
        $this->constraint(array(FieldType::DATE));
        $this->addRule("to",$d->format(Config::get("default_datetime_format")));
        return $this;
    }

    private function constraint(array $types) {
        if(!in_array($this->field->type,$types)){
            $ex = new Exception();
            $trace = $ex->getTrace();
            $final_call = $trace[1]["function"];
            throw new Exception("error: you cannot call method '".$final_call."' on '".$this->field->type."' field type"); 
        }
            
    }

    private function addRule($name,$value) {
        $this->field->rules[$name]= $value;
    }

    private function concat() {
        return "|";
    }

}
?>