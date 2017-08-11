<?php
/**
 * Boostack: Upload_File.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.1
 */

class Upload_File
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var
     */
    private $type;

    /**
     * @var float|int
     */
    private $size;

    /**
     * @var
     */
    private $tmp_name;

    /**
     * @var
     */
    private $extension;


    /**
     * Upload_File constructor.
     * @param $file
     * @throws Exception
     */
    public function __construct($file)
    {
        if ($file["error"] != UPLOAD_ERR_OK)
            throw new Exception("Error during file upload. Error code: ".$file["error"].". Error message: ".$this->errorCodeToMessage($file["error"]));
        $pathInfo = pathinfo($file["name"]);
        $this->type = $file["type"];
        $this->name = $file["name"];
        $this->tmp_name = $file["tmp_name"];
        $this->size = $file["size"];
        $this->extension = isset($pathInfo["extension"]) ? strtolower($pathInfo["extension"]) : null;
    }

    /**
     * @param null $maxSize
     * @param null $maxFilenameLength
     * @param null $allowedTypes
     * @param null $allowedExtensions
     * @return bool
     * @throws Exception
     */
    public function constraints($maxSize = null, $maxFilenameLength = null, $allowedTypes = null, $allowedExtensions = null)
    {
        $validFilesize = empty($maxSize) ? Config::get("max_upload_filesize") : $maxSize;
        $validFilenameLength = empty($maxFilenameLength) ? Config::get("max_upload_filename_length") : $maxFilenameLength;
        $validTypes = empty($allowedTypes) ? Config::get("allowed_file_upload_types") : $allowedTypes;
        $validExtensions = empty($allowedExtensions) ? Config::get("allowed_file_upload_extensions") : $allowedExtensions;

        if ($this->size > $validFilesize)
            throw new Exception("File exceed maximum size");
        if (strlen($this->name) > $validFilenameLength)
            throw new Exception("Filename too long");
        if (!in_array($this->type, $validTypes))
            throw new Exception("Filetype not valid");
        if (!in_array($this->extension, $validExtensions))
            throw new Exception("File extension not valid");
        if (!Validator::filename($this->name))
            throw new Exception("Filename not valid");
        return true;
    }

    /**
     * @param $path
     * @param $filename
     * @param int $permission
     * @param bool $overwriteIfExist
     * @return bool
     * @throws Exception
     */
    public function moveTo($path, $filename, $permission = 0755, $overwriteIfExist = false)
    {
        $destinationFullPath = $path.$filename.".".$this->extension;
        if (!file_exists($path))
            throw new Exception("Destination path does not exists: " . $path);
        if (!is_writable($path))
            throw new Exception("Destination path is not writable: " . $path);
        if (!$overwriteIfExist && file_exists($destinationFullPath))
            throw new Exception("File " . $destinationFullPath." already exists");
        if (move_uploaded_file($this->tmp_name, $destinationFullPath))
            if (is_writable($destinationFullPath))
                chmod($destinationFullPath, $permission);
            else
                throw new Exception("File " . $this->name." is not writable");
        else
            throw new Exception("Can't move uploaded file: " . $this->name);
        return true;
    }

    private function errorCodeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

}
?>