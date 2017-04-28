<?php

// TODO include phpseclib

class SFTPConnection {

    private $port = 22;
    private $timeout = 10;
    private $host;

    private $connection;

    public function __construct() {}

    public function connect($host, $username, $password = null, $privateKeyPath = null, $passphrase = null)
    {
        $loginSuccess = false;
        $this->host = $host;
        $this->connection = new SFTP($this->host, $this->port, $this->timeout);

        if($password !== null && $privateKeyPath !== null) {
            try {
                $privateKey = file_get_contents($privateKeyPath);
            } catch(\Exception $e) {
                throw new \Exception("Private key file not found ".$privateKeyPath);
            }
            $key = new RSA();
            if($passphrase !== null) $key->setPassword($passphrase);
            $key->loadKey($privateKey);
            $loginSuccess = !(!$this->connection->login($username, $key) && !$this->connection->login($username, $password));
        } else if($password === null && $privateKeyPath !== null) {
            try {
                $privateKey = file_get_contents($privateKeyPath);
            } catch(Exception $e) {
                throw new Exception("Private key file not found ".$privateKeyPath);
            }
            $key = new RSA();
            if($passphrase !== null) $key->setPassword($passphrase);
            $key->loadKey($privateKey);
            $loginSuccess = $this->connection->login($username, $key);
        } else if($password !== null && $privateKeyPath === null) {
            $loginSuccess = $this->connection->login($username, $password);
        } else {
            throw new Exception("Invalid connection setting params");
        }

        if(!$loginSuccess) {
            throw new Exception("Login failed to SFTP host ".$this->host);
        }
        return true;
    }

    public function disconnect()
    {
        if(!empty($this->connection)) {
            $this->connection->disconnect();
        }
    }

    public function listDir($dir)
    {
        return $this->connection->nList($dir);
    }

    public function dirOrFileExist($dirOrFile)
    {
        return $this->connection->file_exists($dirOrFile);
    }

    public function makeDir($dir)
    {
        $mkdirSuccess = $this->connection->mkdir($dir, -1, true);
        if(!$mkdirSuccess) {
            throw new Exception("Mkdir failed to ".$dir.". Host ".$this->host." \n ".implode(",",$this->getErrors()));
        }
        return true;
    }

    public function uploadFile($remotePath, $localPath)
    {
        if(!$this->dirOrFileExist(dirname($remotePath))) {
            $this->makeDir(dirname($remotePath));
        }
        $putSuccess = $this->connection->put($remotePath, $localPath, SFTP::SOURCE_LOCAL_FILE);
        if(!$putSuccess) {
            throw new Exception("Failed to put ".$localPath." to ".$remotePath.". Host ".$this->host." \n ".implode(",",$this->getErrors()));
        }
        return true;
    }

    public function createFile($remotePath, $content)
    {
        if(!$this->dirOrFileExist(dirname($remotePath))) {
            $this->makeDir(dirname($remotePath));
        }
        $putSuccess = $this->connection->put($remotePath, $content, SFTP::SOURCE_STRING);
        if(!$putSuccess) {
            throw new Exception("Failed to put string content to ".$remotePath.". Host ".$this->host." \n ".implode(",",$this->getErrors()));
        }
        return true;
    }

    public function downloadFile($remoteFile, $localFile)
    {
        if(!$this->dirOrFileExist($remoteFile)) {
            throw new Exception("File ".$remoteFile." not found. Host ".$this->host);
        }
        if(!file_exists(dirname($localFile))) {
            mkdir(dirname($localFile), 0777, true);
        }
        $getContent = $this->connection->get($remoteFile, $localFile);
        if($getContent === false) {
            throw new Exception("Failed to download content from ".$remoteFile." to ".$localFile.". Host ".$this->host." \n ".implode(",",$this->getErrors()));
        }
        return $getContent;
    }

    public function readFile($remoteFile)
    {
        if(!$this->dirOrFileExist($remoteFile)) {
            throw new Exception("File ".$remoteFile." not found. Host ".$this->host);
        }
        $getContent = $this->connection->get($remoteFile, false);
        if($getContent === false) {
            throw new Exception("Failed to read content from ".$remoteFile. ". Host ".$this->host." \n ".implode(",",$this->getErrors()));
        }
        return $getContent;
    }

    public function removeFile($remoteFile)
    {
        if(!$this->dirOrFileExist($remoteFile)) {
            throw new Exception("File ".$remoteFile." not found. Host ".$this->host." \n ".implode(",",$this->getErrors()));
        }
        return $this->connection->delete($remoteFile);
    }

    public function fileInfo($remoteFile)
    {
        if(!$this->dirOrFileExist($remoteFile)) {
            throw new Exception("File ".$remoteFile." not found. Host ".$this->host." \n ".implode(",",$this->getErrors()));
        }
        return $this->connection->lstat($remoteFile);
    }

    public function getErrors()
    {
        return $this->connection->getSFTPErrors();
    }

}