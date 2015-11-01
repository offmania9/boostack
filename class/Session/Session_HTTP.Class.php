<?php
/**
 * Boostack: Session_HTTP.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */

class Session_HTTP {
    private $php_session_id;
    private $native_session_id;
    private $dbhandle;
    private $logged_in;
    private $user_id;
    private $session_timeout = 600;      # 10 minute inactivity timeout
    private $session_lifespan = 3600;    # 1 hour session duration
    private $http_session_table = "boostack_http_session";
    private $session_variable = "boostack_session_variable";

    public function __construct($timeout=3600,$lifespan=4600) {
      $this->dbhandle = DBMySqlPDO::getInstance();
      $this->session_timeout = $timeout;
      $this->session_lifespan = $lifespan;

      $set_save_handler = session_set_save_handler(
          array($this, '_session_open_method'),
          array($this, '_session_close_method'),
          array($this, '_session_read_method'),
          array($this, '_session_write_method'),
          array($this, '_session_destroy_method'),
          array($this, '_session_gc_method') );


      if (isset($_COOKIE["PHPSESSID"])) {
       $this->php_session_id = sanitizeInput($_COOKIE["PHPSESSID"]);
       /*$datetime_now = time();
		$sql = "SELECT created,last_impression FROM ".$this->http_session_table."
		        WHERE ascii_session_id ='".$this->php_session_id."' ";
		$lease = $this->dbhandle->query($sql)->fetchAll(); debug($lease);
		$interval_created = $datetime_now - (int)$lease[0];
		$interval_last_impression = $datetime_now - (int)$lease[1];

       $stmt = "select id from ".$this->http_session_table."
           WHERE ascii_session_id = '".$this->php_session_id."'
           AND $interval_created < ".$this->session_lifespan."
           AND user_agent='".getUserAgent()."'
           AND $interval_last_impression <= ".$this->session_timeout."
           OR last_impression = 0
           ";#

       if ($this->dbhandle->query($stmt)->rowCount()==0) {
         $maxlifetime = $this->session_lifespan;
         $sql = 'DELETE * FROM ".$this->http_session_table." WHERE last_impression < $old';
		 $sql ="DELETE FROM ".$this->http_session_table." WHERE (ascii_session_id = '". $this->php_session_id . "') OR (time() - created > '$maxlifetime')";
         $result = $this->dbhandle->prepare($sql)->execute();
		 $sql ="DELETE FROM ".$this->session_variable." WHERE session_id NOT IN (SELECT id FROM ".$this->http_session_table.")";
         $result = $this->dbhandle->prepare($sql)->execute();
         unset($_COOKIE["PHPSESSID"]);
       }*/
      }

	  session_set_cookie_params($this->session_lifespan);
      if (!session_id())
		  session_start();
    }

    private function _session_open_method($save_path, $session_name) {
        return true;
    }

    public function _session_close_method() {
        #$this->dbhandle = null;
        return true;
    }

    public function _session_read_method($id) {
        $this->php_session_id = $id;
        $sql = "select id, logged_in, user_id from ".$this->http_session_table." where ascii_session_id = '$id'";
        $result = $this->dbhandle->query($sql);
        if ($result->rowCount() > 0 ) {
            $row = $result->fetch();
            $this->native_session_id = $row["id"];
            if ($row["logged_in"]=="t") {
                $this->logged_in = true;
                $this->user_id = $row["user_id"];
            }
            else {
                $this->logged_in = false;
            }
        }
        else {
            $this->logged_in = false;
            $sql="INSERT INTO ".$this->http_session_table."(id,ascii_session_id, logged_in,user_id, created, user_agent)
							VALUES (NULL,'$id','f',0,'".time()."','".getUserAgent()."')";
            $result = $this->dbhandle->prepare($sql)->execute();
            $sql = "select id from ".$this->http_session_table." where ascii_session_id = '$id'";
            $result = $this->dbhandle->query($sql);
            $row = $result->fetch();
            $this->native_session_id = $row["id"];
        }
        return("");
    }

