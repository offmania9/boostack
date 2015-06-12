<?php
/**
 * Boostack: DatabaseAccessLogger.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
class DatabaseAccessLogger{

  	private $username;
	private $ip;
	private $useragent;
	private $referrer;
	private $query;
	private $message;
	private $date;
	private $time;
	
	static private $instance = NULL;
  	const TABLENAME = "boostack_log";
  
  
  	private function __construct($db = NULL, $objSession) {
		if($db instanceof Database) {
			$this->_db = $db;
			$this->username = (!is_null($objSession))? $objSession->GetUserID(): "Anonymous";
			$this->ip = getenv('REMOTE_ADDR');
			$this->useragent = addslashes(htmlspecialchars(getenv('HTTP_USER_AGENT')));
			$this->referrer = addslashes(htmlspecialchars(getenv('HTTP_REFERER')));
			$this->query = addslashes(htmlspecialchars(getenv('REQUEST_URI')));
		}
		else throw new Exception(__METHOD__ . ' requires an object of type Database');
  	}

  	public function Log($message = NULL) {
		#if(!is_null($message) && $message != '') 
			$message = str_replace(array("\r\n","\n","\r"), "", $message);
			$message = addslashes($message);
			$this->query = str_replace(array("\r\n","\n","\r"), "", $this->query);
			$this->query = addslashes($this->query);
				
			$this->_db->Execute("INSERT INTO ".self::TABLENAME."  (id ,datetime , username, ip ,useragent ,referrer ,query ,message) 
			VALUES(NULL,'".time()."','".$this->username."','".$this->ip."','".$this->useragent."','".$this->useragent."','".$this->query."','".$message."')");
  	}

  	private function __clone(){}
  
	static function getInstance($db,$objSession = NULL){
		if(self::$instance == NULL)
			self::$instance = new DatabaseAccessLogger($db,$objSession);
		
		return self::$instance;
	}
	

  	public function get() {
			$sql = "SELECT * FROM ".self::TABLENAME." ORDER BY datetime DESC";
			$f = mysql_query($sql) or die(mysql_error());
			while($res = mysql_fetch_array($f)) 
				$res2[] = $res['datetime']." - ".$res['username']." - ".$res['message']." - ".$res['ip']." - ".substr($res['useragent'],0,10)." - ".$res['query'];
			
			return $res2;
  	}

}
?>