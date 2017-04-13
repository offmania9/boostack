<?php

/**
 * Boostack: Session_HTTP.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Session_HTTP
{

    /**
     * @var array|string
     */
    private $php_session_id;

    /**
     * @var
     */
    private $native_session_id;

    /**
     * @var null|PDO
     */
    private $dbhandle;

    /**
     * @var
     */
    private $logged_in;

    /**
     * @var
     */
    private $user_id;

    /**
     * @var int
     */
    private $session_timeout = 0;

    /**
     * @var int
     */
    private $session_lifespan = 0;

    /**
     * @var string
     */
    private $http_session_table = "boostack_http_session";

    /**
     * @var string
     */
    private $session_variable = "boostack_session_variable";

    /**
     * Session_HTTP constructor.
     * @param int $timeout
     * @param int $lifespan
     */
    public function __construct($timeout = 3600, $lifespan = 4600)
    {
        $this->dbhandle = Database_PDO::getInstance();
        $this->session_timeout = $timeout;
        $this->session_lifespan = $lifespan;
        
        $set_save_handler = session_set_save_handler(array(
            $this,
            '_session_open_method'
        ), array(
            $this,
            '_session_close_method'
        ), array(
            $this,
            '_session_read_method'
        ), array(
            $this,
            '_session_write_method'
        ), array(
            $this,
            '_session_destroy_method'
        ), array(
            $this,
            '_session_gc_method'
        ));
        
        if (isset($_COOKIE["PHPSESSID"])) {
            if(strlen(Utils::sanitizeInput($_COOKIE["PHPSESSID"])) <= 32){
                $this->php_session_id = Utils::sanitizeInput($_COOKIE["PHPSESSID"]);
            }
            else {
                unset($_COOKIE["PHPSESSID"]);
            }
        }
        $datetime_now = time();
        $sql = "SELECT created,last_impression FROM " . $this->http_session_table . "
              WHERE ascii_session_id ='" . $this->php_session_id . "' ";
        $lease = $this->dbhandle->query($sql)->fetch();
        $interval_created = $datetime_now - intval($lease[0]);
        $interval_last_impression = $datetime_now - intval($lease[1]);
        
        $stmt = "select id from " . $this->http_session_table . "
              WHERE ascii_session_id = '" . $this->php_session_id . "'
                      AND $interval_created < " . $this->session_lifespan . "
              AND user_agent='" . Utils::getUserAgent() . "'
                      AND $interval_last_impression <= " . $this->session_timeout . "
              OR last_impression = 0
              ";
        if ($this->dbhandle->query($stmt)->rowCount() == 0) {
            $maxlifetime = $this->session_lifespan;
            $sql = "DELETE FROM " . $this->http_session_table . "
                         WHERE (ascii_session_id = '" . $this->php_session_id . "') OR ($datetime_now - created > '$maxlifetime')";
            $result = $this->dbhandle->prepare($sql);
            $result->execute();
            $sql = "DELETE FROM " . $this->session_variable . " WHERE session_id NOT IN (SELECT id FROM " . $this->http_session_table . ")";
            $result = $this->dbhandle->prepare($sql);
            $result->execute();
            unset($_COOKIE["PHPSESSID"]);
        }
        
        session_set_cookie_params($this->session_lifespan);
        if (! session_id())
            session_start();

        $this->Impress();
    }

    /**
     * @param $save_path
     * @param $session_name
     * @return bool
     */
    private function _session_open_method($save_path, $session_name)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function _session_close_method()
    {
        $this->dbhandle = NULL;
        return true;
    }

    /**
     * @param $id
     * @return string
     */
    public function _session_read_method($id)
    {
        $this->php_session_id = $id;
        $sql = "select id, logged_in, user_id from " . $this->http_session_table . " where ascii_session_id = '$id'";
        $result = $this->dbhandle->prepare($sql);
        $result->execute();
        if ($result->rowCount() > 0) {
            $row = $result->fetch();
            $this->native_session_id = $row["id"];
            if ($row["logged_in"] == "t") {
                $this->logged_in = true;
                $this->user_id = $row["user_id"];
            } else {
                $this->logged_in = false;
            }
        } else {
            $this->logged_in = false;
            $sql = "INSERT INTO " . $this->http_session_table . "(id,ascii_session_id, logged_in,user_id, created, user_agent)
							VALUES (NULL,'$id','f',1,'" . time() . "','" . Utils::getUserAgent() . "')";
            $result = $this->dbhandle->prepare($sql);
            $result->execute();
            $sql = "select id from " . $this->http_session_table . " where ascii_session_id = '$id'";
            $q = $this->dbhandle->prepare($sql);
            $q->execute();
            $row = $q->fetch();
            $this->native_session_id = $row["id"];
        }
        return("");
    }

    /**
     *
     */
    public function Impress()
    {
        if ($this->native_session_id) {
            $sql = "UPDATE " . $this->http_session_table . " SET last_impression = '" . time() . "' WHERE id = '" . $this->native_session_id . "'";
            $result = $this->dbhandle->prepare($sql)->execute();
        }
    }

    /**
     * @return mixed
     */
    public function IsLoggedIn()
    {
        return ($this->logged_in);
    }

    /**
     * @return bool
     */
    public function GetUserID()
    {
        if ($this->logged_in) {
            return ($this->user_id);
        } else {
            return (false);
        }
    }

    /**
     * @return null|User
     */
    public function GetUserObject()
    {
        if ($this->logged_in) {
            if (class_exists("User_Entity")) {
                $objUser = new User($this->user_id);
                return ($objUser);
            }
        }
        return NULL;
    }

    /**
     * @return array|string
     */
    public function GetSessionIdentifier()
    {
        return ($this->php_session_id);
    }

    /**
     * @return bool
     */
    public function logoutUser() {
        $sql = "UPDATE " . $this->http_session_table . " SET logged_in = 'f', user_id = '1' WHERE id = " . $this->native_session_id;
        $result = $this->dbhandle->prepare($sql);
        $result->execute();
        $this->logged_in = false;
        $this->user_id = 0;
        return true;
    }

    /**
     * @param $userID
     */
    public function loginUser($userID) {
        $this->user_id = $userID;
        $this->logged_in = true;
        $sql = "UPDATE " . $this->http_session_table . " SET logged_in = 't', user_id = '" . $this->user_id . "' WHERE id='" . $this->native_session_id . "'";
        $result = $this->dbhandle->prepare($sql);
        $result->execute();
    }

    /**
     * @param $nm
     * @return mixed|string
     */
    public function __get($nm)
    {
        $sql = "SELECT variable_value FROM " . $this->session_variable . "
				WHERE session_id = '" . $this->native_session_id . "'
				AND variable_name ='" . $nm . "' ORDER BY id DESC";
        $result = $this->dbhandle->prepare($sql);
        $result->execute();
        if ($result->rowCount() > 0) {
            $row = $result->fetch();
            return (unserialize($row["variable_value"]));
        } else {
            return "";
        }
    }

    /**
     * @param $nm
     * @param $val
     */
    public function __set($nm, $val)
    {
        $strSer = serialize($val);
        $this->native_session_id = ($this->native_session_id == "") ? 0 : $this->native_session_id;
        $sql = "SELECT id FROM " . $this->session_variable . "
				WHERE session_id = '" . $this->native_session_id . "' AND variable_name ='" . $nm . "'";
        $result = $this->dbhandle->prepare($sql);
        $result->execute();
        if ($result->rowCount() == 0)
            $sql = "INSERT INTO " . $this->session_variable . "(session_id, variable_name, variable_value)
               VALUES(" . $this->native_session_id . ", '$nm', '$strSer')";
        else
            $sql = "UPDATE " . $this->session_variable . " SET variable_value = '$strSer'
               WHERE session_id = '" . $this->native_session_id . "' AND variable_name ='" . $nm . "'";
        $result = $this->dbhandle->prepare($sql);
        $result->execute();
    }

    /**
     * @param $id
     * @param $sess_data
     * @return bool
     */
    public function _session_write_method($id, $sess_data)
    {
        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    private function _session_destroy_method($id)
    {
        $sql = "DELETE FROM " . $this->http_session_table . " WHERE ascii_session_id = '$id'";
        if ($this->dbhandle->prepare($sql)->execute())
            return true;
        return false;
    }

    /**
     * Metodo invocato automaticamente in modo sincrono dal Garbage Collector di PHP
     * @param $maxlifetime autoiniettato dalle config di PHP
     * @return bool
     */
    private function _session_gc_method($maxlifetime)
    {
        return true;
//        $old = time() - $maxlifetime;
//        /* Cancella tutte le sessioni scadute tranne quella corrente */
//        $sql = 'DELETE FROM ' . $this->http_session_table . ' WHERE last_impression < '.$old.' AND id <> '.$this->native_session_id;
//        $result = $this->dbhandle->prepare($sql);
//        if ($result->execute())
//            return true;
//        return false;
    }
}
?>