<?php
/**
 * Boostack: UserInfo.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
class UserInfo extends User{
	#private $id;#
	private $first_name;#
	private $last_name;
	#private $name;
	private $locale;
	private $city;
	private $state;#
	private $country;#
	private $zip;#
	private $about_me;#
	private $tel;#
	private $cell;#
	private $profession;#
	private $birthday;
	private $movies;
	private $music;
	private $political;
	private $interests;
	private $tv;
	private $religion;
	private $pic_big;
	private $sex;

	private $excluse_from_update = array("id");

	const TABLENAME = "user_info";

	public function __construct($id=-1){
		if($id != -1){
			parent::__construct($id);
			$sql = "SELECT * FROM ".self::TABLENAME." WHERE id ='".$id."' ";
			$fields2 = mysql_query($sql)or die (mysql_error().": $sql");
			$fields = mysql_fetch_array($fields2);	 

			//$this->id = $fields["id"];
			$this->first_name = $fields["first_name"];		
			$this->last_name = $fields["last_name"];		
			#$this->name = $fields["name"];	
			$this->locale = $fields["locale"];	
			$this->city = $fields["city"];	
			$this->state = $fields["state"];	
			$this->country = $fields["country"];	
			$this->zip = $fields["zip"];	
			$this->about_me = $fields["about_me"];	
			$this->tel = $fields["tel"];	
			$this->cell = $fields["cell"];
			$this->profession = $fields["profession"];	
			$this->birthday = $fields["birthday"];	
			$this->movies = $fields["movies"];	
			$this->music = $fields["music"];	
			$this->political = $fields["political"];	
			$this->interests = $fields["interests"];	
			$this->tv = $fields["tv"];	
			$this->religion = $fields["religion"];		
			$this->pic_big = $fields["pic_big"];			
			$this->sex = $fields["sex"];
		}
	}
    public function prepare($post_array) {
		global $default_profilepic;
			$fields["first_name"] = addslashes($post_array["first_name"]);
			$fields["last_name"] = addslashes($post_array["last_name"]);
			#$fields["name"] = addslashes($post_array["name"]);
			$fields["locale"] = (isset($post_array["locale"]))?$post_array["locale"]:"";
			$fields["city"] = (isset($post_array["city"]))?addslashes($post_array["city"]):"";
			$fields["state"] = (isset($post_array["state"]))?$post_array["state"]:"";
			$fields["country"] = (isset($post_array["country"]))?$post_array["country"]:"";
			$fields["zip"] = (isset($post_array["zip"]))?$post_array["zip"]:"";
			$fields["about_me"] = (isset($post_array["about_me"]))?$post_array["about_me"]:"";
			$fields["tel"] = (isset($post_array["tel"]))?$post_array["tel"]:"";
			$fields["cell"] = (isset($post_array["cell"]))?$post_array["cell"]:"";
			$fields["profession"] = (isset($post_array["profession"]))?$post_array["profession"]:"";
			$fields["birthday"] = (isset($post_array["birthday"]))?$post_array["birthday"]:"";
			$fields["movies"] = (isset($post_array["movies"]))?$post_array["movies"]:"";	
			$fields["music"] = (isset($post_array["music"]))?$post_array["music"]:"";	
			$fields["political"] = (isset($post_array["political"]))?$post_array["political"]:"";	
			$fields["interests"] = (isset($post_array["interests"]))?$post_array["interests"]:"";	
			$fields["tv"] = (isset($post_array["tv"]))?$post_array["tv"]:"";	
			$fields["religion"] = (isset($post_array["religion"]))?$post_array["religion"]:"";		
			$fields["pic_big"] = (!isset($post_array["pic_big"]))?$default_profilepic:$post_array["pic_big"];
			$fields["sex"] = (isset($post_array["sex"]))?$post_array["sex"]:"";
			
		foreach($fields as $key => $value)
			$this->$key = $value;  #OBJECT UPDATE
		
		return $fields;
	}
	
    public function insert($post_array) {
		parent::insert($post_array);
		
		$fields = self::prepare($post_array);
		
		$sql_1 = "INSERT INTO ".self::TABLENAME." (id";
	    $sql_2 = "VALUES('".parent::__get("id")."'";
		foreach($fields as $key => $value){
			if($key == "id")
				continue;
			$sql_1 .= ",$key";
			$sql_2 .= ",'$value'";
			#$this->$key = $value;  #OBJECT UPDATE
		}
		$sql_1 .= ") ";
		$sql_2 .= ")";
		
		$sql = $sql_1.$sql_2;
		mysql_query($sql) or die ("QUERY: $sql <br /><br />".mysql_error());
		#$this->id = mysql_insert_id();
		return true;	
	}

	public function update($post_array, $excluse=NULL){
		
		$fields = self::prepare($post_array);
		$sql = "UPDATE ".self::TABLENAME." SET ";
		foreach($fields as $key => $value){
			if(in_array($key, $this->excluse_from_update))# || in_array($key, $excluse)
				continue;
			$sql .= "$key='".$value."',";
			#$this->$key = $value;  #OBJECT UPDATE
		}
		$sql = substr($sql, 0, -1);
		$sql .= " WHERE id='".$this->id."'"; 
		mysql_query($sql) or die ("QUERY: $sql <br /><br />".mysql_error());
		return true;
	}
	
	public function __get($property_name) {
	  if(isset($this->$property_name)) { 
		  return($this->$property_name);
		} else {
		  return(parent::__get($property_name));
		} 
    }

    public function __set($property_name, $val) {
		if(isset($this->$property_name)) { 
			$this->$property_name = $val;
			$sql = "UPDATE ".self::TABLENAME." SET $property_name='".$val."'  WHERE id ='".$this->id."' ";
			mysql_query($sql)or die (mysql_error().": $sql");
		}
		else
			parent::__set($property_name, $val);
    }
}
?>