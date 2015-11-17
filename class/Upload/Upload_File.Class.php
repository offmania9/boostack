<?php
/**
 * Boostack: Upload_File.Class.php
 * ========================================================================
 * Copyright 2015 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 2
 */
require_once ("extension_mimetypes.inc.php");

class Upload_File
{

    private $name;

    private $visual_name;

    private $type;

    private $size;

    private $tmp_name;

    private $path;

    private $extension;

    private $image_types = array(
        "image/gif",
        "image/jpeg",
        "image/pjpeg",
        "image/bmp",
        "image/png"
    );

    public function __construct($file, $destination_folder, $exitifexist = true, $target_name = null, $visual_name = null)
    {
        if ($file["error"] > 0) {
            // throw new Exception("Return Error Code: : ".$file["error"]."<br />");
            return;
        } else {
            $info = pathinfo($file["name"]);
            $this->visual_name = $visual_name;
            $this->extension = $info["extension"];
            $this->name = ($target_name == null) ? $file["name"] : $target_name . "." . $this->extension;
            $this->type = $file["type"];
            $this->size = $file["size"] / 1024;
            $this->tmp_name = $file["tmp_name"];
            $this->path = $destination_folder . $this->name;
            
            // if ($exitifexist && file_exists($destination_folder.$file["name"])){
            // throw new Exception("File already exist.");
            // }
            // else{
            if (move_uploaded_file($file["tmp_name"], $destination_folder . $this->name))
                chmod($destination_folder . $this->name, 0755);
            else
                throw new Exception("Can't MoveUploaded file: " . $this->name);
            // }
        }
    }

    public function constraints($file)
    {
        global $MAX_UPLOAD_IMAGE_SIZE, $MAX_UPLOAD_PDF_SIZE, $MAX_UPLOAD_NAMEFILE_LENGTH, $MAX_UPLOAD_GENERALFILE_SIZE, $mime_types;
        
        if (strlen($file["name"]) >= $MAX_UPLOAD_NAMEFILE_LENGTH) { // # se il nome del file � troppo lungo
            throw new Exception("File Name too long. Rename it and repeat upload. <br />");
        }
        if (in_array($file["type"], $this->image_types)) { // IS IMAGE
            if ($file["size"] > $MAX_UPLOAD_IMAGE_SIZE) // SIZE CHECK
                throw new Exception("File too large. <br />");
            return true;
        }
        /*
         * if ($file["type"] == "application/pdf"){ #
         * if($file["size"] > $MAX_UPLOAD_PDF_SIZE) #SIZE CHECK
         * throw new Exception("File too large. <br />");
         * return true;
         * }
         * if (in_array($file["type"],$mime_types)){ # ANY OTHER FILE IN LIST OF EXTENSIONS
         * if($file["size"] > $MAX_UPLOAD_GENERALFILE_SIZE) #SIZE CHECK
         * throw new Exception("File too large. > $MAX_UPLOAD_GENERALFILE_SIZE<br />");
         * return true;
         * }
         */
        throw new Exception("Unknown file. <br />" . $mime_types["" . $file["type"]] . $file["type"]);
    }

    public function remove()
    {
        return unlink($this->path . $this->name);
    }

    public function __get($property_name)
    {
        if (isset($this->$property_name))
            return ($this->$property_name);
        else
            return (NULL);
    }

    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }
}
?>