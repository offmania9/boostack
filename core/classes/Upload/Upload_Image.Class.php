<?php
/**
 * Boostack: Upload_Image.Class.php
 * ========================================================================
 * Copyright 2014-2021 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4
 */
class Upload_Image
{

    /**
     * @var
     */
    private $source;

    /**
     * @var string
     */
    private $name;

    /**
     * @var null
     */
    private $visual_name;

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
     * @var string
     */
    private $path;

    /**
     * @var
     */
    private $extension;

    /**
     * @var
     */
    private $height;

    /**
     * @var
     */
    private $width;

    /**
     * @var
     */
    private $filter;

    /**
     * @var string
     */
    private $preview_path;

    /**
     * @var array
     */
    private $image_types = array(
        "image/gif",
        "image/jpeg",
        "image/pjpeg",
        "image/bmp",
        "image/png"
    );

    private $PNG_compression = 1;

    /**
     * Upload_Image constructor.
     * @param $file
     * @param $destination_folder
     * @param bool $exitifexist
     * @param null $target_name
     * @param null $visual_name
     * @param null $resize
     * @param null $preview_size
     * @param null $filter
     * @throws Exception
     */
    public function __construct($file, $destination_folder, $exitifexist = true, $target_name = NULL, $visual_name = NULL, $resize = NULL, $preview_size = NULL, $filter = NULL)
    {
        if($this->constraints($file)){
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
        }
    }

    /**
     * @param $file
     * @return bool
     * @throws Exception
     */
    public function constraints($file)
    {
        global $boostack, $MAX_UPLOAD_IMAGE_SIZE, $MAX_UPLOAD_PDF_SIZE, $MAX_UPLOAD_NAMEFILE_LENGTH, $MAX_UPLOAD_GENERALFILE_SIZE, $mime_types;
        
        if (strlen($file["name"]) >= Config::get("max_upload_filename_length")) { // # FILE NAME TOO LONG
            throw new Exception("File Name too long. Rename it and repeat upload. <br />");
        }
        if (in_array($file["type"], $this->image_types)) { // IS IMAGE
            if ($file["size"] > Config::get("max_upload_image_size")) // SIZE CHECK
                throw new Exception("File too large. <br />");
            return true;
        }
        throw new Exception("Unknown file. <br />" . $mime_types["" . $file["type"]] . $file["type"]);

    }

    /**
     * @return bool
     */
    public function remove()
    {
        return unlink($this->path);
    }

    /**
     * @param $property_name
     * @return null
     */
    public function __get($property_name)
    {
        if (isset($this->$property_name)) {
            return ($this->$property_name);
        } else {
            return (NULL);
        }
    }

    /**
     * @param $property_name
     * @param $val
     */
    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }

    /**
     * @param $height
     * @param null $filter
     */
    function resizeToHeight($height, $filter = NULL)
    {
        $ratio = $height / $this->height;
        $width = $this->width * $ratio;
        $this->resize($width, $height, $filter);
    }

    /**
     * @param $width
     * @param null $filter
     */
    function resizeToWidth($width, $filter = NULL)
    {
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        $this->resize($width, $height, $filter);
    }

    /**
     * @param $width
     * @param $filter
     */
    function previewResizeToWidth($width, $filter)
    {
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        $this->previewResize($width, $height, $filter);
    }

    /**
     * @param $scale
     */
    function scale($scale)
    {
        $width = $this->width * $scale / 100;
        $height = $this->height * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * @param $width
     * @param $height
     * @param null $filter
     */
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
            imagepng($new_image, $this->path, $this->PNG_compression);
        
        $this->path = $this->path;
    }

    /**
     * @param $width
     * @param $height
     * @param null $filter
     */
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
            imagepng($new_image, $this->preview_path, $this->PNG_compression);
        
        $this->preview_path = $this->preview_path;
    }
}
?>