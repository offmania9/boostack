<?php

/**
 * Boostack: Session_HTTP.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.1
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
     * @param int $timeout (seconds of inactivity before session expiration)
     * @param int $lifespan (seconds from creation before session expiration)
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
            # check if session cookie is too long (manually edited)
            if(strlen(Utils::sanitizeInput($_COOKIE["PHPSESSID"])) <= 32){
                $this->php_session_id = Utils::sanitizeInput($_COOKIE["PHPSESSID"]);
            } else {
                unset($_COOKIE["PHPSESSID"]);
            }
        }

        # retrieve creation date and last impression of current session
        $sql = "SELECT created,last_impression FROM " . $this->http_session_table . " WHERE ascii_session_id ='" . $this->php_session_id . "' ";
        $lease = $this->dbhandle->query($sql)->fetch();
        $datetime_now = time();
        $interval_created = $datetime_now - intval($lease[0]);
        $interval_last_impression = $datetime_now - intval($lease[1]);

        # check if current session is not expired (creation interval < lifespan and last impression interval < timeout or never impressed)
        $stmt = "SELECT id FROM " . $this->http_session_table . " WHERE ascii_session_id = :sessionId AND user_agent = :userAgent
                AND :intervalCreated < :sessionLifespan AND (:intervalLastImpression <= :sessionTimeout OR last_impression = 0) ";
        $q = $this->dbhandle->prepare($stmt);
        $q->bindValue(':sessionId', $this->php_session_id);
        $q->bindValue(':userAgent', Utils::getUserAgent());
        $q->bindValue(':intervalCreated', $interval_created, PDO::PARAM_INT);
        $q->bindValue(':sessionLifespan', $this->session_lifespan, PDO::PARAM_INT);
        $q->bindValue(':sessionTimeout', $this->session_timeout, PDO::PARAM_INT);
        $q->bindValue(':intervalLastImpression', $interval_last_impression, PDO::PARAM_INT);
        $q->execute();
        # if session is expired
        if ($q->rowCount() == 0) {
            # delete current session and all other expired sessions
            $sql = "DELETE FROM " . $this->http_session_table . " WHERE (ascii_session_id = '" . $this->php_session_id . "') OR ($datetime_now - created > '$this->session_lifespan')";
            $this->dbhandle->query($sql);
            # delete all session variable linked with expired sessions
            $sql = "DELETE FROM " . $this->session_variable . " WHERE session_id NOT IN (SELECT id FROM " . $this->http_session_table . ")";
            $this->dbhandle->query($sql);
            unset($_COOKIE["PHPSESSID"]);
        }
        
        session_set_cookie_params($this->session_lifespan);
        if (!session_id())
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
     * Invoked when session_start() is called
     * @param $id
     * @return string
     */
    public function _session_read_method($id)
    {
        $this->php_session_id = $id;

        # retrieve current session data
        $sql = "SELECT id, logged_in, user_id FROM " . $this->http_session_table . " WHERE ascii_session_id = '$id'";
        $results = $this->dbhandle->query($sql);

        # if current session exist
        if ($results->rowCount() > 0) {
            $row = $results->fetch();
            $this->native_session_id = $row["id"];
            if ($row["logged_in"] == "t") {
                $this->logged_in = true;
                $this->user_id = $row["user_id"];
            } else {
                $this->logged_in = false;
            }
        } else {
            $this->logged_in = false;
            # create session record into database
            $sql = "INSERT INTO " . $this->http_session_table . "(id, ascii_session_id, logged_in, user_id, created, user_agent)
							VALUES (NULL, :sessionId, :loggedIn, :userId, :createdAt, :userAgent)";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':sessionId', $id);
            $q->bindValue(':loggedIn', "f");
            $q->bindValue(':userId', 1);
            $q->bindValue(':createdAt', time());
            $q->bindValue(':userAgent', Utils::getUserAgent());
            $q->execute();
            # retrieve session id
            $sql = "SELECT id FROM " . $this->http_session_table . " WHERE ascii_session_id = '$id'";
            $results = $this->dbhandle->query($sql)->fetch();
            $this->native_session_id = $results["id"];
        }
        return "";
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
        $userClass = Config::get("use_custom_user_class") ? Config::get("custom_user_class") : User::class;
        if ($this->logged_in) {
            if (class_exists($userClass)) {
                $objUser = new $userClass($this->user_id);
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
    public function logoutUser()
    {
        $sql = "UPDATE " . $this->http_session_table . " SET logged_in = 'f', user_id = '1' WHERE id = " . $this->native_session_id;
        $result = $this->dbhandle->query($sql);
        $this->logged_in = false;
        $this->user_id = 0;
        return true;
    }

    /**
     * @param $userID
     */
    public function loginUser($userID)
    {
        $this->user_id = $userID;
        $this->logged_in = true;
        $sql = "UPDATE " . $this->http_session_table . " SET logged_in = 't', user_id = '" . $this->user_id . "' WHERE id='" . $this->native_session_id . "'";
        $result = $this->dbhandle->query($sql);
    }

    /**
     * @param $nm
     * @return mixed|string
     */
    public function __get($nm)
    {
        $sql = "SELECT variable_value FROM " . $this->session_variable . " WHERE session_id = '" . $this->native_session_id . "' AND variable_name ='" . $nm . "' ORDER BY id DESC";
        $results = $this->dbhandle->query($sql);
        if ($results->rowCount() > 0) {
            $row = $results->fetch();
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
        $sql = "SELECT id FROM " . $this->session_variable . " WHERE session_id = '" . $this->native_session_id . "' AND variable_name ='" . $nm . "'";
        $result = $this->dbhandle->query($sql);
        if ($result->rowCount() == 0)
            $sql = "INSERT INTO " . $this->session_variable . "(session_id, variable_name, variable_value) VALUES(" . $this->native_session_id . ", '$nm', '$strSer')";
        else
            $sql = "UPDATE " . $this->session_variable . " SET variable_value = '$strSer' WHERE session_id = '" . $this->native_session_id . "' AND variable_name ='" . $nm . "'";
        $result = $this->dbhandle->query($sql);
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