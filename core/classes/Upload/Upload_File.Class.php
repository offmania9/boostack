<?php
/**
 * Boostack: Upload_File.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
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
     * @param $destination_folder
     * @param bool $exitifexist
     * @param null $target_name
     * @param null $visual_name
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
     * @param $file
     * @return bool
     * @throws Exception
     */
    public function constraints()
    {
        if($this->size > Config::get("max_upload_filesize"))
            throw new Exception("File exceed maximum size");
        if(strlen($this->name) > Config::get("max_upload_filename_length"))
            throw new Exception("Filename too long");
        if(!in_array($this->type, Config::get("allowed_file_upload_types")))
            throw new Exception("Filetype not valid");
        if (!in_array($this->extension, Config::get("allowed_file_upload_extensions")))
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
     * @throws Exception
     */
    public function moveTo($path, $filename, $permission = 0755, $overwriteIfExist = false)
    {
        $destinationFullPath = $path.$filename.".".$this->extension;
        if(!$overwriteIfExist && file_exists($destinationFullPath)) {
            throw new Exception("File " . $destinationFullPath." already exists");
        }
        if(move_uploaded_file($this->tmp_name, $destinationFullPath))
            if(is_writable($destinationFullPath))
                chmod($destinationFullPath, $permission);
            else
                throw new Exception("File " . $this->name." is not writable");
        else
            throw new Exception("Can't move uploaded file: " . $this->name);
        return true;
    }

    /**
     * @param $code
     * @return string
     */
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