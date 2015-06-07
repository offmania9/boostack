<?php
/**
 * Boostack: User.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
class User{
	 
	private $id;
	private $active;
	private $privilege;
	private $name;
	private $username;
	private $pwd;
	private $email;
	private $last_access;
	private $session_cookie;
	private $pic_square;

	private $excluse_from_update = array("id", "active","username","email","last_access","session_cookie" );

	const TABLENAME = "user";

	public function __construct($id=-1,$init=true){
		if($id != -1){
			if($init){
				$sql = "SELECT * FROM ".self::TABLENAME." WHERE id ='".$id."' ";
				$fields2 = mysql_query($sql)or die (mysql_error().": $sql");
				$fields = mysql_fetch_array($fields2);	 
				$this->id = $fields["id"];
				$this->active = $fields["active"];
				$this->privilege = $fields["privilege"];
				$this->name = $fields["name"];
				$this->username = $fields["username"];				
				$this->pwd = $fields["pwd"];	
				$this->email = $fields["email"];	
				$this->last_access = $fields["last_access"];
				$this->session_cookie = $fields["session_cookie"];
				$this->pic_square = $fields["pic_square"];
			}
			else{
				$sql = "SELECT id FROM ".self::TABLENAME." WHERE id ='".$id."' ";
				$fields2 = mysql_query($sql)or die (mysql_error().": $sql");
				$fields = mysql_fetch_array($fields2);	 
				$this->id = $fields["id"];				
			}
		}

	}
    public function prepare($post_array) {
		global $default_profilepic;
		$fields["active"] = (!isset($post_array["active"]))? "0" : $post_array["active"];
		$fields["privilege"] = ($post_array["privilege"] == "")? "0" : $post_array["privilege"];
		$fields["name"] = ($post_array["name"] == "")? "" : $post_array["name"];
		$fields["username"] = $post_array["username"];
		if($post_array["pth"] == "")
			$this->excluse_from_update[] = "pwd";
		else
			$fields["pwd"] = hash("sha512",$post_array["pth"]);
			
		$fields["email"] = $post_array["email"];
		$fields["last_access"] = "0";
		$fields["session_cookie"] = (!isset($post_array["session_cookie"]))? "" : $post_array["session_cookie"];
		$fields["pic_square"] = ($post_array["pic_square"] == "")?$default_profilepic:$post_array["pic_square"];
						
		foreach($fields as $key => $value)
			$this->$key = $value; #OBJECT UPDATE
		
		return $fields;
	}
	
    public function insert($post_array) {
		$fields = self::prepare($post_array);
		
		$sql_1 = "INSERT INTO ".self::TABLENAME." (id";
	    $sql_2 = "VALUES(NULL";
		foreach($fields as $key => $value){
			if($key == "id")
				continue;
			$sql_1 .= ",$key";
			$sql_2 .= ",'$value'";
			#$this->$key = $value; #OBJECT UPDATE
		}
		$sql_1 .= ") ";
		$sql_2 .= ")";
		
		$sql = $sql_1.$sql_2;
		mysql_query($sql) or die ("QUERY: $sql <br /><br />".mysql_error());
		$this->id = mysql_insert_id();
		return true;	
	}

	public function update($post_array, $excluse=NULL){
		
		$fields = self::prepare($post_array);
		$sql = "UPDATE ".self::TABLENAME." SET ";
		foreach($fields as $key => $value){
			if(in_array($key, $this->excluse_from_update) || in_array($key, $excluse))
				continue;
			$sql .= "$key='".$value."',";
			#$this->$key = $value; #OBJECT UPDATE
		}
		$sql = substr($sql, 0, -1);
		$sql .= " WHERE id='".$this->id."'"; 
		mysql_query($sql) or die ("QUERY: $sql <br /><br />".mysql_error());
		return true;
	}
	
	public function delete(){
		$sql = "DELETE FROM ".self::TABLENAME." WHERE id='".$this->id."'";  
		$resurce = mysql_query($sql) or die ("QUERY: $sql <br /><br />".mysql_error());  
		if(mysql_affected_rows() == 0)
			return false;
		return true;
	}
				
	public function __get($property_name) {
	  if(isset($this->$property_name)) { 
		  return($this->$property_name);
		} else {
		  return(NULL);
		} 
    }
								
    public function __set($property_name, $val) {
		$this->$property_name = $val;
		$sql = "UPDATE ".self::TABLENAME." SET $property_name='".$val."'  WHERE id ='".$this->id."' ";
		mysql_query($sql)or die (mysql_error().": $sql");
    }
														
	public function isRegisterbyUsername($username) {
		$sql = "SELECT id FROM ".self::TABLENAME." WHERE username ='".$username."' ";
		$q = mysql_query($sql)or die (mysql_error().": $sql");
		$q2 = mysql_fetch_array($q);
		return (mysql_num_rows($q) == 0)?NULL:$q2[0];
    }
	
	public function isUsernameRegistered($username) {
		$sql = "SELECT id FROM ".self::TABLENAME." WHERE username ='".$username."' ";
		$q = mysql_query($sql)or die (mysql_error().": $sql");
		$q2 = mysql_fetch_array($q);
		return (mysql_num_rows($q) == 0)?NULL:$q2[0];
    }
	
	public function getUsernameByEmail($email) {
		$sql = "SELECT username FROM ".self::TABLENAME." WHERE email ='".$email."' ";
		$q = mysql_query($sql)or die (mysql_error().": $sql");
		$q2 = mysql_fetch_array($q);
		return (mysql_num_rows($q) == 0)?NULL:$q2[0];
    }	
}
?>