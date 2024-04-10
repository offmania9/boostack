<?php
namespace Core\Models\Upload;
use Core\Models\Config;
use Core\Models\Log\Log_Level;
use Core\Models\Log\Logger;
/**
 * Boostack: Upload_Image.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 6.0
 */

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
        "image/jpg",
        "image/pjpeg",
        "image/bmp",
        "image/png"
        //"image/heic"
    );

    private $PNG_compression = 1;

    /**
     * Constructor for the Upload_Image class.
     *
     * @param array $file The $_FILES array representing the uploaded file.
     * @param string $destination_folder The destination folder for the uploaded image.
     * @param bool $exitifexist Whether to exit if the file already exists.
     * @param string|null $target_name The target name for the uploaded image.
     * @param string|null $visual_name The visual name for the uploaded image.
     * @param int|null $resize The resize option for the image.
     * @param int|null $preview_size The preview size option for the image.
     * @param int|null $filter The filter option for the image.
     * @throws \Exception If an error occurs during image upload.
     */
    public function __construct($file, $destination_folder, $exitifexist = true, $target_name = NULL, $visual_name = NULL, $resize = NULL, $preview_size = NULL, $filter = NULL)
    {
        if ($this->constraints($file)) {
            if ($file["error"] > 0) {
                // throw new \Exception("Return Error Code: : ".$file["error"]."<br />");
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
                // throw new \Exception("File already exist.");
                // }
                // else{
                if (move_uploaded_file($file["tmp_name"], $destination_folder . $this->name)) {
                    chmod($destination_folder . $this->name, 0755);
                } else {
                    throw new \Exception("Can't move uploaded file: " . $this->name);
                }
                // }
                list($width, $height, $type, $attr) = getimagesize($this->path);
                $this->height = $height;
                $this->width = $width;

                if ($resize !== NULL) {
                    if ($this->width >= $this->height) {
                        if ($this->width >= $resize) {
                            $this->resizeToWidth($resize, $filter);
                        } else {
                            $this->resizeToWidth($this->width, $filter);
                        }
                    } else {
                        if ($this->height >= 1237) {
                            $this->resizeToHeight(1237, $filter);
                        } else {
                            $this->resizeToHeight($this->height, $filter);
                        }
                    }
                }
                if ($preview_size !== NULL) {
                    $this->previewResizeToWidth($preview_size[0], $filter);
                }
            }
        }
    }

    /**
     * Check if the uploaded image meets the constraints.
     *
     * @param array $file The $_FILES array representing the uploaded image.
     * @return bool Returns true if the image meets the constraints, otherwise throws an \Exception.
     * @throws \Exception If the image does not meet the constraints.
     */
    public function constraints($file)
    {
        if (empty($file) || empty($file["name"]) || empty($file["type"])) {
            Logger::write("Unknown file name or file type.", Log_Level::WARNING);
            throw new \Exception("Unknown file name or file type.");
        }

        if (strlen($file["name"]) >= Config::get("max_upload_filename_length")) { // # FILE NAME TOO LONG
            Logger::write("File Name too long. Rename it and repeat upload.", Log_Level::WARNING);
            throw new \Exception("File Name too long. Rename it and repeat upload.");
        }
        if (in_array($file["type"], $this->image_types)) { // IS IMAGE
            if ($file["size"] > Config::get("max_upload_image_size")) { // SIZE CHECK
                Logger::write("File too large.", Log_Level::WARNING);
                throw new \Exception("File too large.");
            }
            return true;
        } else {
            Logger::write("Unknown file. Type:" . $file["type"] . " Name:" . $file["name"], Log_Level::WARNING);
            throw new \Exception("Unknown file. Type:" . $file["type"] . " Name:" . $file["name"]);
        }
    }


    /**
     * Removes the file from the filesystem.
     *
     * @return bool Returns true if the file is successfully removed, otherwise false.
     */
    public function remove()
    {
        return unlink($this->path);
    }

    /**
     * Magic method to retrieve inaccessible properties.
     *
     * @param string $property_name The name of the property to retrieve.
     * @return mixed|null Returns the value of the property if it exists, otherwise null.
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
     * Magic method to set inaccessible properties.
     *
     * @param string $property_name The name of the property to set.
     * @param mixed $val The value to set.
     */
    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }

    /**
     * Resizes the image to a specified height.
     *
     * @param int $height The height to resize the image to.
     * @param mixed|null $filter The filter to use for resizing.
     */
    function resizeToHeight($height, $filter = NULL)
    {
        $ratio = $height / $this->height;
        $width = $this->width * $ratio;
        $this->resize($width, $height, $filter);
    }

    /**
     * Resizes the image to a specified width.
     *
     * @param int $width The width to resize the image to.
     * @param mixed|null $filter The filter to use for resizing.
     */
    function resizeToWidth($width, $filter = NULL)
    {
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        $this->resize($width, $height, $filter);
    }

    /**
     * Resizes the image to a specified width for preview.
     *
     * @param int $width The width to resize the image to.
     * @param mixed $filter The filter to apply during resizing.
     */
    function previewResizeToWidth($width, $filter)
    {
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        $this->previewResize($width, $height, $filter);
    }

    /**
     * Scales the image by a specified percentage.
     *
     * @param int $scale The percentage by which to scale the image.
     */
    function scale($scale)
    {
        $width = $this->width * $scale / 100;
        $height = $this->height * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * Resizes the image to the specified width and height.
     *
     * @param int $width The target width of the resized image.
     * @param int $height The target height of the resized image.
     * @param string|null $filter The filter to apply to the resized image (optional).
     * @return void
     */
    function resize($width, $height, $filter = NULL)
    {
        // Create a new true color image resource with the specified dimensions
        $new_image = imagecreatetruecolor($width, $height);

        // Load the source image based on its type
        switch ($this->type) {
            case "image/jpeg":
            case "image/pjpeg":
                $this->source = imagecreatefromjpeg($this->path);
                break;
            case "image/gif":
                $this->source = imagecreatefromgif($this->path);
                break;
            case "image/bmp":
                $this->source = imagecreatefromwbmp($this->path);
                break;
            case "image/png":
                $this->source = imagecreatefrompng($this->path);
                break;
        }

        // Copy and resample the source image onto the new image resource
        imagecopyresampled($new_image, $this->source, 0, 0, 0, 0, $width, $height, $this->width, $this->height);

        // Remove the original image file
        $this->remove();

        // Apply the specified filter to the resized image
        switch ($filter) {
            case "la":
                imagefilter($new_image, IMG_FILTER_CONTRAST, -40);
                break;
            case "ny":
                imagefilter($new_image, IMG_FILTER_GRAYSCALE);
                break;
            case "sd":
                imagefilter($new_image, IMG_FILTER_COLORIZE, 0, 0, 100);
                break;
            case "sf":
                imagefilter($new_image, IMG_FILTER_COLORIZE, 0, 100, 0);
                break;
        }

        // Save the resized image based on its type
        switch ($this->type) {
            case "image/jpeg":
            case "image/pjpeg":
                imagejpeg($new_image, $this->path, 100);
                break;
            case "image/gif":
                imagegif($new_image, $this->path, 100);
                break;
            case "image/bmp":
                imagewbmp($new_image, $this->path, 100);
                break;
            case "image/png":
                imagepng($new_image, $this->path, $this->PNG_compression);
                break;
        }

        // Update the path property
        $this->path = $this->path;
    }


    /**
     * Resizes the image to the specified width and height for preview purposes.
     *
     * @param int $width The target width of the preview image.
     * @param int $height The target height of the preview image.
     * @param string|null $filter The filter to apply to the preview image (optional).
     * @return void
     */
    function previewResize($width, $height, $filter = NULL)
    {
        // Create a new true color image resource with the specified dimensions
        $new_image = imagecreatetruecolor($width, $height);

        // Load the source image based on its type
        switch ($this->type) {
            case "image/jpeg":
            case "image/pjpeg":
                $this->source = imagecreatefromjpeg($this->path);
                break;
            case "image/gif":
                $this->source = imagecreatefromgif($this->path);
                break;
            case "image/bmp":
                $this->source = imagecreatefromwbmp($this->path);
                break;
            case "image/png":
                $this->source = imagecreatefrompng($this->path);
                break;
        }

        // Copy and resample the source image onto the new image resource
        imagecopyresampled($new_image, $this->source, 0, 0, 0, 0, $width, $height, $this->width, $this->height);

        // Save the preview image based on its type
        switch ($this->type) {
            case "image/jpeg":
            case "image/pjpeg":
                imagejpeg($new_image, $this->preview_path, 100);
                break;
            case "image/gif":
                imagegif($new_image, $this->preview_path, 100);
                break;
            case "image/bmp":
                imagewbmp($new_image, $this->preview_path, 100);
                break;
            case "image/png":
                imagepng($new_image, $this->preview_path, $this->PNG_compression);
                break;
        }

        // Update the preview_path property
        $this->preview_path = $this->preview_path;
    }


    /**
     * Creates a thumbnail from the given image source and saves it to the destination.
     *
     * @param string $src The source file location.
     * @param string $dest The destination file location.
     * @param int $targetWidth The desired output width.
     * @param int|null $targetHeight The desired output height or null.
     * @return bool|null True on success, null on failure.
     */
    public static function createThumbnail($src, $dest, $targetWidth, $targetHeight = null)
    {
        // Check if the source file exists
        if (!file_exists($src)) {
            return null;
        }

        // Get the image type
        $type = exif_imagetype($src);

        // Check if a valid image type and handler exist
        if (!$type || !IMAGE_HANDLERS[$type]) {
            return null;
        }

        // Load the image with the appropriate handler
        $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);

        // Check if the image was loaded successfully
        if (!$image) {
            return null;
        }

        // Get the original image dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // Calculate the height if it's not specified
        if ($targetHeight === null) {
            $ratio = $width / $height;
            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            } else {
                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }

        // Create a new image resource for the thumbnail
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        // Set transparency options for GIFs and PNGs
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
            imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }

        // Copy and resize the original image to the thumbnail
        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        // Save the thumbnail to disk
        return call_user_func(IMAGE_HANDLERS[$type]['save'], $thumbnail, $dest, IMAGE_HANDLERS[$type]['quality']);
    }

    /**
     * Copies an image file to a new location and converts it to a different format if necessary.
     *
     * @param string $src The source file location.
     * @param string $dest The destination file location.
     * @return bool|null True on success, null on failure.
     */
    public static function copy($src, $dest)
    {
        // Get the extension of the destination file
        $ext = pathinfo($dest, PATHINFO_EXTENSION);

        // Check if the extension is supported
        if (empty(IMAGETYPE_EXTENSION[$ext])) {
            return null;
        }

        // Get the image type of the source file
        $type = exif_imagetype($src);

        // Check if a valid image type and handler exist
        if (!$type || !IMAGE_HANDLERS[$type]) {
            return null;
        }

        // Load the image with the appropriate handler
        $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);

        // Check if the image was loaded successfully
        if (!$image) {
            return null;
        }

        // Get the image type of the destination file
        $type_dest = IMAGETYPE_EXTENSION[$ext];

        // Save the image to the destination with the appropriate handler
        return call_user_func(IMAGE_HANDLERS[$type_dest]['save'], $image, $dest, IMAGE_HANDLERS[$type_dest]['quality']);
    }
}
