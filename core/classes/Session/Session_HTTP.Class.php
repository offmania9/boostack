<?php

/**
 * Boostack: Session_HTTP.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
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
     * @var string
     */
    private $CSRFDefaultKey = "BCSRFT";

    /**
     * @var bool
     */
    private $newTokenGeneration = true;


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
            $this->php_session_id = Utils::sanitizeInput($_COOKIE["PHPSESSID"]);
        }
        $datetime_now = time();
        $sql = "SELECT created,last_impression FROM " . $this->http_session_table . " WHERE ascii_session_id = :ascii_session_id ";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':ascii_session_id', $this->php_session_id);
        $q->execute();
        $lease = $q->fetch();
        $interval_created = $datetime_now - intval($lease[0]);
        $interval_last_impression = $datetime_now - intval($lease[1]);

        $stmt = "SELECT id FROM " . $this->http_session_table . "
                    WHERE ascii_session_id = :ascii_session_id
                        AND :interval_created < :session_lifespan
                        AND user_agent = :user_agent
                        AND :interval_last_impression <= :session_timeout
              OR last_impression = 0
              ";
        $q = $this->dbhandle->prepare($stmt);
        $q->bindValue(':ascii_session_id', $this->php_session_id);
        $q->bindValue(':interval_created', $interval_created, PDO::PARAM_INT);
        $q->bindValue(':session_lifespan', $this->session_lifespan, PDO::PARAM_INT);
        $q->bindValue(':user_agent', Utils::getUserAgent());
        $q->bindValue(':interval_last_impression', $interval_last_impression, PDO::PARAM_INT);
        $q->bindValue(':session_timeout', $this->session_timeout, PDO::PARAM_INT);
        $q->execute();
        if ($q->rowCount() == 0) {
            $maxlifetime = $this->session_lifespan;
            $sql = "DELETE FROM " . $this->http_session_table . " WHERE (ascii_session_id = :ascii_session_id) OR (:datetime_now - created > :maxlifetime)";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':ascii_session_id', $this->php_session_id);
            $q->bindValue(':datetime_now', $datetime_now, PDO::PARAM_INT);
            $q->bindValue(':maxlifetime', $maxlifetime, PDO::PARAM_INT);
            $q->execute();
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
        $sql = "SELECT id, logged_in, user_id FROM " . $this->http_session_table . " WHERE ascii_session_id = :ascii_session_id";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':ascii_session_id', $this->php_session_id);
        $q->execute();
        if ($q->rowCount() > 0) {
            $row = $q->fetch();
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
							VALUES (NULL, :ascii_session_id, :logged_in, :user_id, :created, :user_agent)";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':ascii_session_id', $id);
            $q->bindValue(':logged_in', "f");
            $q->bindValue(':user_id', 1);
            $q->bindValue(':created', time());
            $q->bindValue(':user_agent', Utils::getUserAgent());
            $q->execute();

            $sql = "SELECT id FROM " . $this->http_session_table . " WHERE ascii_session_id = :ascii_session_id";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':ascii_session_id', $id);
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
            $sql = "UPDATE " . $this->http_session_table . " SET last_impression = :last_impression WHERE id = :native_session_id";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':last_impression', time());
            $q->bindValue(':native_session_id', $this->native_session_id);
            $q->execute();
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
        $sql = "UPDATE " . $this->http_session_table . " SET logged_in = :logged_in, user_id = :user_id WHERE id = :native_session_id";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':logged_in', 'f');
        $q->bindValue(':user_id', '1');
        $q->bindValue(':native_session_id', $this->native_session_id);
        $q->execute();
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
        $sql = "UPDATE " . $this->http_session_table . " SET logged_in = :logged_in, user_id = :user_id WHERE id = :native_session_id";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':logged_in', 't');
        $q->bindValue(':user_id', $this->user_id);
        $q->bindValue(':native_session_id', $this->native_session_id);
        $q->execute();
    }

    /**
     * @param $nm
     * @return mixed|string
     */
    public function __get($nm)
    {
        $sql = "SELECT variable_value FROM " . $this->session_variable . "
				WHERE session_id = :session_id
				AND variable_name = :variable_name ORDER BY id DESC";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':session_id', $this->native_session_id);
        $q->bindValue(':variable_name', $nm);
        $q->execute();
        if ($q->rowCount() > 0) {
            $row = $q->fetch();
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
				WHERE session_id = :session_id AND variable_name = :variable_name";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':session_id', $this->native_session_id);
        $q->bindValue(':variable_name', $nm);
        $q->execute();
        if ($q->rowCount() == 0) {
            $sql = "INSERT INTO " . $this->session_variable . "(session_id, variable_name, variable_value)
               VALUES(:session_id, :variable_name, :variable_value)";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':session_id', $this->native_session_id);
            $q->bindValue(':variable_name', $nm);
            $q->bindValue(':variable_value', $strSer);
            $q->execute();
        } else {
            $sql = "UPDATE " . $this->session_variable . " SET variable_value = :variable_value
               WHERE session_id = :session_id AND variable_name = :variable_name";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':variable_value', $strSer);
            $q->bindValue(':session_id', $this->native_session_id);
            $q->bindValue(':variable_name', $nm);
            $q->execute();
        }
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
        $sql = "DELETE FROM " . $this->http_session_table . " WHERE ascii_session_id = :ascii_session_id";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':ascii_session_id', $id);
        $q->execute();
        if ($q->execute())
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

    /**
     * @return string
     */
    public function CSRFRenderHiddenField()
    {
        return "<input type=\"hidden\" name=\"" . $this->CSRFDefaultKey. "\" id=\"" . $this->CSRFDefaultKey . "\"  class=\"CSRFcheck\" value=\"" . self::CSRFTokenGenerator() . "\"/>";
    }

    /**
     * @return string
     */
    public function getCSRFDefaultKey()
    {
        return $this->CSRFDefaultKey;
    }

    /**
     * @return string
     */
    public function getCSRFKey()
    {
        $key = $this->CSRFDefaultKey;
        return $this->$key;
    }


    /**
     * @return string
     */
    public function CSRFTokenGenerator()
    {
        $key = $this->CSRFDefaultKey;
        if ($this->$key == null){
            $token = base64_encode(Utils::getSecureRandomString(32) . self::getRequestInfo() . time());
            $this->$key = $token; // store in session
        }
        else{
            if(Auth::isLoggedIn()) {
                $timespan = Config::get("csrf_timeout");
                $decodedToken = base64_decode($this->$key);
                $decodedToken_timestamp = intval(substr($decodedToken, -10));
                // check token validity, if expired, i generate a new one
                if ($decodedToken_timestamp + $timespan < time())
                    $this->CSRFTokenInvalidation();
            }
            else
                $this->CSRFTokenInvalidation();
        }
        return $this->$key;
    }

    /**
     * @return string
     */
    protected static function getRequestInfo()
    {
        return sha1(Utils::sanitizeInput(Utils::getIpAddress() . Utils::getUserAgent()));
    }

    /**
     * @param $postArray
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    protected function CSRFCheckTokenValidity($postArray, $throwException = true)
    {
        $timespan = Config::get("csrf_timeout");
        $key = $this->CSRFDefaultKey; // get token value from dbsession
        $sessionToken = $this->$key;

        if ($sessionToken == "")
            if ($throwException)
                throw new Exception('Attention! Missing CSRF session token.');
            else
                return false;

        if (! isset($postArray[$key]))
            if ($throwException)
                throw new Exception('Attention! Missing CSRF form token.');
            else
                return false;

        if ($postArray[$key] != $sessionToken)
            if ($throwException) {
                $this->CSRFTokenInvalidation();
                throw new Exception('Attention! Invalid CSRF token.' . $postArray[$key] . '<br>' . $sessionToken);
            }
            else{
                $this->CSRFTokenInvalidation();
                return false;
            }

        $decodedToken = base64_decode($sessionToken);
        $decodedToken_requestInfo = substr($decodedToken, 32, 40);
        $decodedToken_timestamp = intval(substr($decodedToken, - 10));


        if (self::getRequestInfo() != $decodedToken_requestInfo) {
            if ($throwException)
                throw new Exception('Attention! Form request infos don\'t match token request infos.');
            else
                return false;
        }

        if ($timespan != null && is_int($timespan) && $decodedToken_timestamp + $timespan < time())
            if ($throwException)
                throw new Exception('Attention! CSRF token has expired.');
            else {
                $this->CSRFTokenInvalidation();
                return false;
            }

        return true;
    }

    /**
     * @return null|string
     */
    public function CSRFTokenInvalidation(){
        $res = NULL;
        $key = $this->CSRFDefaultKey;
        $this->$key = null;
        if($this->newTokenGeneration){
            $res = $this->CSRFTokenGenerator();
        }
        return $res;
    }

    /**
     * @param $postArray
     * @param bool $throwException
     * @return bool
     * @throws Exception
     */
    public function CSRFCheckValidity($postArray, $throwException = true){
        try {
            return $this->CSRFCheckTokenValidity($postArray, $throwException);
        } catch(Exception $e) {
            Logger::write('Session_CSRF -> CSRFCheckValidity -> Caught exception: '.$e->getMessage().$e->getTraceAsString(),Log_Level::ERROR);
            throw new Exception('Invalid CSRF token'.$e->getMessage());
        }
    }
}
?>