    public function Impress() {
      if ($this->native_session_id) {
		$sql = "UPDATE ".$this->http_session_table." SET last_impression = '".time()."' WHERE id = '" . $this->native_session_id ."'";
        $result = $this->dbhandle->prepare($sql)->execute();
      }
    }

    public function IsLoggedIn() {
      return($this->logged_in);
    }

    public function GetUserID() {
      if ($this->logged_in) {
        return($this->user_id);
      }
      else {
        return(false);
      }
    }

    public function GetUserObject() {
      if ($this->logged_in) {
        if (class_exists("User")) {
          $objUser = new User($this->user_id);
          return($objUser);
        }
      }
      return null;
    }

    public function GetSessionIdentifier() {
      return($this->php_session_id);
    }

    public function Login($strUsername, $strPlainPassword, $hashed_psw="") {
	  if($hashed_psw !== "")
	  	$strMD5Password = $hashed_psw;
	  else
      	$strMD5Password = hash("sha512",$strPlainPassword);
		
      $stmt = "SELECT id FROM boostack_user WHERE username = '$strUsername'
	  AND pwd = '$strMD5Password' AND active='1'";
      $result = $this->dbhandle->prepare($stmt);
      if ($result->rowCount() > 0) {
        $row = $result->fetch();
        $this->user_id = $row["id"];
        $this->logged_in = true;
		$sql = "UPDATE ".$this->http_session_table." SET logged_in = 't', user_id = '".$this->user_id."' WHERE id='".$this->native_session_id."'";
        $result = $this->dbhandle->prepare($sql)->execute();
        $sql = "UPDATE boostack_user SET last_access='".time()."' where id='".$row["id"]."'";
        $result = $this->dbhandle->prepare($sql)->execute();
        return true;
      }
      else {
        return false ;
      }
    }

    public function LogOut() {
      if ($this->logged_in == true) {
		$sql = "UPDATE ".$this->http_session_table." SET logged_in = 'f', user_id = '0' WHERE id = " . $this->native_session_id;
        $result = $this->dbhandle->prepare($sql)->execute();
        $this->logged_in = false;
        $this->user_id = 0;
        return true;
      }
      else {
        return false;
      }
    }

    public function __get($nm) {
		$sql = "SELECT variable_value FROM ".$this->session_variable."
				WHERE session_id = '".$this->native_session_id."'
				AND variable_name ='".$nm."' ORDER BY id DESC";
      $result = $this->dbhandle->prepare($sql);
      if ($result->rowCount() > 0) {
        $row = $result->fetch();
        return(unserialize($row["variable_value"]));
      }
      else {
        return false;
      }
    }

    public function __set($nm, $val) {
      $strSer = serialize($val);
	  $this->native_session_id = ($this->native_session_id == "")?0:$this->native_session_id;
      $sql = "SELECT id FROM ".$this->session_variable."
				WHERE session_id = '".$this->native_session_id."' AND variable_name ='".$nm."'";
      $result = $this->dbhandle->query($sql);
      if ($result->rowCount() == 0)
        $sql = "INSERT INTO ".$this->session_variable."(session_id, variable_name, variable_value)
               VALUES(".$this->native_session_id.", '$nm', '$strSer')";
      else
        $sql = "UPDATE ".$this->session_variable." SET variable_value = '$strSer'
               WHERE session_id = '".$this->native_session_id."' AND variable_name ='".$nm."'";

        $result = $this->dbhandle->prepare($sql)->execute();
    }

    public function _session_write_method($id, $sess_data) {
      return true;
    }

    private function _session_destroy_method($id) {
      $sql = "DELETE FROM ".$this->http_session_table." WHERE ascii_session_id = '$id'" ;
      if($this->dbhandle->prepare($sql)->execute());
        return true;
      return false;
    }

    private function _session_gc_method($maxlifetime) {
        $old = time() - $maxlifetime;
        $sql = 'DELETE * FROM ".$this->http_session_table." WHERE last_impression < $old';
        if($this->dbhandle->prepare($sql)->execute())
            return true;
        return false;
    }
}
?>
