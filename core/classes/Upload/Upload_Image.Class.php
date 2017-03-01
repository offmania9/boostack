<?php
/**
 * Boostack: Upload_Image.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
class Upload_Image
{

    private $source;

    private $name;

    private $visual_name;

    private $type;

    private $size;

    private $tmp_name;

    private $path;

    private $extension;

    private $height;

    private $width;

    private $filter;

    private $preview_path;

    private $image_types = array(
        "image/gif",
        "image/jpeg",
        "image/pjpeg",
        "image/bmp",
        "image/png"
    );

    public function __construct($file, $destination_folder, $exitifexist = true, $target_name = NULL, $visual_name = NULL, $resize = NULL, $preview_size = NULL, $filter = NULL)
    {
        // if($this->constraints($file)){
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
            $this->preview_path = $destination_folder . "s_" . $this->name;
            
            // if ($exitifexist && file_exists($destination_folder.$file["name"])){
            // throw new Exception("File already exist.");
            // }
            // else{
            if (move_uploaded_file($file["tmp_name"], $destination_folder . $this->name))
                chmod($destination_folder . $this->name, 0755);
            else
                throw new Exception("Can't MoveUploaded file: " . $this->name);
                // }
            list ($width, $height, $type, $attr) = getimagesize($this->path);
            $this->height = $height;
            $this->width = $width;
            
            if ($resize !== NULL) {
                if ($this->width >= $this->height) {
                    if ($this->width >= $resize)
                        $this->resizeToWidth($resize, $filter);
                    else
                        $this->resizeToWidth($this->width, $filter);
                } else {
                    if ($this->height >= 1237)
                        $this->resizeToHeight(1237, $filter);
                    else
                        $this->resizeToHeight($this->height, $filter);
                }
            }
            if ($preview_size !== NULL) {
                $this->previewResizeToWidth($preview_size[0], $filter);
            }
        }
        // }
    }

    public function constraints($file)
    {
        global $boostack, $MAX_UPLOAD_IMAGE_SIZE, $MAX_UPLOAD_PDF_SIZE, $MAX_UPLOAD_NAMEFILE_LENGTH, $MAX_UPLOAD_GENERALFILE_SIZE, $mime_types;
        
        if (strlen($file["name"]) >= $boostack->getConfig("max_upload_namefile_length")) { // # FILE NAME TOO LONG
            throw new Exception("File Name too long. Rename it and repeat upload. <br />");
        }
        if (in_array($file["type"], $this->image_types)) { // IS IMAGE
            if ($file["size"] > $boostack->getConfig("max_upload_image_size")) // SIZE CHECK
                throw new Exception("File too large. <br />");
            return true;
        }
        throw new Exception("Unknown file. <br />" . $mime_types["" . $file["type"]] . $file["type"]);
    }

    public function remove()
    {
        return unlink($this->path);
    }

    public function __get($property_name)
    {
        if (isset($this->$property_name)) {
            return ($this->$property_name);
        } else {
            return (NULL);
        }
    }

    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }

    function resizeToHeight($height, $filter = NULL)
    {
        $ratio = $height / $this->height;
        $width = $this->width * $ratio;
        $this->resize($width, $height, $filter);
    }

    function resizeToWidth($width, $filter = NULL)
    {
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        $this->resize($width, $height, $filter);
    }

    function previewResizeToWidth($width, $filter)
    {
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        $this->previewResize($width, $height, $filter);
    }

    function scale($scale)
    {
        $width = $this->width * $scale / 100;
        $height = $this->height * $scale / 100;
        $this->resize($width, $height);
    }

    function resize($width, $height, $filter = NULL)
    {
        $new_image = imagecreatetruecolor($width, $height);
        if ($this->type == "image/jpeg" || $this->type == "image/pjpeg")
            $this->source = imagecreatefromjpeg($this->path);
        if ($this->type == "image/gif")
            $this->source = imagecreatefromgif($this->path);
        if ($this->type == "image/bmp")
            $this->source = imagecreatefromwbmp($this->path);
        if ($this->type == "image/png")
            $this->source = imagecreatefrompng($this->path);
        
        imagecopyresampled($new_image, $this->source, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        $this->remove();
        
        switch ($filter) {
            case "la":
                {
                    imagefilter($new_image, IMG_FILTER_CONTRAST, - 40);
                    break;
                }
            case "ny":
                {
                    imagefilter($new_image, IMG_FILTER_GRAYSCALE);
                    break;
                }
            case "sd":
                {
                    imagefilter($new_image, IMG_FILTER_COLORIZE, 0, 0, 100);
                    break;
                }
            case "sf":
                {
                    imagefilter($new_image, IMG_FILTER_COLORIZE, 0, 100, 0);
                    break;
                }
        }
        if ($this->type == "image/jpeg" || $this->type == "image/pjpeg")
            imagejpeg($new_image, $this->path, 100);
        if ($this->type == "image/gif")
            imagegif($new_image, $this->path, 100);
        if ($this->type == "image/bmp")
            imagewbmp($new_image, $this->path, 100);
        if ($this->type == "image/png")
            imagepng($new_image, $this->path, 100);
        
        $this->path = $this->path;
    }

    function previewResize($width, $height, $filter = NULL)
    {
        $new_image = imagecreatetruecolor($width, $height);
        if ($this->type == "image/jpeg" || $this->type == "image/pjpeg")
            $this->source = imagecreatefromjpeg($this->path);
        if ($this->type == "image/gif")
            $this->source = imagecreatefromgif($this->path);
        if ($this->type == "image/bmp")
            $this->source = imagecreatefromwbmp($this->path);
        if ($this->type == "image/png")
            $this->source = imagecreatefrompng($this->path);
        
        imagecopyresampled($new_image, $this->source, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        
        if ($this->type == "image/jpeg" || $this->type == "image/pjpeg")
            imagejpeg($new_image, $this->preview_path, 100);
        if ($this->type == "image/gif")
            imagegif($new_image, $this->preview_path, 100);
        if ($this->type == "image/bmp")
            imagewbmp($new_image, $this->preview_path, 100);
        if ($this->type == "image/png")
            imagepng($new_image, $this->preview_path, 100);
        
        $this->preview_path = $this->preview_path;
    }
}
?>