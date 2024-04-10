<?php
namespace Core\Models\Session;
use Core\Models\Database\Database_PDO;
use Core\Models\Log\Log_Level;
use Core\Models\Log\Logger;
use Core\Models\Utils\Utils;
use Core\Models\User\User;
use Core\Models\Config;
use Core\Models\Auth;
use Core\Models\Request;

/**
 * Boostack: Session_HTTP.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */
class Session_HTTP
{

    private $php_session_id;

    private $native_session_id;

    private $dbhandle;

    private $logged_in;

    private $user_id;

    private $session_timeout = 0;

    private $session_lifespan = 0;

    private $http_session_table = "boostack_http_session";

    private $session_variable = "boostack_session_variable";

    private $CSRFDefaultKey = "BCSRFT";

    /**
     * @var bool
     */
    private $newTokenGeneration = true;


    /**
     * Constructor for Session_HTTP class.
     *
     * @param int $timeout The session timeout value in seconds (default: 3600).
     * @param int $lifespan The session lifespan value in seconds (default: 4600).
     */
    public function __construct($timeout = 3600, $lifespan = 4600)
    {
        // Get a handle to the database
        $this->dbhandle = Database_PDO::getInstance();

        // Set session timeout and lifespan
        $this->session_timeout = $timeout;
        $this->session_lifespan = $lifespan;

        // Set session save handler
        $set_save_handler = session_set_save_handler(
            array($this, '_session_open_method'),
            array($this, '_session_close_method'),
            array($this, '_session_read_method'),
            array($this, '_session_write_method'),
            array($this, '_session_destroy_method'),
            array($this, '_session_gc_method')
        );

        // Check if PHPSESSID cookie is set
        if (isset($_COOKIE["PHPSESSID"])) {
            $this->php_session_id = Request::sanitizeInput($_COOKIE["PHPSESSID"]);

            // Get current time
            $datetime_now = time();

            // Retrieve session information from the database
            $sql = "SELECT created, last_impression FROM " . $this->http_session_table . " WHERE ascii_session_id = :ascii_session_id ";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':ascii_session_id', $this->php_session_id);
            $q->execute();
            $lease = $q->fetch();

            // Check if session lease exists
            if ($lease !== FALSE) {
                $interval_created = $datetime_now - intval($lease[0]);
                $interval_last_impression = $datetime_now - intval($lease[1]);

                // Check if session is valid
                $stmt = "SELECT id FROM " . $this->http_session_table . "
                     WHERE ascii_session_id = :ascii_session_id
                     AND :interval_created < :session_lifespan
                     AND user_agent = :user_agent
                     AND (:interval_last_impression <= :session_timeout OR last_impression = 0)";
                $q = $this->dbhandle->prepare($stmt);
                $q->bindValue(':ascii_session_id', $this->php_session_id);
                $q->bindValue(':interval_created', $interval_created, \PDO::PARAM_INT);
                $q->bindValue(':session_lifespan', $this->session_lifespan, \PDO::PARAM_INT);
                $q->bindValue(':user_agent', Request::getUserAgent());
                $q->bindValue(':interval_last_impression', $interval_last_impression, \PDO::PARAM_INT);
                $q->bindValue(':session_timeout', $this->session_timeout, \PDO::PARAM_INT);
                $q->execute();

                // If session is not valid, delete it from the database
                if ($q->rowCount() == 0) {
                    $maxlifetime = $this->session_lifespan;
                    $sql = "DELETE FROM " . $this->http_session_table . " WHERE (ascii_session_id = :ascii_session_id) OR (:datetime_now - created > :maxlifetime)";
                    $q = $this->dbhandle->prepare($sql);
                    $q->bindValue(':ascii_session_id', $this->php_session_id);
                    $q->bindValue(':datetime_now', $datetime_now, \PDO::PARAM_INT);
                    $q->bindValue(':maxlifetime', $maxlifetime, \PDO::PARAM_INT);
                    $q->execute();

                    // Clear expired session variables
                    $sql = "DELETE FROM " . $this->session_variable . " WHERE session_id NOT IN (SELECT id FROM " . $this->http_session_table . ")";
                    $result = $this->dbhandle->prepare($sql);
                    $result->execute();

                    // Unset PHPSESSID cookie
                    unset($_COOKIE["PHPSESSID"]);
                }
            }
        }

        // Set session cookie parameters
        session_set_cookie_params($this->session_lifespan);

        // Start session if not already started
        if (!session_id()) {
            session_start();
        }

