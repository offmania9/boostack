<?php

/**
 * Boostack: Session_CSRF.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Session_CSRF extends Session_HTTP
{

    /**
     * @var string
     */
    private $CSRFDefaultKey = "BCSRFT";

    /**
     * @var bool
     */
    private $newTokenGeneration = true;

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
            Logger::write('Session_CSRF -> CSRFCheckValidity -> Caught exception: '.$e->getMessage().$e->getTraceAsString(),Logger::LEVEL_ERROR);
            throw new Exception('Invalid CSRF token');
        }
    }
}
?>