<?php

/**
 * Boostack: Upload_Image.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 4.2
 */
// Link image type to correct image loader and saver
// - makes it easier to add additional types later on
// - makes the function easier to read


const IMAGETYPE_EXTENSION = array(
    "gif" => IMAGETYPE_GIF,
    "jpeg" => IMAGETYPE_JPEG,
    "png" => IMAGETYPE_PNG,
    "swf" => IMAGETYPE_SWF,
    "psd" => IMAGETYPE_PSD,
    "bmp" => IMAGETYPE_BMP,
    "tiff" => IMAGETYPE_TIFF_II,
    "tiff" => IMAGETYPE_TIFF_MM,
    "jpc" => IMAGETYPE_JPC,
    "jp2" => IMAGETYPE_JP2,
    "jpx" => IMAGETYPE_JPX,
    "jb2" => IMAGETYPE_JB2,
    "swc" => IMAGETYPE_SWC,
    "iff" => IMAGETYPE_IFF,
    "wbmp" => IMAGETYPE_WBMP,
    "xbm" => IMAGETYPE_XBM,
    "ico" => IMAGETYPE_ICO,
    "webp" => IMAGETYPE_WEBP
);

const IMAGE_HANDLERS = [
    null,
    IMAGETYPE_GIF => [
        'load' => 'imagecreatefromgif',
        'save' => 'imagegif'
    ],
    IMAGETYPE_JPEG => [
        'load' => 'imagecreatefromjpeg',
        'save' => 'imagejpeg',
        'quality' => 100
    ],
    IMAGETYPE_PNG => [
        'load' => 'imagecreatefrompng',
        'save' => 'imagepng',
        'quality' => -1
    ],
    IMAGETYPE_SWF => null,
    IMAGETYPE_PSD => null,
    IMAGETYPE_BMP => null,
    IMAGETYPE_TIFF_II  => null,
    IMAGETYPE_TIFF_MM  => null,
    IMAGETYPE_JPC => null,
    IMAGETYPE_JP2 => null,
    IMAGETYPE_JPX => null,
    IMAGETYPE_JB2 => null,
    IMAGETYPE_SWC => null,
    IMAGETYPE_IFF => null,
    IMAGETYPE_WBMP => null,
    IMAGETYPE_XBM => null,
    IMAGETYPE_ICO => null,
    IMAGETYPE_WEBP => [
        'load' => 'imagecreatefromwebp',
        'save' => 'imagewebp',
        'quality' => 100
    ]
];

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
        "image/jpg",
        "image/pjpeg",
        "image/bmp",
        "image/png"
        //"image/heic"
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
        if ($this->constraints($file)) {
            if ($file["error"] > 0) {
                // throw new Exception("Return Error Code: : ".$file["error"]."<br />");
                return;
            } else {
                $info = pathinfo($file["name"]);
                $this->visual_name = $visual_name;
                $this->extension = $info["extension"];
                $namefile = ($target_name == null) ? $file["name"] : $target_name;
                $this->name = $namefile . "." . $this->extension;
                $this->type = $file["type"];
                $this->size = $file["size"] / 1024;
                $this->tmp_name = $file["tmp_name"];
                $this->path = $destination_folder . $this->name;
                $this->preview_path = $destination_folder . "" . $namefile . "_s" . "." . $this->extension;

                // if ($exitifexist && file_exists($destination_folder.$file["name"])){
                // throw new Exception("File already exist.");
                // }
                // else{
                if (move_uploaded_file($file["tmp_name"], $destination_folder . $this->name))
                    chmod($destination_folder . $this->name, 0755);
                else
                    throw new Exception("Can't MoveUploaded file: " . $this->name);
                // }
                list($width, $height, $type, $attr) = getimagesize($this->path);
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
        global $MAX_UPLOAD_IMAGE_SIZE, $MAX_UPLOAD_PDF_SIZE, $MAX_UPLOAD_NAMEFILE_LENGTH, $MAX_UPLOAD_GENERALFILE_SIZE, $mime_types;
        if (empty($file) || empty($file["name"]) || empty($file["type"])) {
            Logger::write("Unknown file name or file type.", Log_Level::WARNING);
            throw new Exception("Unknown file name or file type.");
        }

        if (strlen($file["name"]) >= Config::get("max_upload_filename_length")) { // # FILE NAME TOO LONG
            Logger::write("File Name too long. Rename it and repeat upload.", Log_Level::WARNING);
            throw new Exception("File Name too long. Rename it and repeat upload.");
        }
        if (in_array($file["type"], $this->image_types)) { // IS IMAGE
            if ($file["size"] > Config::get("max_upload_image_size")) { // SIZE CHECK
                Logger::write("File too large.", Log_Level::WARNING);
                throw new Exception("File too large.");
            }
            return true;
        } else {
            Logger::write("Unknown file. Type:" . $file["type"] . " Name:" . $file["name"], Log_Level::WARNING);
            throw new Exception("Unknown file. Type:" . $file["type"] . " Name:" . $file["name"]);
        }
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
            case "la": {
                    imagefilter($new_image, IMG_FILTER_CONTRAST, -40);
                    break;
                }
            case "ny": {
                    imagefilter($new_image, IMG_FILTER_GRAYSCALE);
                    break;
                }
            case "sd": {
                    imagefilter($new_image, IMG_FILTER_COLORIZE, 0, 0, 100);
                    break;
                }
            case "sf": {
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

    /**
     * @param $src - a valid file location
     * @param $dest - a valid file target
     * @param $targetWidth - desired output width
     * @param $targetHeight - desired output height or null
     */
    public static function createThumbnail($src, $dest, $targetWidth, $targetHeight = null)
    {

        // 1. Load the image from the given $src
        // - see if the file actually exists
        // - check if it's of a valid image type
        // - load the image resource

        // get the type of the image
        // we need the type to determine the correct loader
        $type = exif_imagetype($src);

        // if no valid type or no handler found -> exit
        if (!$type || !IMAGE_HANDLERS[$type]) {
            return null;
        }

        // load the image with the correct loader
        $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);

        // no image found at supplied location -> exit
        if (!$image) {
            return null;
        }

        // 2. Create a thumbnail and resize the loaded $image
        // - get the image dimensions
        // - define the output size appropriately
        // - create a thumbnail based on that size
        // - set alpha transparency for GIFs and PNGs
        // - draw the final thumbnail

        // get original image width and height
        $width = imagesx($image);
        $height = imagesy($image);

        // maintain aspect ratio when no height set
        if ($targetHeight == null) {

            // get width to height ratio
            $ratio = $width / $height;

            // if is portrait
            // use ratio to scale height to fit in square
            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            }
            // if is landscape
            // use ratio to scale width to fit in square
            else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }

        // create duplicate image based on calculated target size
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        // set transparency options for GIFs and PNGs
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

            // make image transparent
            imagecolortransparent(
                $thumbnail,
                imagecolorallocate($thumbnail, 0, 0, 0)
            );

            // additional settings for PNGs
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }

        // copy entire source image to duplicate image and resize
        imagecopyresampled(
            $thumbnail,
            $image,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height
        );
        // 3. Save the $thumbnail to disk
        // - call the correct save method
        // - set the correct quality level

        // save the duplicate version of the image to disk
        return call_user_func(
            IMAGE_HANDLERS[$type]['save'],
            $thumbnail,
            $dest,
            IMAGE_HANDLERS[$type]['quality']
        );
    }

    public static function copy($src, $dest)
    {
        $ext = pathinfo($dest, PATHINFO_EXTENSION);
        if(empty(IMAGETYPE_EXTENSION[$ext])) {
            return null;
        }
        $type = exif_imagetype($src);
        if (!$type || !IMAGE_HANDLERS[$type]) {
            return null;
        }
        $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);
        if (!$image) {
            return null;
        }
        $type_dest = IMAGETYPE_EXTENSION[$ext]; 
        return call_user_func(
            IMAGE_HANDLERS[$type_dest]['save'],
            $image,
            $dest,
            IMAGE_HANDLERS[$type_dest]['quality']
        );
    }
}