        // Record session impression
        $this->Impress();
    }

    /**
     * Method for session open.
     *
     * @param string $save_path
     * @param string $session_name
     * @return bool
     */
    private function _session_open_method($save_path, $session_name)
    {
        return true;
    }


    /**
     * Method for session close.
     *
     * @return bool
     */
    public function _session_close_method()
    {
        // Close the database connection
        $this->dbhandle = NULL;
        return true;
    }

    /**
     * Method for session read.
     *
     * @param string $id
     * @return string
     */
    public function _session_read_method($id)
    {
        // Set the PHP session ID
        $this->php_session_id = $id;

        // Query the database for session information
        $sql = "SELECT id, logged_in, user_id FROM " . $this->http_session_table . " WHERE ascii_session_id = :ascii_session_id";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':ascii_session_id', $this->php_session_id);
        $q->execute();

        // Check if session exists
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
            // If session does not exist, create a new session entry
            $this->logged_in = false;
            $sql = "INSERT INTO " . $this->http_session_table . "(id, ascii_session_id, logged_in, user_id, created, user_agent)
                VALUES (NULL, :ascii_session_id, :logged_in, :user_id, :created, :user_agent)";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':ascii_session_id', $id);
            $q->bindValue(':logged_in', "f");
            $q->bindValue(':user_id', 1);
            $q->bindValue(':created', time());
            $q->bindValue(':user_agent', Request::getUserAgent());
            $q->execute();

            // Retrieve the newly created session ID
            $sql = "SELECT id FROM " . $this->http_session_table . " WHERE ascii_session_id = :ascii_session_id";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':ascii_session_id', $id);
            $q->execute();
            $row = $q->fetch();
            $this->native_session_id = $row["id"];
        }

        // Return an empty string
        return "";
    }

    /**
     * Method to update session impression time.
     */
    public function Impress()
    {
        // Update last impression time if session ID is set
        if ($this->native_session_id) {
            $sql = "UPDATE " . $this->http_session_table . " SET last_impression = :last_impression WHERE id = :native_session_id";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':last_impression', time());
            $q->bindValue(':native_session_id', $this->native_session_id);
            $q->execute();
        }
    }

    /**
     * Check if a user is logged in.
     *
     * @return bool
     */
    public function IsLoggedIn()
    {
        return $this->logged_in;
    }

    /**
     * Get the ID of the logged-in user.
     *
     * @return bool|int
     */
    public function GetUserID()
    {
        if ($this->logged_in) {
            return $this->user_id;
        } else {
            return false;
        }
    }

    /**
     * Get the User object of the logged-in user.
     *
     * @return User|null
     */
    public function GetUserObject()
    {
        if ($this->logged_in && class_exists("\Core\Models\User\User_Entity")) {
            return new User($this->user_id);
        }
        return null;
    }

    /**
     * Get the session identifier.
     *
     * @return array|string
     */
    public function GetSessionIdentifier()
    {
        return $this->php_session_id;
    }

    /**
     * Logout the user.
     *
     * @return bool
     */
    public function logoutUser()
    {
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
     * Login the user.
     *
     * @param $userID
     */
    public function loginUser($userID)
    {
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
     * Magic method to get session variable value.
     *
     * @param $nm
     * @return mixed|string
     */
    public function __get($nm)
    {
        $sql = "SELECT variable_value FROM " . $this->session_variable . " WHERE session_id = :session_id AND variable_name = :variable_name ORDER BY id DESC";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':session_id', $this->native_session_id);
        $q->bindValue(':variable_name', $nm);
        $q->execute();
        if ($q->rowCount() > 0) {
            $row = $q->fetch();
            return unserialize($row["variable_value"]);
        } else {
            return "";
        }
    }

    /**
     * Magic method to set session variable value.
     *
     * @param $nm
     * @param $val
     */
    public function __set($nm, $val)
    {
        $strSer = serialize($val);
        $this->native_session_id = ($this->native_session_id == "") ? 0 : $this->native_session_id;
        $sql = "SELECT id FROM " . $this->session_variable . " WHERE session_id = :session_id AND variable_name = :variable_name";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':session_id', $this->native_session_id);
        $q->bindValue(':variable_name', $nm);
        $q->execute();
        if ($q->rowCount() == 0) {
            $sql = "INSERT INTO " . $this->session_variable . "(session_id, variable_name, variable_value) VALUES(:session_id, :variable_name, :variable_value)";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':session_id', $this->native_session_id);
            $q->bindValue(':variable_name', $nm);
            $q->bindValue(':variable_value', $strSer);
            $q->execute();
        } else {
            $sql = "UPDATE " . $this->session_variable . " SET variable_value = :variable_value WHERE session_id = :session_id AND variable_name = :variable_name";
            $q = $this->dbhandle->prepare($sql);
            $q->bindValue(':variable_value', $strSer);
            $q->bindValue(':session_id', $this->native_session_id);
            $q->bindValue(':variable_name', $nm);
            $q->execute();
        }
    }

    /**
     * Placeholder method for writing session data.
     *
     * @param mixed $id The session ID.
     * @param mixed $sess_data The session data.
     * @return bool Returns true as a placeholder for successful session data writing.
     */
    public function _session_write_method($id, $sess_data)
    {
        return true;
    }

    /**
     * Destroys a session.
     *
     * @param mixed $id The session ID.
     * @return bool Returns true if the session was successfully destroyed, false otherwise.
     */
    private function _session_destroy_method($id)
    {
        $sql = "DELETE FROM " . $this->http_session_table . " WHERE ascii_session_id = :ascii_session_id";
        $q = $this->dbhandle->prepare($sql);
        $q->bindValue(':ascii_session_id', $id);
        $q->execute();
        if ($q->execute()) {
            return true;
        }
        return false;
    }



    /**
     * Method invoked automatically by PHP's Garbage Collector.
     *
     * @param $maxlifetime auto-injected by PHP config
     * @return bool
     */
    private function _session_gc_method($maxlifetime)
    {
        return true;
        //        $old = time() - $maxlifetime;
        //        /* Remove all old expired sessions except the current session */
        //        $sql = 'DELETE FROM ' . $this->http_session_table . ' WHERE last_impression < '.$old.' AND id <> '.$this->native_session_id;
        //        $result = $this->dbhandle->prepare($sql);
        //        if ($result->execute())
        //            return true;
        //        return false;
    }


    /**
     * Render a hidden field containing the CSRF token.
     *
     * @return string
     */
    public function CSRFRenderHiddenField()
    {
        return "<input type=\"hidden\" name=\"" . $this->CSRFDefaultKey . "\" id=\"" . $this->CSRFDefaultKey . "\"  class=\"CSRFcheck\" value=\"" . self::CSRFTokenGenerator() . "\"/>";
    }

    /**
     * Get the default CSRF key.
     *
     * @return string
     */
    public function getCSRFDefaultKey()
    {
        return $this->CSRFDefaultKey;
    }

    /**
     * Get the CSRF key value.
     *
     * @return string
     */
    public function getCSRFKey()
    {
        $key = $this->CSRFDefaultKey;
        return $this->$key;
    }

    /**
     * Generate a CSRF token.
     *
     * @return string
     */
    public function CSRFTokenGenerator()
    {
        $key = $this->CSRFDefaultKey;
        if ($this->$key == null) {
            $token = base64_encode(Utils::getSecureRandomString(32) . self::getRequestInfo() . time());
            $this->$key = $token; // store in session
        } else {
            if (Auth::isLoggedIn()) {
                $timespan = Config::get("csrf_timeout");
                $decodedToken = base64_decode($this->$key);
                $decodedToken_timestamp = intval(substr($decodedToken, -10));
                // check token validity, if expired, generate a new one
                if ($decodedToken_timestamp + $timespan < time())
                    $this->CSRFTokenInvalidation();
            } else
                $this->CSRFTokenInvalidation();
        }
        return $this->$key;
    }

    /**
     * Get request information.
     *
     * @return string
     */
    protected static function getRequestInfo()
    {
        return sha1(Request::sanitizeInput(Request::getIpAddress() . Request::getUserAgent()));
    }

    /**
     * Check the validity of the CSRF token.
     *
     * @param $postArray
     * @param bool $throwException
     * @return bool
     * @throws \Exception
     */
    protected function CSRFCheckTokenValidity($postArray, $throwException = true)
    {
        $timespan = Config::get("csrf_timeout");
        $key = $this->CSRFDefaultKey; // get token value from dbsession
        $sessionToken = $this->$key;

        if ($sessionToken == "")
            if ($throwException)
                throw new \Exception('Attention! Missing CSRF session token.');
            else
                return false;

        if (!isset($postArray[$key]))
            if ($throwException)
                throw new \Exception('Attention! Missing CSRF form token.');
            else
                return false;

        if ($postArray[$key] != $sessionToken)
            if ($throwException) {
                $this->CSRFTokenInvalidation();
                throw new \Exception('Attention! Invalid CSRF token.' . $postArray[$key] . '<br>' . $sessionToken);
            } else {
                $this->CSRFTokenInvalidation();
                return false;
            }

        $decodedToken = base64_decode($sessionToken);
        $decodedToken_requestInfo = substr($decodedToken, 32, 40);
        $decodedToken_timestamp = intval(substr($decodedToken, -10));

        if (self::getRequestInfo() != $decodedToken_requestInfo) {
            if ($throwException)
                throw new \Exception('Attention! Form request infos don\'t match token request infos.');
            else
                return false;
        }

        if ($timespan != null && is_int($timespan) && $decodedToken_timestamp + $timespan < time())
            if ($throwException)
                throw new \Exception('Attention! CSRF token has expired.');
            else {
                $this->CSRFTokenInvalidation();
                return false;
            }

        return true;
    }

    /**
     * Invalidate the CSRF token.
     *
     * @return null|string
     */
    public function CSRFTokenInvalidation()
    {
        $res = NULL;
        $key = $this->CSRFDefaultKey;
        $this->$key = null;
        if ($this->newTokenGeneration) {
            $res = $this->CSRFTokenGenerator();
        }
        return $res;
    }

    /**
     * Check the validity of the CSRF token.
     *
     * @param $postArray
     * @param bool $throwException
     * @return bool
     * @throws \Exception
     */
    public function CSRFCheckValidity($postArray, $throwException = true)
    {
        try {
            return $this->CSRFCheckTokenValidity($postArray, $throwException);
        } catch (\Exception $e) {
            Logger::write('Session_CSRF -> CSRFCheckValidity -> Caught \Exception: ' . $e->getMessage() . $e->getTraceAsString(), Log_Level::ERROR);
            throw new \Exception('Invalid CSRF token' . $e->getMessage());
        }
    }
}
