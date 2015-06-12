<?php
/**
 * Boostack: Notify.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
class Notify{
	 
	private $id;
	private $active;
	private $id_u_from;
	private $id_u_to;
	private $id_obj;
	private $code;
	private $join_date;
	private $visited;
	
	const TABLENAME = "boostack_notify";
		
	public function __construct($id=-1){		 
		if($id != -1){
			$this->id = htmlspecialchars($id);
			$sql = "SELECT * FROM ".self::TABLENAME." WHERE id ='".$id."' ";
			$fields2 = mysql_query($sql)or die (mysql_error().": $sql");
			$fields = mysql_fetch_array($fields2);	 

			$this->active = $fields["active"];
			$this->id_u_from = $fields["id_u_from"];
			$this->id_u_to = $fields["id_u_to"];
			$this->id_obj = $fields["id_obj"];
			$this->code = $fields["code"];
			$this->join_date = $fields["join_date"];
			$this->visited = $fields["visited"];
		}
	}
		
	public function insert($id_u_from,$id_u_to,$id_obj,$code) {
		
			$this->active = htmlspecialchars($active);
			$this->id_u_from = htmlspecialchars($id_u_from);
			$this->id_u_to = htmlspecialchars($id_u_to);
			$this->id_obj = htmlspecialchars($id_obj);
			$this->code = htmlspecialchars($code);
			$this->join_date = getDateTimeTimestamp(getDateTime());
			$c = ($this->code == 0)?"1":"0";
		
			$sql = "INSERT INTO ".self::TABLENAME." 
			VALUES
			(NULL,'1',".$this->id_u_from.",".$this->id_u_to.",'".$this->id_obj."','".$this->code."','".$this->join_date."','".$c."') ";
			$result = mysql_query($sql) or die ("QUERY: $sql <br /><br />".mysql_error()); 
			$this->id = mysql_insert_id();
			return true;
	}
	

	public function setVisited() {		
			$sql = "UPDATE ".self::TABLENAME." SET visited='1' WHERE id='".$this->id."'";
			$result = mysql_query($sql) or die ("QUERY: $sql <br /><br />".mysql_error()); 
			return true;
	}
	
	public function setAllVisitedByObjID() {		
			$sql = "UPDATE ".self::TABLENAME." SET visited='1' WHERE id_obj='".$this->id_obj."'";
			$result = mysql_query($sql) or die ("QUERY: $sql <br /><br />".mysql_error()); 
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
		#$sql = "UPDATE ".self::TABLENAME." SET $property_name='".$val."'  WHERE id ='".$this->id."' ";
		#mysql_query($sql)or die (mysql_error().": $sql");
    }

}

?>