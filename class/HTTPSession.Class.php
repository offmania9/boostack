<?php
/**
 * Boostack: HTTPSession.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
include_once("class/DBMySqlDatabase.Class.php");
  
class HTTPSession {
    private $php_session_id;
    private $native_session_id;
    private $dbhandle;
    private $logged_in;
    private $user_id;
    private $session_timeout = 600;      # 10 minute inactivity timeout
    private $session_lifespan = 3600;    # 1 hour session duration

    public function __construct($timeout=3600,$lifespan=4600) {
      $this->session_timeout = $timeout;
      $this->session_lifespan = $lifespan;

      $set_save_handler = session_set_save_handler(
          array(&$this, '_session_open_method'),
          array(&$this, '_session_close_method'),
          array(&$this, '_session_read_method'),
          array(&$this, '_session_write_method'),
          array(&$this, '_session_destroy_method'),
          array(&$this, '_session_gc_method') );

      $strUserAgent = addslashes(htmlspecialchars($_SERVER["HTTP_USER_AGENT"]));
      if (isset($_COOKIE["PHPSESSID"])) {
       $this->php_session_id = addslashes(htmlspecialchars($_COOKIE["PHPSESSID"]));
       $datetime_now = $this->get_datetime_now();
		$sql = "SELECT created,last_impression
		FROM http_session WHERE ascii_session_id ='".$this->php_session_id."' ";
		$lease = mysql_query($sql)or die (mysql_error().": $sql");
		$lease2 = mysql_fetch_array($lease);
		$interval_created = strtotime($datetime_now)- strtotime($lease2[0]);
		$interval_last_impression = strtotime($datetime_now) - strtotime($lease2[1]);   

       $stmt = "select id from http_session 
           WHERE ascii_session_id = '".$this->php_session_id."'
           AND $interval_created < ".$this->session_lifespan."
           AND user_agent='".$strUserAgent."'
           AND $interval_last_impression <= ".$this->session_timeout."
           OR last_impression IS NULL";

       $result = mysql_query($stmt) or die (mysql_error().": $stmt");
       if (mysql_num_rows($result)==0) {
         $failed = 1;
         $maxlifetime = $this->session_lifespan;
		 $sql ="DELETE FROM http_session WHERE (ascii_session_id = '". $this->php_session_id . "') OR (now() - created > '$maxlifetime seconds')";
         $result = mysql_query($sql) or die (mysql_error().": $sql");
		 $sql ="DELETE FROM session_variable WHERE session_id NOT IN (SELECT id FROM http_session)";
         $result = mysql_query($sql) or die (mysql_error().": $sql");
         unset($_COOKIE["PHPSESSID"]);
       };
      };

	session_set_cookie_params($this->session_lifespan);
	    if (!session_id()) {
		  session_start();
		}
    }

    public function Impress() {
      if ($this->native_session_id) {
		$sql = "UPDATE http_session SET last_impression = '".$this->get_datetime_now()."' WHERE id = '" . $this->native_session_id ."'";
        $result = mysql_query($sql) or die (mysql_error().": $sql");
      };
    }

    public function IsLoggedIn() {
      return($this->logged_in);
    }

    public function IsFBSync() {
      $stmt = "select id FROM user WHERE id = '".$this->user_id."' AND oauth_uid = '-1'";
      $result = mysql_query($stmt) or die (mysql_error().": $stmt");
	  if (mysql_num_rows($result)>0)
	  	return false;
	  return true;
    }

    public function GetUserID() {
      if ($this->logged_in) {
        return($this->user_id);
      }
      else {
        return(false);
      };
    }

    public function GetUserObject() {
      if ($this->logged_in) {
        if (class_exists("user")) {
          $objUser = new User($this->user_id);
          return($objUser);
        }
        else {
          return(false);
        };
      };
    }

    public function GetSessionIdentifier() {
      return($this->php_session_id);
    }

    public function Login($strUsername, $strPlainPassword, $hashed_psw="") {
	  if($md5_psw !== "")
	  	$strMD5Password = $md5_psw;
	  else
      	$strMD5Password = hash("sha512",$strPlainPassword);
		
      $stmt = "SELECT id FROM user WHERE username = '$strUsername' 
	  AND pwd = '$strMD5Password' AND active='1'";
      $result = mysql_query($stmt) or die (mysql_error().": $stmt");
      if (mysql_num_rows($result)>0) {
        $row = mysql_fetch_array($result);
        $this->user_id = $row["id"];
        $this->logged_in = true;
		$sql = "UPDATE http_session SET logged_in = 't', user_id = '".$this->user_id."' WHERE id='".$this->native_session_id."'";
        $result = mysql_query($sql) or die (mysql_error().": $sql");
		mysql_query("UPDATE user SET last_access='".time()."' where id='".$row["id"]."'") or die ("error set access");				
        return(true);
      }
      else {
        return(false);
      };
    }

    public function LogOut() {
      if ($this->logged_in == true) {
		$sql = "UPDATE http_session SET logged_in = 'f', user_id = '0' WHERE id = " . $this->native_session_id;
        $result = mysql_query($sql) or die (mysql_error().": $sql");
        $this->logged_in = false;
        $this->user_id = 0;
        return(true);
      }
      else {
        return(false);
      };
    }

    public function __get($nm) {
		$sql = "SELECT variable_value FROM session_variable 
							WHERE session_id = '".$this->native_session_id."'
							AND variable_name ='".$nm."' ORDER BY id DESC"; # aggiunta SS: ORDER BY 'id' DESC"
      $result = mysql_query($sql) or die (mysql_error().": $sql");
      if (mysql_num_rows($result)>0) {
        $row = mysql_fetch_array($result);
        return(unserialize($row["variable_value"]));
      }
      else {
        return(false);
      }
    }

    public function __set($nm, $val) {
      $strSer = serialize($val);
	  #$this->native_session_id = $this->php_session_id;
	  $this->native_session_id = ($this->native_session_id == "")?0:$this->native_session_id;
      $stmt = "INSERT INTO session_variable(session_id, variable_name, variable_value)
               VALUES(".$this->native_session_id.", '$nm', '$strSer')";
      $result = mysql_query($stmt)  or die (mysql_error().":** $stmt");
    }

    private function _session_open_method($save_path, $session_name) {
      return(true);
    }

    public function _session_close_method() {
      #mysql_close($this->dbhandle);
      return(true);
    }

    public function _session_read_method($id) {
      $strUserAgent = addslashes(htmlspecialchars($_SERVER["HTTP_USER_AGENT"]));
      $this->php_session_id = $id;
      $failed = 1;
	  $sql = "select id, logged_in, user_id from http_session where ascii_session_id = '$id'";
      $result = mysql_query($sql) or die (mysql_error().": $sql");
      if (mysql_num_rows($result)>0) {
       $row = mysql_fetch_array($result);
       $this->native_session_id = $row["id"];
       if ($row["logged_in"]=="t") {
         $this->logged_in = true;
         $this->user_id = $row["user_id"];
       }
       else {
         $this->logged_in = false;
       };
      }
      else {
        $this->logged_in = false;
		$sql="INSERT INTO http_session(id,ascii_session_id, logged_in,user_id, created, user_agent) 
							VALUES (NULL,'$id','f',0,'".$this->get_datetime_now()."','$strUserAgent')";
        $result = mysql_query($sql)or die (mysql_error().": $sql");
		$sql = "select id from http_session where ascii_session_id = '$id'";
        $result = mysql_query($sql) or die (mysql_error().": $sql");
        $row = mysql_fetch_array($result);
        $this->native_session_id = $row["id"];
      };
      return("");
    }

    public function _session_write_method($id, $sess_data) {
      return(true);
    }

    private function _session_destroy_method($id) {
		$sql = "DELETE FROM http_session WHERE ascii_session_id = '$id'" ;
      $result = mysql_query($sql) or die (mysql_error().": $sql");
      return($result);
    }

    private function _session_gc_method($maxlifetime) {
      return(true);
    }

	private function get_datetime_now() {
      $date = mysql_query("SELECT CURDATE() as datas");
	  $data = mysql_fetch_array($date);
	  $times = mysql_query("SELECT CURTIME() as tempo");
	  $time = mysql_fetch_array($times);
	  $datetime_now = $data['datas']." ".$time['tempo'];
	  return $datetime_now;
    }
}
?>
