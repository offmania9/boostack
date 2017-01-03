<?php

/**
 * Boostack: Session_CSRF.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2.3
 */
class Session_CSRF extends Session_HTTP
{

    private $CSRFDefaultKey = "BCSRFT";

    private $CSRFRandomStringLength = 32;

    private $newTokenGeneration = false;

    public function CSRFRenderHiddenField()
    {
        return "<input type=\"hidden\" name=\"" . $this->CSRFDefaultKey. "\" id=\"" . $this->CSRFDefaultKey . "\"  class=\"CSRFcheck\" value=\"" . self::CSRFTokenGenerator() . "\"/>";
    }

    public function CSRFTokenGenerator()
    {
        $key = $this->CSRFDefaultKey;
        $token = base64_encode(Utils::getSecureRandomString(32) . self::getRequestInfo() . time());
        $this->$key = $token; // store in session
        return $token;
    }

    protected static function getRequestInfo()
    {
        return sha1(Utils::sanitizeInput(Utils::getIpAddress() . Utils::getUserAgent()));
    }

    protected function CSRFCheckTokenValidity($postArray, $timespan = null, $oneTimeToken = false, $throwException = true)
    {
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
            if ($throwException)
                throw new Exception('Attention! Invalid CSRF token.'.$postArray[$key].'<br>'.$sessionToken);
            else
                return false;
        
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
            else
                return false;

        if ($oneTimeToken)
            $newToken = CSRCTokenInvalidation();
        
        return true;
    }

    public function CSRCTokenInvalidation(){
        $res = NULL;
        $key = $this->CSRFDefaultKey;
        $this->$key = null;
        if($this->newTokenGeneration){
            $res = CSRFTokenGenerator();
        }
        return $res;
    }

    public function CSRFCheckValidity($postArray, $timespan = null, $oneTimeToken = false, $throwException = true){
        global $boostack;
        try
        {
            $this->CSRFCheckTokenValidity($postArray, $timespan, $oneTimeToken, $throwException);
            #$boostack->writeLog('CSRFToken: OK');
        }
        catch(Exception $e)
        {
            $boostack->writeLog('Session_CSRF -> CSRFCheckValidity -> Caught exception: '.$e->getMessage().$e->getTraceAsString(),"error");
            throw new Exception('Attention! Wrong CSRF token.');
        }
    }
}
?>